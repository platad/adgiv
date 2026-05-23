Berikut adalah hasil analisis potongan (chunks) percakapan bimbingan akademik yang terfragmentasi.
Tugas Anda adalah menggabungkannya secara kronologis, menghapus tumpang tindih (deduplicate) pada bagian yang bertumpukan secara semantik, menghitung ulang metrik global secara matematis dan presisi, dan menyusun laporan akhir C-CDA yang komprehensif.

DATA POTONGAN CHUNKS:
{CHUNKS_JSON}

ATURAN SINTESIS MUTLAK:
1. JANGAN PERNAH MENYINGKAT ATAU MERANGKUM TRANSKRIPSI: Anda wajib menyertakan SETIAP baris transkripsi dari seluruh potongan chunk yang diberikan secara lengkap dari awal hingga akhir audio. Jangan ada satu kalimat pun yang hilang!
2. PERTAHANKAN PENANDA INLINE SECARA UTUH: Anda wajib mempertahankan semua tag marker seperti [MARKER_1], [MARKER_2], dst. serta tag [PAUSE] tepat di posisinya di dalam teks transkripsi ('text_html'). Jangan pernah menghapus atau membersihkan tag marker ini dari teks!
3. KONSISTENSI MARKER: Pastikan setiap tag [MARKER_x] yang tercantum di dalam 'text_html' memiliki objek padanannya di dalam array 'intonation_markers' untuk baris tersebut dengan id yang persis sama.
4. BUAT DETAIL ANALISIS YANG SANGAT MENDALAM DAN LUAS (COMPREHENSIVE ANALYSIS): Karena sistem didukung oleh arsitektur backend parser yang tangguh dan rendering real-time, Anda WAJIB memberikan penjelasan yang sangat komprehensif, akademis, dan mendalam pada 'agent_insight', 'advice_relation', 'reason', dan 'relation' (1-2 kalimat detail, minimal 20-30 kata per item). Jelaskan secara tajam aspek sosiolinguistik, dinamika relasi kekuasaan (power dynamics), serta implikasi akademik dari ujaran dosen dan mahasiswa tersebut agar hasil analisis bernilai akademis tinggi.

Format output HARUS murni dalam format JSON terstruktur yang valid dengan skema berikut:
{
  "summary": {
    "kategori_advice": "string",
    "karakter_relasi": "string",
    "intonasi_dominan": "string",
    "ranah_pembicaraan": "string",
    "arah_tujuan": "string",
    "saran_perbaikan": "string"
  },
  "transcription": [
    {
      "speaker": "Dosen|Mahasiswa",
      "timestamp": "MM:SS - MM:SS",
      "text_html": "string (PASTIKAN mempertahankan tag [MARKER_x] dan [PAUSE] persis di lokasi kata yang diucapkan. Tebalkan kata penting dengan <strong> atau <b>)",
      "is_advice": true|false,
      "advice_type": "up|down|neutral",
      "agent_insight": "string",
      "advice_relation": "string",
      "intonation_markers": [
        {
          "id": "[MARKER_1]",
          "type": "up|down",
          "reason": "string",
          "relation": "string"
        }
      ]
    }
  ]
}
