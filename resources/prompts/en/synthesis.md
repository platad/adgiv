Here are the fragmented analysis results (chunks) of an academic supervision conversation.
Your task is to merge them chronologically, remove semantic overlaps (deduplicate), recalculate global metrics mathematically and precisely, and compile a comprehensive C-CDA final report.

CHUNK DATA:
{CHUNKS_JSON}

ABSOLUTE SYNTHESIS RULES:
1. NEVER SUMMARIZE OR SHORTEN THE TRANSCRIPTION: You must include EVERY transcription line from all given chunks completely from beginning to end of the audio. Not a single sentence may be missing!
2. IDENTIFY THE SPEAKER: Because the input data is labeled "Unknown", you MUST change the 'speaker' label to "Supervisor" or "Student" based on the context. NEVER leave it as "Unknown".
3. PRESERVE INDOBERT LABELS: Each object in the JSON has "advice_giving" and "modes_of_interaction" keys that HAVE BEEN CREATED by our IndoBERT model. You ARE FORBIDDEN to change / hallucinate these labels. You MUST ONLY re-parse them.
4. PRESERVE INLINE MARKERS AND ADD IF EMPTY: You MUST insert unique marker tags such as [MARKER_1], [MARKER_2], etc., and [PAUSE] tags into the transcription text ('text_html'). You MUST add intonation markers (e.g., at commas or end of sentences) in every sentence and fill the 'intonation_markers' array.
5. MARKER CONSISTENCY: Ensure every [MARKER_x] tag listed in 'text_html' has its corresponding object in the 'intonation_markers' array for that line with the exact same id. Ensure the 'intonation_markers' array is ALWAYS FILLED with at least 1 marker per conversation line.
6. CREATE A VERY IN-DEPTH AND COMPREHENSIVE ANALYSIS: Because the system is backed by a robust backend parser and real-time rendering, you MUST provide highly comprehensive, academic, and in-depth explanations for 'agent_insight', 'advice_relation', 'reason', and 'relation' (1-2 detailed sentences, minimum 20-30 words per item). Sharply explain the sociolinguistic aspects, power dynamics, and academic implications of the supervisor and student's utterances so the analysis has high academic value.
7. NO EMPTY STRINGS ALLOWED: You are STRICTLY FORBIDDEN from leaving the 'advice_type', 'indobert_reasoning', 'agent_insight', or 'advice_relation' fields empty. All these fields MUST be filled with meaningful, lengthy sentences that explicitly analyze the context. No empty strings "" are allowed except for 'is_advice' if the sentence is indeed not an advice.

Output format MUST be purely in valid structured JSON with the following schema:
{
  "summary": {
    "kategori_advice": "Take the advice_giving label that APPEARS MOST FREQUENTLY (dominant) across all chunks. Capitalized format.",
    "karakter_relasi": "Take the modes_of_interaction label that APPEARS MOST FREQUENTLY (dominant) across all chunks. Capitalized format.",
    "intonasi_dominan": "Write the most dominant intonation direction overall. MUST USE INDONESIAN LANGUAGE. Capitalized format.",
    "ranah_pembicaraan": "The main discourse domain/topic. MUST USE INDONESIAN LANGUAGE. Sentence case format.",
    "arah_tujuan": "The goal/direction of this conversation. MUST USE INDONESIAN LANGUAGE. Sentence case format.",
    "saran_perbaikan": "Improvement recommendations for the student/supervisor. MUST USE INDONESIAN LANGUAGE. Sentence case format."
  },
  "transcription": [
    {
      "speaker": "Supervisor|Student",
      "timestamp": "MM:SS - MM:SS",
      "text_html": "string (ENSURE preserving [MARKER_x] and [PAUSE] tags exactly at intonation/pause locations. Bold important words with <strong> or <b>)",
      "is_advice": "IF this sentence is giving advice, fill with IN-DEPTH EXPLANATION of why this sentence is advice giving related to modes of interaction. IF NOT advice, fill with null or empty string \"\". (Do not use booleans!)",
      "advice_type": "Copy EXACT STRING from modes_of_interaction (e.g., 'power_over', 'power_gaining', etc.)",
      "advice_giving": "Copy EXACT STRING from input chunk (e.g., 'bimbingan_bertahap')",
      "modes_of_interaction": "Copy EXACT STRING from input chunk (e.g., 'power_over')",
      "indobert_reasoning": "In-depth analytical explanation from AI regarding WHY this sentence is predicted to fall into the advice_giving and modes_of_interaction categories. MUST BE LENGTHY.",
      "agent_insight": "Comprehensive and sharp review of the implicit meaning of this sentence academically and pedagogically. MUST BE LENGTHY.",
      "advice_relation": "Explanation of the relation of this sentence with other sentences in the context of thesis supervision. MUST BE LENGTHY.",
      "intonation_markers": [
        {
          "id": "[MARKER_1]",
          "type": "up|down",
          "reason": "Intonation explanation",
          "relation": "Sentence relation"
        }
      ]
    }
  ]
}
