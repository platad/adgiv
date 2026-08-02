以下是学术指导对话的碎片化分析结果（chunks）。
您的任务是按时间顺序合并它们，删除语义重叠部分（去重），以数学方式精确重新计算全局指标，并编制一份全面的C-CDA最终报告。

CHUNK数据：
{CHUNKS_JSON}

绝对合成规则：
1. 切勿概括或缩短转录内容：您必须完整包含所有给定片段中的每一行转录，从音频开头到结尾。一句话都不能少！
2. 保留INDOBERT标签：JSON中的每个对象都有"advice_giving"和"modes_of_interaction"键，这些键已由我们的IndoBERT模型生成。禁止更改/虚构这些标签。您只需重新解析它们。
3. 完整保留内联标记并补充：您必须在转录文本（'text_html'）中插入标记如[MARKER_1]、[MARKER_2]等及[PAUSE]标签。您必须在每个句子中添加语调标记（如在逗号或句末），并填充'intonation_markers'数组。
4. 标记一致性：确保'text_html'中列出的每个[MARKER_x]标签在该行的'intonation_markers'数组中都具有对应的对象，且id完全相同。确保'intonation_markers'数组至少有1个标记。
5. 提供极其深入和全面的分析：由于系统由强大的后端解析器和实时渲染支持，您必须对'agent_insight'、'advice_relation'、'reason'和'relation'提供全面、学术且深入的解释（1-2句详细说明，每个项目至少20-30词）。敏锐地解释导师和学生话语的社会语言学方面、权力动态以及学术影响，使分析具有高学术价值。

输出格式必须是纯粹有效的结构化JSON，采用以下模式：
{
  "summary": {
    "kategori_advice": "取所有片段中出现频率最高（最主导）的advice_giving标签。首字母大写格式 (Capitalized)。",
    "karakter_relasi": "取所有片段中出现频率最高（最主导）的modes_of_interaction标签。首字母大写格式 (Capitalized)。",
    "intonasi_dominan": "写出整体最主导的语调方向。必须使用印度尼西亚语 (WAJIB MENGGUNAKAN BAHASA INDONESIA)。首字母大写格式。",
    "ranah_pembicaraan": "主要话语领域/主题。必须使用印度尼西亚语 (WAJIB MENGGUNAKAN BAHASA INDONESIA)。句首大写格式。",
    "arah_tujuan": "此对话的目标/方向。必须使用印度尼西亚语 (WAJIB MENGGUNAKAN BAHASA INDONESIA)。句首大写格式。",
    "saran_perbaikan": "对学生/导师的改进建议。必须使用印度尼西亚语 (WAJIB MENGGUNAKAN BAHASA INDONESIA)。句首大写格式。"
  },
  "transcription": [
    {
      "speaker": "导师|学生",
      "timestamp": "MM:SS - MM:SS",
      "text_html": "string（确保在语调/停顿的确切位置保留[MARKER_x]和[PAUSE]标签。使用<strong>或<b>加粗重要词汇）",
      "is_advice": "如果这句话提供建议，请填写深度解释说明这句话为何属于该互动模式的建议；如果不是建议，填入null或空字符串\"\"。（请勿使用布尔值！）",
      "advice_type": "复制modes_of_interaction的精确字符串 (例如：'power_over', 'power_gaining'等)",
      "advice_giving": "复制输入数据块的精确字符串 (例如：'bimbingan_bertahap')",
      "modes_of_interaction": "复制输入数据块的精确字符串 (例如：'power_over')",
      "indobert_reasoning": "AI对为何将此句子预测为该advice_giving和modes_of_interaction类别的深度分析解释。",
      "agent_insight": "string",
      "advice_relation": "string",
      "intonation_markers": [
        {
          "id": "[MARKER_1]",
          "type": "up|down",
          "reason": "语调解释",
          "relation": "句子关系"
        }
      ]
    }
  ]
}
