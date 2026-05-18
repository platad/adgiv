# Rencana Overhaul: Pipeline Segment-Level Discourse Analysis BIMA

## 🎯 Tujuan

Mengubah paradigma analisis dari **whole-transcript classification** (satu label untuk seluruh percakapan) menjadi **segment-level discourse analysis** (setiap blok waktu/topik dianalisis terpisah) dengan **root cause tracking** yang ditampilkan real-time di UI.

---

## 🏗️ Arsitektur Baru

```mermaid
flowchart TD
    A[🎤 Audio Input] --> B[Step 0: Transkripsi<br>Whisper API + timestamps]

    B --> C{Step 1: Segmentasi<br>segment_splitter Agent}

    C --> D1[Segmen 1<br>00:00-00:17]
    C --> D2[Segmen 2<br>00:18-00:45]
    C --> D3[Segmen N<br>...]

    D1 --> E1[Step 2: Analisis per Segmen<br>segment_classifier Agent]
    D2 --> E2[Step 2: Analisis per Segmen<br>segment_classifier Agent]
    D3 --> E3[Step 2: Analisis per Segmen<br>segment_classifier Agent]

    E1 --> F1[Output: Dialogue Act | Power ↑↓ | Advice | Intonation | Markers bold?]
    E2 --> F2[Output: Dialogue Act | Power ↑↓ | Advice | Intonation | Markers bold?]
    E3 --> F3[Output: Dialogue Act | Power ↑↓ | Advice | Intonation | Markers bold?]

    F1 --> G[Step 3: BIMA Insights<br>kimi_insights Agent<br>Analisis global + korelasi antar segmen]
    F2 --> G
    F3 --> G

    G --> H[📊 Dashboard Timeline<br>Segmen Cards + Root Cause Log]

    style A fill:#e1f5fe
    style H fill:#c8e6c9
```

---

## 📊 Skema Anotasi per Segmen

Setiap segmen akan memiliki field-field berikut:

| Field               | Deskripsi               | Contoh Nilai                                         |
| ------------------- | ----------------------- | ---------------------------------------------------- |
| `speaker`           | Peran pembicara dominan | `dosen`, `mahasiswa`, `campuran`                     |
| `dialogue_act`      | Tindak tutur            | `directive`, `assertive`, `commissive`, `expressive` |
| `power_marker`      | Relasi kuasa            | `↑` (power-over), `↓` (power-under), `↔` (seimbang)  |
| `advice_category`   | Kategori bimbingan      | `Arahan Eksplisit`, `Dukungan Keputusan`, etc.       |
| `intonation`        | Nada bicara             | `Tegas`, `Lembut`, `Tanya`, `Instruksi`              |
| `discourse_markers` | Penanda wacana          | `**bold**` (penekanan), `?` (keraguan), `!` (seruan) |
| `sentiment`         | Sentimen                | `positif`, `negatif`, `netral`                       |
| `topic`             | Topik utama segmen      | `Judul Skripsi`, `Metodologi`, `Revisi`              |

---

## 🔄 Pipeline Baru: 3 Step Utama + Sub-Proses Detail

### Step 0: Transkripsi Bertimestamp

- **Sub-step 0.1**: Upload audio → Whisper API
- **Sub-step 0.2**: Terima response dengan `timestamp_granularities=["segment"]`
- **Sub-step 0.3**: Simpan transkripsi bertimestamp ke `transcript_segments` (hanya text + time)
- **Output**: Array segmen `[{start_time, end_time, text, speaker_label}]`

### Step 1: Segmentasi Cerdas (Topic Boundary Detection)

- **Sub-step 1.1**: Kirim transkripsi utuh ke `segment_splitter` Agent (LLM)
- **Sub-step 1.2**: LLM identifikasi pergeseran topik → pecah menjadi blok logis
- **Sub-step 1.3**: Gabungkan Whisper timestamps dengan segmentasi LLM
- **Sub-step 1.4**: Simpan segmen final ke database
- **Output**: N segmen dengan `{start_time, end_time, topic, text}`

### Step 2: Klasifikasi per Segmen (PARALEL)

Untuk SETIAP segmen, jalankan secara paralel:

- **Sub-step 2.1a**: Dialogue Act Classification
- **Sub-step 2.1b**: Power Dynamics Detection (↑↓↔)
- **Sub-step 2.1c**: Advice Category Classification
- **Sub-step 2.1d**: Intonation & Sentiment Analysis
- **Sub-step 2.1e**: Discourse Markers Extraction (bold, ?, !)
- **Sub-step 2.2**: Simpan hasil per segmen ke `transcript_segments`

### Step 3: BIMA Insights Global

- **Sub-step 3.1**: Kirim semua segmen + hasil klasifikasi ke `kimi_insights` Agent
- **Sub-step 3.2**: LLM analisis korelasi antar segmen, tren percakapan
- **Sub-step 3.3**: Generate summary, aim, suggestions
- **Output**: Insights global + korelasi segmen

---

## 🗄️ Perubahan Database

### Tabel Baru: `transcript_segments`

```sql
CREATE TABLE transcript_segments (
    id UUID PRIMARY KEY,
    chat_session_id UUID REFERENCES chat_sessions(id) ON DELETE CASCADE,
    segment_index INTEGER NOT NULL,
    speaker VARCHAR(50) DEFAULT 'unknown',
    start_time DECIMAL(8,2) NOT NULL,  -- dalam detik
    end_time DECIMAL(8,2) NOT NULL,
    text LONGTEXT NOT NULL,
    topic VARCHAR(255),
    dialogue_act VARCHAR(100),
    power_marker VARCHAR(10),           -- ↑, ↓, ↔
    advice_category VARCHAR(100),
    intonation VARCHAR(100),
    discourse_markers JSON,             -- {"bold": [...], "question": [...], "exclamation": [...]}
    sentiment VARCHAR(20),
    raw_json JSON,                      -- response mentah dari LLM
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel Baru: `segment_analysis_logs` (Root Cause Tracking)

```sql
CREATE TABLE segment_analysis_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    chat_session_id UUID REFERENCES chat_sessions(id) ON DELETE CASCADE,
    segment_id UUID REFERENCES transcript_segments(id) ON DELETE SET NULL,
    step_name VARCHAR(100) NOT NULL,     -- e.g., 'segmentasi', 'klasifikasi', 'insights'
    sub_step VARCHAR(100) NOT NULL,      -- e.g., 'dialogue_act', 'power_detection'
    status ENUM('pending', 'running', 'done', 'failed') DEFAULT 'pending',
    process_detail TEXT,                 -- deskripsi yang sedang diproses (untuk UI)
    input_summary TEXT,                  -- ringkasan input (token count, dll)
    result_summary TEXT,                 -- ringkasan hasil
    result_data JSON,                    -- response mentah
    duration_ms INTEGER,                 -- berapa lama proses (ms)
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Kolom Tambahan di `chat_sessions`

```sql
ALTER TABLE chat_sessions ADD COLUMN audio_duration DECIMAL(8,2) DEFAULT 0;
ALTER TABLE chat_sessions ADD COLUMN segment_count INTEGER DEFAULT 0;
ALTER TABLE chat_sessions ADD COLUMN analysis_status VARCHAR(50) DEFAULT 'idle';
ALTER TABLE chat_sessions ADD COLUMN workflow_state JSON;
ALTER TABLE chat_sessions ADD COLUMN raw_whisper_response JSON;
```

---

## 🧩 Komponen yang Dibuat/Diubah

| #   | File                                                                  | Aksi        | Deskripsi                                                     |
| --- | --------------------------------------------------------------------- | ----------- | ------------------------------------------------------------- |
| 1   | `database/migrations/...create_transcript_segments_table.php`         | **BUAT**    | Migration tabel transcript_segments                           |
| 2   | `database/migrations/...create_segment_analysis_logs_table.php`       | **BUAT**    | Migration tabel segment_analysis_logs                         |
| 3   | `database/migrations/...add_segment_columns_to_chat_sessions.php`     | **BUAT**    | Migration tambah kolom                                        |
| 4   | `app/Models/TranscriptSegment.php`                                    | **BUAT**    | Model + relasi                                                |
| 5   | `app/Models/SegmentAnalysisLog.php`                                   | **BUAT**    | Model + relasi                                                |
| 6   | `app/Models/ChatSession.php`                                          | **UPDATE**  | Tambah relasi `segments()`, `segmentAnalysisLogs()`           |
| 7   | `app/Services/TranscriptionService.php`                               | **UPDATE**  | Tambah `timestamp_granularities`, return timestamped response |
| 8   | `database/seeders/AgentPromptSeeder.php`                              | **UPDATE**  | Tambah prompt: `segment_splitter`, `segment_classifier`       |
| 9   | `app/Services/DiscoursePipelineService.php`                           | **BUAT**    | Orkestrasi pipeline segmentasi (core logic)                   |
| 10  | `app/Http/Controllers/ChatController.php`                             | **REWRITE** | Ganti 6 step lama, tambah endpoint SSE                        |
| 11  | `routes/web.php`                                                      | **UPDATE**  | Route baru untuk pipeline + SSE                               |
| 12  | `resources/views/components/chat/workflow-status-dashboard.blade.php` | **REWRITE** | Tampilkan sub-proses detail + root cause                      |
| 13  | `resources/views/components/chat/segment-results-dashboard.blade.php` | **BUAT**    | Timeline card per blok dengan anotasi                         |
| 14  | `resources/views/chat.blade.php`                                      | **UPDATE**  | Integrasi dashboard baru                                      |
| 15  | `resources/views/components/chat/scripts.blade.php`                   | **REWRITE** | Alpine.js untuk pipeline baru + SSE progress                  |
| 16  | `resources/views/components/voice-input.blade.php`                    | **UPDATE**  | Trigger pipeline baru                                         |
| 17  | `app/Services/OpenAiService.php`                                      | **MINOR**   | Tambah method untuk streaming/SSE jika perlu                  |
| 18  | `app/Services/KimiApiService.php`                                     | **FIX**     | Perbaiki bug model di line 44                                 |

---

## 🖥️ Desain UI: Root Cause Tracking

### Workflow Dashboard (Kiri)

Setiap step menampilkan:

1. **Nama Step** (Segmentasi / Klasifikasi / Insights)
2. **Status Icon** (⏳ running / ✅ done / ❌ failed)
3. **Progress Bar** dengan sub-step detail:
    - "Mengirim ke Whisper API..."
    - "Menerima 3 segmen (00:00-00:17, 00:18-00:45, 00:46-01:20)"
    - "Mengklasifikasi Segmen 1/3: Dialogue Act..."
    - "Mengklasifikasi Segmen 1/3: Power Detection..."
    - "Selesai: Arahan Eksplisit | ↑ | Tegas"
4. **Setiap sub-step** adalah log entry dari `segment_analysis_logs` yang di-broadcast via SSE

### Segment Results Dashboard (Kanan)

Timeline vertikal dengan card per segmen:

```
┌─────────────────────────────────────────┐
│ ⏱ 00:00 - 00:17  │ Topik: Judul Skripsi │
├─────────────────────────────────────────┤
│ "Coba judulnya diganti jadi..."         │
│                                         │
│ 🏷 Dialogue Act: Directive             │
│ ⚡ Power: ↑ (Power-over)               │
│ 🎓 Advice: Arahan Eksplisit            │
│ 🔊 Intonasi: Tegas                     │
│ 📝 Markers: **ganti** ?                │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ ⏱ 00:18 - 00:45  │ Topik: Metodologi   │
├─────────────────────────────────────────┤
│ "Saya bingung pakai metode apa..."      │
│                                         │
│ 🏷 Dialogue Act: Expressive            │
│ ⚡ Power: ↓ (Power-under)              │
│ 🎓 Advice: Petunjuk Kontekstual        │
│ 🔊 Intonasi: Ragu-ragu                 │
│ 📝 Markers: ?bingung?                  │
└─────────────────────────────────────────┘
```

---

## 🔌 SSE (Server-Sent Events) untuk Real-Time Progress

Endpoint: `GET /chat/analyse/stream?session_id=xxx`

Server mengirim event stream:

```
event: progress
data: {"step": "segmentasi", "sub_step": "whisper_api", "status": "running", "detail": "Mengirim audio ke Whisper API...", "progress": 10}

event: progress
data: {"step": "segmentasi", "sub_step": "whisper_api", "status": "done", "detail": "Transkripsi selesai: 3 segmen terdeteksi", "progress": 30}

event: progress
data: {"step": "klasifikasi", "sub_step": "segmen_1_dialogue_act", "status": "running", "detail": "Mengklasifikasi Segmen 1/3: Dialogue Act...", "progress": 45}

event: segment_done
data: {"segment_index": 0, "start_time": 0, "end_time": 17, "dialogue_act": "directive", "power_marker": "↑", ...}

event: progress
data: {"step": "klasifikasi", "sub_step": "segmen_2_dialogue_act", "status": "running", "detail": "Mengklasifikasi Segmen 2/3: Dialogue Act...", "progress": 65}

event: segment_done
data: {"segment_index": 1, ...}

event: complete
data: {"segments": [...], "insights": {...}}
```

---

## 🚀 Urutan Eksekusi

1. **Database**: Buat 3 migration baru → migrate fresh
2. **Models**: TranscriptSegment, SegmentAnalysisLog, update ChatSession
3. **Services**: Update TranscriptionService, buat DiscoursePipelineService
4. **Seeders**: Update AgentPromptSeeder dengan prompt baru
5. **Controller**: Rewrite ChatController (endpoint SSE + pipeline)
6. **Routes**: Tambah route baru
7. **Views (Backend)**: Buat segment-results-dashboard, rewrite workflow-status-dashboard
8. **Views (Layout)**: Update chat.blade.php
9. **JavaScript**: Rewrite scripts.blade.php (Alpine.js SSE client)
10. **Cleanup**: Nonaktifkan endpoint step2-step6 lama

---

## ⚠️ Catatan Penting

1. **Biaya LLM**: Segmentasi + klasifikasi per segmen bisa 5-10x lebih mahal. Satu sesi 5 menit (~10 segmen × 5 analisis = 50 LLM calls). Gunakan `gpt-4o-mini` dengan `response_format: json_object`.

2. **Paralelisasi**: Sub-step klasifikasi per segmen (dialogue_act, power, advice, intonation) bisa dijalankan paralel dengan `curl_multi` atau `GuzzleHttp\Pool`.

3. **SSE di Laravel**: Gunakan `Symfony\Component\HttpFoundation\StreamedResponse` untuk SSE endpoint.

4. **Fallback**: Jika segment_splitter gagal, fallback ke segmentasi berbasis timestamp Whisper saja (setiap 15-30 detik sebagai satu segmen).

5. **Retensi**: Tabel `segment_analysis_logs` bisa membesar cepat. Tambahkan fitur prune log > 30 hari di scheduler.
