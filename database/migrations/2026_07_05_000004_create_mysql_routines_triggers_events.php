<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_chunk_insert');
        DB::unprepared('
            CREATE TRIGGER trg_after_chunk_insert
            AFTER INSERT ON analysis_chunks
            FOR EACH ROW
            BEGIN
                UPDATE analyses
                SET total_chunks = (
                    SELECT COUNT(*) FROM analysis_chunks
                    WHERE analysis_id = NEW.analysis_id
                )
                WHERE id = NEW.analysis_id;
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_chunk_status_update');
        DB::unprepared('
            CREATE TRIGGER trg_after_chunk_status_update
            AFTER UPDATE ON analysis_chunks
            FOR EACH ROW
            BEGIN
                IF OLD.status != \'done\' AND NEW.status = \'done\' THEN
                    UPDATE analyses
                    SET processed_chunks = (
                        SELECT COUNT(*) FROM analysis_chunks
                        WHERE analysis_id = NEW.analysis_id AND status = \'done\'
                    )
                    WHERE id = NEW.analysis_id;

                    IF (SELECT COUNT(*) FROM analysis_chunks
                        WHERE analysis_id = NEW.analysis_id AND status NOT IN (\'done\', \'skipped\')) = 0 THEN
                        UPDATE analyses
                        SET status = \'synthesizing\'
                        WHERE id = NEW.analysis_id AND status = \'processing\';
                    END IF;
                END IF;

                IF OLD.status != \'failed\' AND NEW.status = \'failed\' THEN
                    UPDATE analyses
                    SET status = \'partial_failure\'
                    WHERE id = NEW.analysis_id AND status = \'processing\';
                END IF;
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_analysis_delete');
        DB::unprepared('
            CREATE TRIGGER trg_before_analysis_delete
            BEFORE DELETE ON analyses
            FOR EACH ROW
            BEGIN
                IF OLD.audio_path IS NOT NULL AND OLD.audio_path != \'\' THEN
                    INSERT INTO pending_file_deletions (file_path, created_at)
                    VALUES (OLD.audio_path, NOW());
                END IF;

                INSERT INTO pending_file_deletions (file_path, created_at)
                SELECT chunk_path, NOW()
                FROM analysis_chunks
                WHERE analysis_id = OLD.id AND chunk_path IS NOT NULL AND chunk_path != \'\';
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_analysis_log_insert');
        DB::unprepared('
            CREATE TRIGGER trg_after_analysis_log_insert
            AFTER INSERT ON analysis_logs
            FOR EACH ROW
            BEGIN
                UPDATE analyses SET updated_at = NOW()
                WHERE id = NEW.analysis_id;
            END
        ');

        DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_progress_percent');
        DB::unprepared('
            CREATE FUNCTION fn_calculate_progress_percent(p_analysis_id BIGINT UNSIGNED)
            RETURNS TINYINT UNSIGNED
            DETERMINISTIC READS SQL DATA
            BEGIN
                DECLARE v_total SMALLINT UNSIGNED DEFAULT 0;
                DECLARE v_done  SMALLINT UNSIGNED DEFAULT 0;

                SELECT total_chunks, processed_chunks
                INTO v_total, v_done
                FROM analyses WHERE id = p_analysis_id;

                IF v_total = 0 THEN RETURN 0; END IF;
                RETURN ROUND((v_done / v_total) * 100);
            END
        ');

        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_analysis_total_tokens');
        DB::unprepared('
            CREATE FUNCTION fn_get_analysis_total_tokens(p_analysis_id BIGINT UNSIGNED)
            RETURNS INT UNSIGNED
            DETERMINISTIC READS SQL DATA
            BEGIN
                RETURN (
                    SELECT COALESCE(SUM(
                        CAST(JSON_EXTRACT(detail, \'$.tokens_used\') AS UNSIGNED)
                    ), 0)
                    FROM analysis_logs
                    WHERE analysis_id = p_analysis_id
                      AND event = \'chunk_done\'
                );
            END
        ');

        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_analysis_duration_ms');
        DB::unprepared('
            CREATE FUNCTION fn_get_analysis_duration_ms(p_analysis_id BIGINT UNSIGNED)
            RETURNS BIGINT UNSIGNED
            DETERMINISTIC READS SQL DATA
            BEGIN
                RETURN (
                    SELECT COALESCE(TIMESTAMPDIFF(
                        SECOND,
                        MIN(created_at),
                        MAX(created_at)
                    ) * 1000, 0)
                    FROM analysis_logs
                    WHERE analysis_id = p_analysis_id
                );
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reset_failed_chunks');
        DB::unprepared('
            CREATE PROCEDURE sp_reset_failed_chunks(IN p_analysis_id BIGINT UNSIGNED)
            BEGIN
                UPDATE analysis_chunks
                SET status = \'pending\',
                    started_at = NULL,
                    completed_at = NULL,
                    error_message = NULL,
                    retry_count = retry_count + 1
                WHERE analysis_id = p_analysis_id
                  AND status IN (\'failed\', \'running\');

                UPDATE analyses
                SET status = \'processing\',
                    updated_at = NOW()
                WHERE id = p_analysis_id
                  AND status IN (\'partial_failure\', \'failed\');

                INSERT INTO analysis_logs (analysis_id, event, status, message, created_at)
                VALUES (p_analysis_id, \'resumed\', \'info\', \'Analisis dilanjutkan dari titik kegagalan.\', NOW());
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_analysis_progress');
        DB::unprepared('
            CREATE PROCEDURE sp_get_analysis_progress(IN p_slug VARCHAR(20))
            BEGIN
                SELECT
                    a.id, a.slug, a.title, a.status, a.locale,
                    a.total_chunks, a.processed_chunks,
                    a.model_used, a.synthesis_model,
                    a.created_at, a.updated_at,
                    fn_calculate_progress_percent(a.id) AS progress_percent,
                    fn_get_analysis_total_tokens(a.id)  AS total_tokens_used,
                    fn_get_analysis_duration_ms(a.id)   AS total_duration_ms
                FROM analyses a
                WHERE a.slug = p_slug;

                SELECT
                    ac.chunk_index, ac.status, ac.model_used,
                    ac.duration_ms, ac.started_at, ac.completed_at,
                    ac.error_message, ac.retry_count,
                    JSON_EXTRACT(ac.result_data, \'$.transcription\') IS NOT NULL AS has_result
                FROM analysis_chunks ac
                INNER JOIN analyses a ON ac.analysis_id = a.id
                WHERE a.slug = p_slug
                ORDER BY ac.chunk_index ASC;
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cleanup_orphaned_analyses');
        DB::unprepared('
            CREATE PROCEDURE sp_cleanup_orphaned_analyses()
            BEGIN
                UPDATE analyses
                SET status = \'failed\', updated_at = NOW()
                WHERE status IN (\'processing\', \'synthesizing\', \'uploaded\')
                  AND updated_at < DATE_SUB(NOW(), INTERVAL 2 HOUR);

                UPDATE analysis_chunks ac
                INNER JOIN analyses a ON ac.analysis_id = a.id
                SET ac.status = \'failed\',
                    ac.error_message = \'Dihentikan otomatis: timeout >2 jam.\'
                WHERE a.status = \'failed\'
                  AND ac.status = \'running\';

                INSERT INTO analysis_logs (analysis_id, event, status, message, created_at)
                SELECT id, \'auto_timeout\', \'warning\',
                       \'Analisis dihentikan otomatis (tidak aktif >2 jam).\',
                       NOW()
                FROM analyses
                WHERE status = \'failed\'
                  AND updated_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);
            END
        ');

        DB::unprepared('SET GLOBAL event_scheduler = ON');

        DB::unprepared('DROP EVENT IF EXISTS evt_cleanup_stale_analyses');
        DB::unprepared('
            CREATE EVENT evt_cleanup_stale_analyses
            ON SCHEDULE EVERY 30 MINUTE
            STARTS NOW()
            DO
                CALL sp_cleanup_orphaned_analyses()
        ');

        DB::unprepared('DROP EVENT IF EXISTS evt_mark_pending_deletions');
        DB::unprepared('
            CREATE EVENT evt_mark_pending_deletions
            ON SCHEDULE EVERY 15 MINUTE
            STARTS NOW()
            DO
            BEGIN
                INSERT IGNORE INTO pending_file_deletions (file_path, created_at)
                SELECT ac.chunk_path, NOW()
                FROM analysis_chunks ac
                INNER JOIN analyses a ON ac.analysis_id = a.id
                WHERE a.status = \'completed\'
                  AND ac.chunk_path IS NOT NULL
                  AND ac.chunk_path != \'\'
                  AND ac.completed_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
                  AND NOT EXISTS (
                      SELECT 1 FROM pending_file_deletions
                      WHERE file_path = ac.chunk_path
                  );
            END
        ');

        DB::unprepared('DROP EVENT IF EXISTS evt_archive_old_logs');
        DB::unprepared('
            CREATE EVENT evt_archive_old_logs
            ON SCHEDULE EVERY 1 DAY
            STARTS (DATE(NOW()) + INTERVAL 1 DAY + INTERVAL 2 HOUR)
            DO
            BEGIN
                INSERT INTO analysis_logs_archive
                    (id, analysis_id, event, status, message, detail, duration_ms, created_at, archived_at)
                SELECT id, analysis_id, event, status, message, detail, duration_ms, created_at, NOW()
                FROM analysis_logs
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

                DELETE FROM analysis_logs
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
            END
        ');

        DB::unprepared('DROP EVENT IF EXISTS evt_daily_stats_snapshot');
        DB::unprepared('
            CREATE EVENT evt_daily_stats_snapshot
            ON SCHEDULE EVERY 1 DAY
            STARTS (DATE(NOW()) + INTERVAL 1 DAY + INTERVAL 5 MINUTE)
            DO
            BEGIN
                INSERT INTO daily_analysis_stats
                    (snapshot_date, total_analyses, completed_analyses,
                     failed_analyses, total_tokens_used, avg_duration_ms, created_at)
                SELECT
                    CURDATE() - INTERVAL 1 DAY,
                    COUNT(*),
                    SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END),
                    SUM(CASE WHEN status = \'failed\' THEN 1 ELSE 0 END),
                    COALESCE(SUM(fn_get_analysis_total_tokens(id)), 0),
                    COALESCE(AVG(fn_get_analysis_duration_ms(id)), 0),
                    NOW()
                FROM analyses
                WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY
                ON DUPLICATE KEY UPDATE
                    total_analyses = VALUES(total_analyses),
                    completed_analyses = VALUES(completed_analyses),
                    failed_analyses = VALUES(failed_analyses),
                    total_tokens_used = VALUES(total_tokens_used),
                    avg_duration_ms = VALUES(avg_duration_ms);
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP EVENT IF EXISTS evt_daily_stats_snapshot');
        DB::unprepared('DROP EVENT IF EXISTS evt_archive_old_logs');
        DB::unprepared('DROP EVENT IF EXISTS evt_mark_pending_deletions');
        DB::unprepared('DROP EVENT IF EXISTS evt_cleanup_stale_analyses');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cleanup_orphaned_analyses');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_analysis_progress');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reset_failed_chunks');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_analysis_duration_ms');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_analysis_total_tokens');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_progress_percent');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_analysis_log_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_analysis_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_chunk_status_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_chunk_insert');
    }
};
