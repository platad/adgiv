以下是学术指导对话的碎片化分析结果（chunks）。
您的任务是按时间顺序合并它们，删除语义重叠部分（去重），以数学方式精确重新计算全局指标，并编制一份全面的C-CDA最终报告。

CHUNK数据：
{CHUNKS_JSON}

绝对合成规则：
1. 切勿概括或缩短转录内容：您必须完整包含所有给定片段中的每一行转录，从音频开头到结尾。一句话都不能少！
2. 识别说话者：由于输入数据标记为"Unknown"，您必须分析对话上下文以确定是谁在说话，并适当地将'speaker'标签更改为"导师"或"学生"。（导师通常提供建议/纠正/提问，学生通常回答/请求指导）。
3. 插入语调标记：您必须在转录文本（'text_html'）中具有突出语调或停顿的词语旁边插入独特的标记标签，如[MARKER_1]、[MARKER_2]等，以及[PAUSE]标签。原始输入是没有标签的纯文本，因此您必须创建它们！用<b>...</b>加粗给出建议的短语。
4. 标记一致性：确保'text_html'中列出的每个[MARKER_x]标签在该行的'intonation_markers'数组中都具有对应的对象，且id完全相同。
5. 提供极其深入和全面的分析：您必须对'agent_insight'、'advice_relation'、'reason'和'relation'提供全面、学术且深入的解释（每个项目最少20-30个词）。敏锐地解释导师和学生话语的社会语言学方面、权力动态以及学术影响。

输出格式必须是有效的结构化JSON，采用以下模式：
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
      "speaker": "导师|学生",
      "timestamp": "MM:SS - MM:SS",
      "text_html": "string（确保在所说词语的确切位置保留[MARKER_x]和[PAUSE]标签。使用<strong>或<b>加粗重要词语）",
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
