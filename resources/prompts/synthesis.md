Berikut adalah hasil analisis potongan (chunks) percakapan bimbingan akademik yang terfragmentasi.
Tugas Anda adalah menggabungkannya secara kronologis, menghapus tumpang tindih (deduplicate) pada bagian yang bertumpukan secara semantik, menghitung ulang metrik global secara matematis dan presisi, dan menyusun laporan akhir C-CDA yang komprehensif.

DATA POTONGAN CHUNKS:
{CHUNKS_JSON}

ATURAN SINTESIS MUTLAK:
1. JANGAN PERNAH MENYINGKAT ATAU MERANGKUM TRANSKRIPSI: Anda wajib menyertakan SETIAP baris transkripsi dari seluruh potongan chunk yang diberikan secara lengkap dari awal hingga akhir audio. Jangan ada satu kalimat pun yang hilang!
2. PERTAHANKAN LABEL INDOBERT: Setiap objek dalam JSON memiliki key "advice_giving" dan "modes_of_interaction" yang TELAH DIBUAT oleh model IndoBERT kami. Anda DILARANG MENGUBAH / MENGHALUSINASI label ini. Anda HANYA mem-parsing ulang label tersebut.
3. PERTAHANKAN PENANDA INLINE SECARA UTUH DAN TAMBAHKAN JIKA KOSONG: Anda wajib meletakkan tag marker seperti [MARKER_1], [MARKER_2], dst. serta tag [PAUSE] di dalam teks transkripsi ('text_html'). Anda WAJIB menambahkan intonasi marker (seperti di koma atau di akhir kalimat) di setiap kalimatnya dan mengisi array 'intonation_markers'.
4. KONSISTENSI MARKER: Pastikan setiap tag [MARKER_x] yang tercantum di dalam 'text_html' memiliki objek padanannya di dalam array 'intonation_markers' untuk baris tersebut dengan id yang persis sama. Pastikan array 'intonation_markers' SELALU TERISI minimal 1 marker per baris percakapan.
5. BUAT DETAIL ANALISIS YANG SANGAT MENDALAM DAN LUAS (COMPREHENSIVE ANALYSIS): Karena sistem didukung oleh arsitektur backend parser yang tangguh dan rendering real-time, Anda WAJIB memberikan penjelasan yang sangat komprehensif, akademis, dan mendalam pada 'agent_insight', 'advice_relation', 'reason', dan 'relation' (1-2 kalimat detail, minimal 20-30 kata per item). Jelaskan secara tajam aspek sosiolinguistik, dinamika relasi kekuasaan (power dynamics), serta implikasi akademik dari ujaran dosen dan mahasiswa tersebut agar hasil analisis bernilai akademis tinggi.

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
      "text_html": "string (PASTIKAN menyertakan tag [MARKER_x] dan [PAUSE] di lokasi intonasi/jeda. Tebalkan kata penting dengan <strong> atau <b>)",
      "is_advice": "JIKA kalimat ini adalah pemberian saran (advice), isi dengan PENJELASAN MENDALAM mengapa kalimat ini merupakan advice giving yang berelasi dengan modes of interaction. JIKA BUKAN saran, isi dengan null atau string kosong \"\". (Jangan gunakan boolean!)",
      "advice_type": "Salin EXACT STRING dari modes_of_interaction (misal: 'power_over', 'power_gaining', dsb)",
      "advice_giving": "Salin EXACT STRING dari input chunk (misal: 'bimbingan_bertahap')",
      "modes_of_interaction": "Salin EXACT STRING dari input chunk (misal: 'power_over')",
      "indobert_reasoning": "Penjelasan analitis mendalam dari AI mengenai MENGAPA kalimat ini diprediksi masuk ke dalam kategori advice_giving dan modes_of_interaction tersebut.",
      "agent_insight": "string",
      "advice_relation": "string",
      "intonation_markers": [
        {
          "id": "[MARKER_1]",
          "type": "up|down",
          "reason": "Penjelasan intonasi",
          "relation": "Relasi kalimat"
        }
      ]
    }
  ]
}
