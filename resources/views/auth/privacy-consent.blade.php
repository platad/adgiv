<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(app()->getLocale() === 'zh')
            隐私政策与服务条款 – Supervisory AI
        @elseif(app()->getLocale() === 'en')
            Privacy Policy & Consent Terms – Supervisory AI
        @else
            Kebijakan Privasi & Persetujuan – Supervisory AI
        @endif
    </title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --color-bima-red: #cc0000;
            --color-bima-red-dark: #990000;
            --color-bima-dark: #080b18;
        }
        body {
            background-color: #fafafa;
            font-family: 'Inter', sans-serif;
        }
        /* Language handling through HTML lang attribute */
        html[lang="id"] .lang-en, html[lang="id"] .lang-zh { display: none !important; }
        html[lang="en"] .lang-id, html[lang="en"] .lang-zh { display: none !important; }
        html[lang="zh"] .lang-id, html[lang="zh"] .lang-en { display: none !important; }

        /* Custom scrollbar for premium light panel */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(204, 0, 0, 0.3);
        }
    </style>
</head>
<body class="min-h-screen text-gray-900 flex flex-col relative overflow-x-hidden">

    {{-- Glowing background orbs --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[60rem] h-[60rem] bg-bima-red/5 rounded-full blur-[140px] opacity-70"></div>
        <div class="absolute -bottom-40 -right-40 w-[50rem] h-[50rem] bg-red-600/5 rounded-full blur-[140px] opacity-50"></div>
        <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col min-h-screen">
        
        {{-- Navigation Header --}}
        <header class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-gray-200/50 mb-10">
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="w-12 h-12 bg-white border border-gray-100/80 hover:bg-bima-red/5 hover:border-bima-red/20 transition-all duration-300 rounded-2xl flex items-center justify-center text-gray-700 hover:text-bima-red shadow-sm group">
                    <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"></i>
                </a>
                <div>
                    <h1 class="text-xl font-black uppercase tracking-tight text-gray-900 flex items-center gap-2">
                        Supervisory <span class="text-bima-red">AI</span>
                    </h1>
                    <p class="text-[0.6rem] font-bold text-gray-400 tracking-[0.2em] uppercase">
                        <span class="lang-id">Multi-Agent Voice Analysis &bull; BIMA</span>
                        <span class="lang-en">Multi-Agent Voice Analysis &bull; BIMA</span>
                        <span class="lang-zh">多智能体语音分析 &bull; 核心算法</span>
                    </p>
                </div>
            </div>

            {{-- Right side info widgets --}}
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                {{-- Clock Widget --}}
                <div class="bg-white border border-gray-150 rounded-2xl px-4 py-2 flex items-center gap-3 shadow-sm">
                    <div class="text-right">
                        <span id="live-time" class="block text-xs font-black tracking-widest font-mono text-gray-900">00:00:00</span>
                        <span id="live-date" class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">---</span>
                    </div>
                </div>

                {{-- Language Toggles --}}
                <div class="bg-gray-200/50 p-0.5 rounded-xl flex items-center gap-0.5 shadow-sm border border-gray-200/20">
                    <button onclick="setLanguage('id')" id="lang-btn-id" class="px-3.5 py-1.5 rounded-xl text-[0.65rem] font-black tracking-widest transition-all duration-300">ID</button>
                    <button onclick="setLanguage('en')" id="lang-btn-en" class="px-3.5 py-1.5 rounded-xl text-[0.65rem] font-black tracking-widest transition-all duration-300">EN</button>
                    <button onclick="setLanguage('zh')" id="lang-btn-zh" class="px-3.5 py-1.5 rounded-xl text-[0.65rem] font-black tracking-widest transition-all duration-300">ZH</button>
                </div>
            </div>
        </header>

        {{-- Page Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 flex-1">
            
            {{-- Sidebar navigation --}}
            <aside class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6 lang-id">Daftar Isi</h3>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6 lang-en">Table of Contents</h3>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6 lang-zh">目录索引</h3>
                        
                        <nav class="flex flex-col gap-2">
                            <a href="#consent" class="flex items-center gap-3 p-3 rounded-2xl text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all duration-200">
                                <i data-lucide="check-square" class="w-4 h-4 text-bima-red shrink-0"></i>
                                <span class="lang-id">1. Persetujuan Layanan</span>
                                <span class="lang-en">1. Informed Consent</span>
                                <span class="lang-zh">1. 知情同意条款</span>
                            </a>
                            <a href="#methodology" class="flex items-center gap-3 p-3 rounded-2xl text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all duration-200">
                                <i data-lucide="brain" class="w-4 h-4 text-bima-red shrink-0"></i>
                                <span class="lang-id">2. Metodologi Komputasi</span>
                                <span class="lang-en">2. Computational Methodology</span>
                                <span class="lang-zh">2. 计算方法论</span>
                            </a>
                            <a href="#national-law" class="flex items-center gap-3 p-3 rounded-2xl text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all duration-200">
                                <i data-lucide="shield-check" class="w-4 h-4 text-bima-red shrink-0"></i>
                                <span class="lang-id">3. UU PDP & UU ITE</span>
                                <span class="lang-en">3. UU PDP & UU ITE Compliance</span>
                                <span class="lang-zh">3. 国家法律合规性</span>
                            </a>
                            <a href="#education-law" class="flex items-center gap-3 p-3 rounded-2xl text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all duration-200">
                                <i data-lucide="graduation-cap" class="w-4 h-4 text-bima-red shrink-0"></i>
                                <span class="lang-id">4. Peraturan PT (PPKS)</span>
                                <span class="lang-en">4. Higher Education Rules</span>
                                <span class="lang-zh">4. 高等教育伦理</span>
                            </a>
                            <a href="#user-rights" class="flex items-center gap-3 p-3 rounded-2xl text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all duration-200">
                                <i data-lucide="user-x" class="w-4 h-4 text-bima-red shrink-0"></i>
                                <span class="lang-id">5. Hak Pengguna & Opt-Out</span>
                                <span class="lang-en">5. User Rights & Opt-Out</span>
                                <span class="lang-zh">5. 数据主体与退出</span>
                            </a>
                        </nav>
                    </div>

                    {{-- Back button link --}}
                    <a href="{{ route('login') }}" class="w-full py-4 rounded-[1.8rem] bg-gray-900 hover:bg-bima-red text-white font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2 shadow-xl shadow-gray-200 hover:shadow-red-100/50">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span class="lang-id">Kembali ke Masuk</span>
                        <span class="lang-en">Back to Sign In</span>
                        <span class="lang-zh">返回登录界面</span>
                    </a>
                </div>
            </aside>

            {{-- Main Content Panels --}}
            <main class="lg:col-span-3 space-y-8">
                
                {{-- Welcome Title Panel --}}
                <div class="relative bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-bima-red/5 rounded-full blur-[90px] pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-bima-red/5 text-bima-red border border-bima-red/10 text-[0.65rem] font-black uppercase tracking-widest mb-6">
                            <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                            <span class="lang-id">Dokumen Hukum & Etika</span>
                            <span class="lang-en">Legal & Ethical Document</span>
                            <span class="lang-zh">法律与学术道德规程</span>
                        </span>
                        
                        <h2 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tight leading-tight mb-4">
                            <span class="lang-id">Kebijakan Privasi & Persyaratan Persetujuan Layanan</span>
                            <span class="lang-en">Privacy Policy & Consent Agreement Terms</span>
                            <span class="lang-zh">隐私政策与服务条款协议</span>
                        </h2>
                        
                        <p class="text-gray-500 text-xs font-semibold leading-relaxed max-w-3xl lang-id">
                            Harap baca dokumen ini dengan seksama. Dokumen ini mendefinisikan aspek perlindungan data pribadi Anda, metodologi komputasi kritis (C-CDA) yang kami gunakan, serta kepatuhan hukum atas pemrosesan data audio/suara bimbingan Anda.
                        </p>
                        <p class="text-gray-500 text-xs font-semibold leading-relaxed max-w-3xl lang-en">
                            Please read this document carefully. It defines the parameters of your personal data protection, the critical computational discourse analysis (C-CDA) methodology applied, and the regulatory compliance under which your audio data is processed.
                        </p>
                        <p class="text-gray-500 text-xs font-semibold leading-relaxed max-w-3xl lang-zh">
                            请仔细阅读本声明。本文件明确界定了您的个人数据保护参数、所采用的计算机辅助批判性话语分析（C-CDA）方法论框架，以及您的录音数据处理所遵循的国家及部门合规标准。
                        </p>
                    </div>
                </div>

                {{-- Policy Sections Container --}}
                <div class="space-y-8">
                    
                    {{-- 1. INFORMED CONSENT --}}
                    <section id="consent" class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/15 border border-bima-red/20 text-bima-red flex items-center justify-center shadow-lg">
                                <i data-lucide="check-square" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">BAB 1 &bull; CHAPTER 1 &bull; 第1章</span>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">
                                    <span class="lang-id">Persetujuan Tindakan Mediasi & Perekaman (Informed Consent)</span>
                                    <span class="lang-en">Informed Consent & Audio Processing Authorization</span>
                                    <span class="lang-zh">知情同意与录音授权规程</span>
                                </h3>
                            </div>
                        </div>

                        <div class="text-gray-600 text-xs leading-relaxed space-y-4 font-medium">
                            <p class="lang-id">
                                Dengan menggunakan platform <strong>Supervisory AI</strong>, baik Dosen maupun Mahasiswa menyatakan telah setuju secara sadar dan aktif (*explicit informed consent*) untuk mengunggah atau merekam dialog bimbingan akademik dalam format suara (audio).
                            </p>
                            <p class="lang-en">
                                By engaging with the <strong>Supervisory AI</strong> platform, both Faculty members and Students declare their explicit, active informed consent to upload or record academic supervision dialogues in audio format.
                            </p>
                            <p class="lang-zh">
                                通过使用 <strong>Supervisory AI</strong> 智能督导系统平台，教师和学生均在此声明已明确并主动授予“知情同意权” (*explicit informed consent*)，允许系统录制、上传或导入学术辅导过程中的语音会话。
                            </p>

                            <p class="lang-id">
                                Persetujuan ini memberikan kewenangan sah kepada sistem untuk memproses data bio-akustik dan transkripsi verbal guna mendeteksi dinamika interaksi, strategi kesantunan, dan sintesis relasi wacana bimbingan.
                            </p>
                            <p class="lang-en">
                                This consent grants the platform legitimate authority to process bio-acoustic wave features and verbal transcriptions to detect interaction dynamics, politeness strategies, and conversational relation synthesis.
                            </p>
                            <p class="lang-zh">
                                本授权赋予系统合法的处理权限，可以对导入的生理生电声波特征以及口头文本转译进行深度分析，用于计算交互动态、评估礼貌策略并整合话语支配关系。
                            </p>
                        </div>
                    </section>

                    {{-- 2. COMPUTATIONAL METHODOLOGY --}}
                    <section id="methodology" class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/5 border border-bima-red/10 text-bima-red flex items-center justify-center shadow-sm">
                                <i data-lucide="brain" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">BAB 2 &bull; CHAPTER 2 &bull; 第2章</span>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">
                                    <span class="lang-id">Metodologi Computational Critical Discourse Analysis (C-CDA)</span>
                                    <span class="lang-en">Computational Critical Discourse Analysis (C-CDA) Methodology</span>
                                    <span class="lang-zh">计算辅助批判性话语分析（C-CDA）科学方法论</span>
                                </h3>
                            </div>
                        </div>

                        <div class="text-gray-600 text-xs leading-relaxed space-y-4 font-medium">
                            <p class="lang-id">
                                Kajian Wacana Kritis secara Tradisional (AWK) didigitalisasi melalui kerangka <strong>C-CDA</strong> yang memanfaatkan teknologi terdepan:
                            </p>
                            <p class="lang-en">
                                Traditional Critical Discourse Analysis (CDA) is digitized under our proprietary <strong>C-CDA</strong> framework using cutting-edge techniques:
                            </p>
                            <p class="lang-zh">
                                我们通过 <strong>C-CDA</strong> 算法系统将传统的社会语言学批判性话语分析（CDA）完全数字化，该架构深度集成了以下前沿技术：
                            </p>

                            <ul class="list-disc pl-5 space-y-2 text-gray-500">
                                <li>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-id">Pemrosesan Nativ Multimodal (G-MLLM):</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-en">Native Multimodal Processing (G-MLLM):</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-zh">原生多模态生成式大模型端到端处理 (G-MLLM):</strong>
                                    <span class="lang-id"> Suara diproses secara langsung (*speech-to-semantic*) tanpa hanya bergantung pada transkrip teks mentah. Ini memungkinkan deteksi aspek non-verbal seperti intonasi (nada tinggi/rendah), jeda kesunyian (*silence pauses*), dan ketegasan bicara.</span>
                                    <span class="lang-en"> Audio waves are analyzed directly in an end-to-end *speech-to-semantic* approach. This preserves structural non-verbal features like speech pitch (intonation), silence pauses, and conversational momentum.</span>
                                    <span class="lang-zh"> 语音数据在统一的端到端神经网络中直接转化为高维语义表示 (*speech-to-semantic*)，不再仅仅依赖容易丢失特征的文字转译。这得以准确识别音调波动（intonation）、非自愿沉默间隙（silence pauses）和言语决断力。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-id">Debat Agen Kecerdasan Buatan (Multi-Agent AI Debate):</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-en">Multi-Agent AI Debate:</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-zh">多智能体人工智能内在博弈辩论 (Multi-Agent AI Debate):</strong>
                                    <span class="lang-id"> Beberapa agen kecerdasan buatan (yang mengadopsi peran dosen, mahasiswa, dan ahli bahasa) saling berdiskusi secara internal guna mensintesis dan menyeimbangkan penilaian tentang adanya bias dominasi wacana secara objektif.</span>
                                    <span class="lang-en"> Multiple intelligent agents (simulating the viewpoints of lecturers, students, and critical linguists) debate internally to objectively synthesize and minimize relational evaluation bias.</span>
                                    <span class="lang-zh"> 部署多个垂直微调的 AI 智能体（分别扮演资深教授、在读学生及批判性语言学专家的角色）进行多轮内部对齐博弈，以极其客观的方式归纳话语控制偏差，最小化评估的主观局限。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-id">Metrik Politeness & Power:</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-en">Politeness & Power Metrics:</strong>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-zh">礼貌矩阵与权力控制度量:</strong>
                                    <span class="lang-id"> Menganalisis strategi pengarahan saran akademis (*advice-giving*) untuk memisahkan bimbingan yang bersahabat dan kolaboratif dari interaksi yang bersifat asimetris koersif.</span>
                                    <span class="lang-en"> Evaluates academic advice-giving strategies to differentiate supportive, collaborative guidance from coercive, asymmetric interactions.</span>
                                    <span class="lang-zh"> 系统对学术辅导中的“建言提供策略”（*advice-giving*）进行精密建模，可有效将健康、平等的协作性辅导与暗含学术霸凌、强迫倾向的不对称宰制关系区分开来。</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    {{-- 3. NATIONAL LAW COMPLIANCE --}}
                    <section id="national-law" class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/5 border border-bima-red/10 text-bima-red flex items-center justify-center shadow-sm">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">BAB 3 &bull; CHAPTER 3 &bull; 第3章</span>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">
                                    <span class="lang-id">Kepatuhan Hukum Negara Republik Indonesia (UU PDP & UU ITE)</span>
                                    <span class="lang-en">Compliance with Republic of Indonesia Laws (UU PDP & UU ITE)</span>
                                    <span class="lang-zh">符合印尼国家法典合规标准（UU PDP & UU ITE）</span>
                                </h3>
                            </div>
                        </div>

                        <div class="text-gray-600 text-xs leading-relaxed space-y-4 font-medium">
                            <p class="lang-id">
                                Pengolahan data suara merupakan bentuk pengolahan data pribadi sensitif (fisiologis/biometrik) yang diproteksi dengan ketat:
                            </p>
                            <p class="lang-en">
                                Audio wave processing represents the handling of sensitive physiological/biometric personal data, heavily protected under national frameworks:
                            </p>
                            <p class="lang-zh">
                                语音或声音数据的提取属于高度敏感的生理与生物特征个人数据处理，受到严格的合规性保护：
                            </p>

                            <ul class="list-disc pl-5 space-y-2 text-gray-500">
                                <li>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-id">UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP):</strong>
                                    <span class="lang-id"> Menjamin hak Anda untuk mengakhiri pemrosesan, memperbaiki kekeliruan data, membatasi pemrosesan, dan menuntut penghapusan rekaman secara permanen. Pengiriman dan penyimpanan data audio dienkripsi penuh menggunakan AES-256.</span>
                                    <span class="lang-en"> Ensures your right to terminate data processing, rectify errors, restrict handling, and demand permanent record erasure. Audio data transmission and storage are protected with end-to-end AES-256 encryption.</span>
                                    <span class="lang-zh"> 充分保障您的数据主体权利，包括随时撤销处理授权、修正标注偏误、限制二次共享以及要求永久销毁数据的权利。所有的音频在信道传输与磁盘静态存储时均由 AES-256 高强度机制加密。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-800 uppercase tracking-wider text-[0.7rem] lang-id">UU ITE No. 11/2008 jo. UU No. 19/2016:</strong>
                                    <span class="lang-id"> Sistem ini menjamin validitas pembuktian log digital (*digital audit trails*), integritas pemrosesan informasi elektronik, serta perlindungan sistem dari akses tidak sah (*unauthorized access*).</span>
                                    <span class="lang-en"> Validates the legality of electronic logs, guarantees electronic information transmission integrity, and secures the system against unauthorized access.</span>
                                    <span class="lang-zh"> 确保系统中产生的所有电子操作溯源日志（digital audit trails）符合电子司法证据的可采性要求，保证在电子数据交换中具备防篡改的完整性。</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    {{-- 4. HIGHER EDUCATION AND MINISTRY RULES --}}
                    <section id="education-law" class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/5 border border-bima-red/10 text-bima-red flex items-center justify-center shadow-sm">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">BAB 4 &bull; CHAPTER 4 &bull; 第4章</span>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">
                                    <span class="lang-id">Peraturan Kementerian Pendidikan & Etika Perguruan Tinggi</span>
                                    <span class="lang-en">Ministry of Education Regulations & Higher Ed Academic Ethics</span>
                                    <span class="lang-zh">印尼国家教育部高等教育条例与学术伦理合规</span>
                                </h3>
                            </div>
                        </div>

                        <div class="text-gray-600 text-xs leading-relaxed space-y-4 font-medium">
                            <p class="lang-id">
                                Supervisory AI dirancang sebagai alat pendukung etika bimbingan akademik yang bermutu tinggi dan selaras dengan mandat kementerian:
                            </p>
                            <p class="lang-en">
                                Supervisory AI serves as an ethical enabler for premium academic guidance, fully complying with ministry mandates:
                            </p>
                            <p class="lang-zh">
                                Supervisory AI 系统是作为辅助高等院校高质量学术导师制与学术公平而设计的科学仪器，严格遵循教育部要求：
                            </p>

                            <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 space-y-3.5 shadow-sm">
                                <h4 class="text-xs font-black uppercase tracking-wider text-bima-red flex items-center gap-2">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-bima-red shrink-0"></i>
                                    <span class="lang-id">Mitigasi Asimetri Relasi Kuasa (Permendikbudristek No. 30 Tahun 2021)</span>
                                    <span class="lang-en">Power Asymmetry Mitigation (Permendikbudristek No. 30 Year 2021)</span>
                                    <span class="lang-zh">防范学术权力不对称（教育部2021年第30号条例 PPKS）</span>
                                </h4>
                                <p class="lang-id text-gray-500 font-semibold">
                                    Peraturan Menteri ini secara eksplisit mengamanatkan pencegahan kekerasan seksual dan perundungan yang bersumber dari <strong>ketimpangan relasi kuasa (power asymmetry)</strong> di perguruan tinggi. Supervisory AI menggunakan komputasi C-CDA secara transparan untuk mendeteksi tanda-tanda dominasi wacana, intimidasi terselubung, dan koersi akademik antara Dosen (selaku pemegang otoritas) dan Mahasiswa, guna menjamin iklim kampus yang aman dan setara.
                                </p>
                                <p class="lang-en text-gray-500 font-medium">
                                    This ministerial regulation explicitly mandates the prevention of harassment, bullying, and abuse originating from <strong>power asymmetry</strong> in higher education institutions. Supervisory AI applies C-CDA modeling transparently to detect structural discourse dominance, passive intimidation, and academic coercion between Faculty (as authority holders) and Students, promoting a safe and inclusive campus environment.
                                </p>
                                <p class="lang-zh text-gray-500 font-medium">
                                    该部令明确指出必须防范因**权力不对称 (power asymmetry)** 衍生的高等院校内部性骚扰、精神压迫与学术胁迫。Supervisory AI 系统利用 C-CDA 智能话语特征工程分析，客观诊断导师（权力强势方）与学生之间是否存在话语控制、隐性威逼和恐吓，确保校园拥有开放、安全和平等的氛围。
                                </p>
                            </div>

                            <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 space-y-3.5 shadow-sm">
                                <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 flex items-center gap-2">
                                    <i data-lucide="award" class="w-4 h-4 shrink-0"></i>
                                    <span class="lang-id">Penjaminan Mutu & Etika Riset (Permendikbudristek No. 53 Tahun 2023)</span>
                                    <span class="lang-en">Quality Assurance & Research Ethics (Permendikbudristek No. 53 Year 2023)</span>
                                    <span class="lang-zh">质量保障与国家级课题学术道德准则 (2023年第53号部令)</span>
                                </h4>
                                <p class="lang-id text-gray-500 font-semibold">
                                    Memastikan seluruh metode riset komputasional dan pengumpulan korpus teranotasi (gold-standard dataset) melalui skema Penelitian Terapan BIMA Kemendikbudristek ini memenuhi asas kelaikan etika (*ethical clearance*) perguruan tinggi secara akuntabel.
                                </p>
                                <p class="lang-en text-gray-500 font-medium">
                                    Ensures all computational research and annotated corpus acquisition (gold-standard dataset) conducted under the BIMA Kemendikbudristek Applied Research grant conform strictly to academic ethical clearance requirements.
                                </p>
                                <p class="lang-zh text-gray-500 font-medium">
                                    确保本项目（属于印尼国家教育部 BIMA 科技应用研究资助计划）在构建我国首个具备科学价值的“话语金标准标注语料库”时，从机制设计到流程管理均完全通过高校严格的伦理委员会安全审查（*ethical clearance*）。
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 5. USER RIGHTS AND OPT-OUT --}}
                    <section id="user-rights" class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-100/50 backdrop-blur-xl">
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/5 border border-bima-red/10 text-bima-red flex items-center justify-center shadow-sm">
                                <i data-lucide="user-x" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">BAB 5 &bull; CHAPTER 5 &bull; 第5章</span>
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">
                                    <span class="lang-id">Hak Subjek Data, Prosedur Opt-Out & Penghapusan</span>
                                    <span class="lang-en">Data Subject Rights, Opt-Out Procedures & Erasure</span>
                                    <span class="lang-zh">数据主体法定权利、一键选择退出与记录擦除流程</span>
                                </h3>
                            </div>
                        </div>

                        <div class="text-gray-600 text-xs leading-relaxed space-y-4 font-medium">
                            <p class="lang-id">
                                Keikutsertaan Anda dalam analisis bimbingan akademis berbasis AI ini bersifat 100% sukarela. Pengguna berhak penuh untuk:
                            </p>
                            <p class="lang-en">
                                Your participation in this AI-driven academic discourse analysis is 100% voluntary. Users retain the complete right to:
                            </p>
                            <p class="lang-zh">
                                用户参与本平台的话语科学分析完全遵循自愿原则。系统绝对保护用户的以下数据权利：
                            </p>

                            <ul class="list-disc pl-5 space-y-2 text-gray-500">
                                <li>
                                    <span class="lang-id">Menarik kembali persetujuan perekaman (*withdraw consent*) kapan pun bimbingan sedang atau telah berlangsung.</span>
                                    <span class="lang-en">Withdraw recording consent at any time during or after a supervision session.</span>
                                    <span class="lang-zh">在对话进行中、或者录音上传完毕后的任意时间节点，随时无条件撤回录音及计算授权（*withdraw consent*）。</span>
                                </li>
                                <li>
                                    <span class="lang-id">Menghapus data audio mentah dan transkrip wacana secara permanen melalui tombol hapus pada riwayat analisis.</span>
                                    <span class="lang-en">Permanently purge raw audio files and discourse transcriptions via the dashboard's delete mechanism.</span>
                                    <span class="lang-zh">通过系统控制面板直接向服务器下发擦除指令，永久、物理抹除原始音频切片以及相关联的话语判定转译记录。</span>
                                </li>
                                <li>
                                    <span class="lang-id">Menghubungi administrator atau Tim Penelitian Terapan BIMA (UMPO & UMPRI) untuk meminta laporan audit transparansi pengolahan data Anda.</span>
                                    <span class="lang-en">Contact the systems administrator or the BIMA Research Team (UMPO & UMPRI) to request a personal data audit report.</span>
                                    <span class="lang-zh">直接联系平台运维人员或印尼 BIMA 国家重点课题组（UMPO & UMPRI 分支），获取您的个人数据审计与加密传输的合规透明度核实报告。</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                </div>

            </main>
        </div>

        {{-- Footer --}}
        <footer class="mt-16 pt-8 border-t border-white/5 text-center text-[0.65rem] font-bold text-white/20 uppercase tracking-[0.25em] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                &copy; 2026 Supervisory AI &bull; BIMA Kemendikbudristek &bull; UMPO
            </div>
            <div>
                <a href="{{ route('login') }}" class="text-slate-500 hover:text-white transition-colors">
                    <span class="lang-id">Kembali ke Halaman Masuk</span>
                    <span class="lang-en">Back to Login Screen</span>
                    <span class="lang-zh">返回登录界面</span>
                </a>
            </div>
        </footer>

    </div>

    {{-- Interactive Live Clock & Language Scripts --}}
    <script>
        function updateClock() {
            const now = new Date();
            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            let seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('live-time').textContent = `${hours}:${minutes}:${seconds}`;

            const activeLang = document.documentElement.getAttribute('lang') || 'id';
            let dateStr = '';
            
            if (activeLang === 'id') {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} WIB`;
            } else if (activeLang === 'zh') {
                const days = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                dateStr = `${now.getFullYear()}年${now.getMonth() + 1}月${now.getDate()}日 ${days[now.getDay()]} (WIB)`;
            } else {
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const tz = 'GMT' + (now.getTimezoneOffset() <= 0 ? '+' : '-') + Math.abs(Math.floor(now.getTimezoneOffset() / 60));
                dateStr = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()} ${now.getFullYear()} ${tz}`;
            }
            
            document.getElementById('live-date').textContent = dateStr;
        }

        function setLanguage(lang) {
            // Write to cookie (for backend autoredirect)
            document.cookie = "locale=" + lang + "; path=/; max-age=" + (30 * 24 * 60 * 60);
            
            // Save in localStorage (cache browser)
            localStorage.setItem('lang', lang);
            
            // Set lang attribute on root html
            document.documentElement.setAttribute('lang', lang);
            
            // Stylize the active toggle button
            const btnId = document.getElementById('lang-btn-id');
            const btnEn = document.getElementById('lang-btn-en');
            const langBtnZh = document.getElementById('lang-btn-zh');
            
            const activeClass = "px-3.5 py-1.5 rounded-xl text-[0.65rem] font-black tracking-widest bg-bima-red text-white shadow-md transition-all duration-300 border-none cursor-pointer";
            const inactiveClass = "px-3.5 py-1.5 rounded-xl text-[0.65rem] font-black tracking-widest text-gray-400 hover:text-gray-900 transition-all duration-300 border-none cursor-pointer";
            
            btnId.className = (lang === 'id') ? activeClass : inactiveClass;
            btnEn.className = (lang === 'en') ? activeClass : inactiveClass;
            langBtnZh.className = (lang === 'zh') ? activeClass : inactiveClass;
            
            updateClock();

            // Synchronize active URL path
            const currentPath = window.location.pathname; // e.g. "/id/privacy-consent"
            const pathParts = currentPath.split('/'); // ["", "id", "privacy-consent"]
            if (pathParts.length > 1 && ['id', 'en', 'zh'].includes(pathParts[1])) {
                if (pathParts[1] !== lang) {
                    pathParts[1] = lang;
                    const newPath = pathParts.join('/');
                    window.location.href = newPath + window.location.search + window.location.hash;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Read language state from active URL segment first
            const pathParts = window.location.pathname.split('/');
            let activeLocale = 'id';
            if (pathParts.length > 1 && ['id', 'en', 'zh'].includes(pathParts[1])) {
                activeLocale = pathParts[1];
            } else {
                activeLocale = localStorage.getItem('lang') || 'id';
            }
            
            setLanguage(activeLocale);
            
            updateClock();
            setInterval(updateClock, 1000);
            
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
