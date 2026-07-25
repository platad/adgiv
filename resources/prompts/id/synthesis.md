Berikut adalah hasil analisis potongan (chunks) percakapan bimbingan akademik yang terfragmentasi.
Tugas Anda adalah menggabungkannya secara kronologis, menghapus tumpang tindih (deduplicate) pada bagian yang bertumpukan secara semantik, menghitung ulang metrik global secara matematis dan presisi, dan menyusun laporan akhir C-CDA yang komprehensif.

DATA POTONGAN CHUNKS:
{CHUNKS_JSON}

ATURAN SINTESIS MUTLAK:
1. JANGAN PERNAH MENYINGKAT ATAU MERANGKUM TRANSKRIPSI: Anda wajib menyertakan SETIAP baris transkripsi dari seluruh potongan chunk yang diberikan secara lengkap dari awal hingga akhir audio. Jangan ada satu kalimat pun yang hilang!
2. IDENTIFIKASI PEMBICARA: Karena input data berlabel "Unknown", Anda WAJIB menganalisis konteks percakapan untuk menentukan siapa pembicaranya dan ubah label 'speaker' menjadi "Dosen" atau "Mahasiswa" dengan tepat. (Dosen biasanya memberi saran/koreksi/bertanya, Mahasiswa biasanya menjawab/meminta arahan).
3. SISIPKAN PENANDA INTONASI: Anda WAJIB menyisipkan tag penanda unik seperti [MARKER_1], [MARKER_2], dst., serta tag [PAUSE] ke dalam teks transkripsi ('text_html') tepat di sebelah kata yang memiliki intonasi menonjol atau jeda. Input asli berupa teks mentah tanpa tag, jadi Anda harus membuatnya! Tebalkan (bold) frasa pemberian saran dengan <b>...</b>.
4. KONSISTENSI MARKER: Pastikan setiap tag [MARKER_x] yang tercantum di dalam 'text_html' memiliki objek padanannya di dalam array 'intonation_markers' untuk baris tersebut dengan id yang persis sama.
5. BUAT DETAIL ANALISIS MENDALAM (COMPREHENSIVE ANALYSIS): Anda WAJIB memberikan penjelasan yang komprehensif, akademis, dan mendalam pada 'agent_insight', 'advice_relation', 'reason', dan 'relation' (minimal 20-30 kata per item). Jelaskan secara tajam aspek sosiolinguistik, dinamika relasi kekuasaan, serta implikasi akademik dari ujaran dosen dan mahasiswa.

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
