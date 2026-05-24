Here are the fragmented analysis results (chunks) of an academic supervision conversation.
Your task is to merge them chronologically, remove semantic overlaps (deduplicate), recalculate global metrics mathematically and precisely, and compile a comprehensive C-CDA final report.

CHUNK DATA:
{CHUNKS_JSON}

ABSOLUTE SYNTHESIS RULES:
1. NEVER SUMMARIZE OR SHORTEN THE TRANSCRIPTION: You must include EVERY transcription line from all given chunks completely from beginning to end of the audio. Not a single sentence may be missing!
2. PRESERVE ALL INLINE MARKERS INTACT: You must maintain all marker tags such as [MARKER_1], [MARKER_2], etc., as well as [PAUSE] tags exactly in their positions within the transcription text ('text_html'). Never remove or clean these marker tags from the text!
3. MARKER CONSISTENCY: Ensure every [MARKER_x] tag listed in 'text_html' has its corresponding object in the 'intonation_markers' array for that line with the exact same id.
4. PROVIDE EXTREMELY DEEP AND COMPREHENSIVE ANALYSIS: Because the system is supported by a robust backend parser architecture and real-time rendering, you MUST provide very comprehensive, academic, and in-depth explanations for 'agent_insight', 'advice_relation', 'reason', and 'relation' (1-2 detailed sentences, minimum 20-30 words per item). Sharply explain the sociolinguistic aspects, power dynamics, and academic implications of the supervisor's and student's utterances so the analysis has high academic value.

Output format MUST be purely in valid structured JSON with the following schema:
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
      "speaker": "Supervisor|Student",
      "timestamp": "MM:SS - MM:SS",
      "text_html": "string (ENSURE preserving [MARKER_x] and [PAUSE] tags exactly at the spoken word locations. Bold important words with <strong> or <b>)",
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
