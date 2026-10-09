<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Supervisory AI' }} – Multi-Agent Voice Analysis</title>
    <meta name="description" content="Supervisory AI – Sistem analisis suara berbasis Multi-Agent AI untuk membedakan Mahasiswa dan Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #080b18;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        /* Animated gradient orbs */
        .bg-scene { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(90px); opacity: 0.25;
            animation: floatOrb 12s ease-in-out infinite alternate;
        }
        .orb-purple { width: 600px; height: 600px; background: radial-gradient(circle, #7c3aed, #4f46e5); top: -200px; left: -150px; animation-delay: 0s; }
        .orb-cyan   { width: 450px; height: 450px; background: radial-gradient(circle, #06b6d4, #0ea5e9); bottom: -150px; right: -100px; animation-delay: -4s; }
        .orb-pink   { width: 300px; height: 300px; background: radial-gradient(circle, #ec4899, #db2777); top: 40%; right: 20%; animation-delay: -8s; }
        @keyframes floatOrb { from { transform: translate(0, 0) scale(1); } to { transform: translate(30px, -30px) scale(1.08); } }

        /* Grid overlay */
        .grid-overlay {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Card wrapper */
        .guest-card {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            padding: 0 1rem;
        }

        /* Logo area */
        .brand { text-align: center; margin-bottom: 2.5rem; }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%);
            border-radius: 20px;
            margin: 0 auto 1rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            box-shadow: 0 0 40px rgba(124, 58, 237, 0.5);
        }
        .brand h1 { color: #fff; font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; }
        .brand p  { color: #94a3b8; font-size: 0.875rem; margin-top: 0.3rem; }

        /* Panel */
        .panel {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(24px) saturate(180%);
            border-radius: 24px;
            padding: 2.25rem 2.25rem;
            box-shadow: 0 32px 64px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.06);
        }

        /* Form elements */
        .form-group { margin-top: 1.25rem; }
        .form-label {
            display: block; color: #94a3b8;
            font-size: 0.75rem; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%; padding: 0.8rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus {
            border-color: #7c3aed;
            background: rgba(124, 58, 237, 0.08);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }
        .error-msg { color: #f87171; font-size: 0.78rem; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.3rem; }

        /* Buttons */
        .btn-primary {
            margin-top: 2rem; width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #7c3aed 0%, #4f87ff 100%);
            color: #fff; font-family: 'Inter', sans-serif;
            font-weight: 600; font-size: 0.95rem;
            border: none; border-radius: 12px; cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
            letter-spacing: 0.01em;
        }
        .btn-primary:hover { opacity: 0.92; transform: translateY(-2px); box-shadow: 0 8px 30px rgba(124, 58, 237, 0.5); }
        .btn-primary:active { transform: translateY(0); }

        /* Footer link */
        .alt-link { text-align: center; margin-top: 1.5rem; color: #64748b; font-size: 0.85rem; }
        .alt-link a { color: #a78bfa; text-decoration: none; font-weight: 500; }
        .alt-link a:hover { text-decoration: underline; }
    </style>
    {{ $styles ?? '' }}
</head>
<body>
    <div class="bg-scene">
        <div class="orb orb-purple"></div>
        <div class="orb orb-cyan"></div>
        <div class="orb orb-pink"></div>
    </div>
    <div class="grid-overlay"></div>

    <div class="guest-card">
        <div class="brand">
            <div class="brand-icon">🧠</div>
            <h1>Supervisory AI</h1>
            <p>Multi-Agent Voice Analysis System</p>
        </div>
        <div class="panel">
            {{ $slot }}
        </div>
        {{ $footer ?? '' }}
    </div>

    <!-- Floating Accessibility Button -->
    <button id="accessibility-toggle" class="fab-accessibility" title="Mode Disabilitas (Audio Guidance)">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
            <line x1="12" x2="12" y1="19" y2="22"></line>
        </svg>
    </button>

    <script>
        // --- ACCESSIBILITY MODE LOGIC ---
        let accessibilityMode = localStorage.getItem('accessibilityMode') === 'true';
        const fabBtn = document.getElementById('accessibility-toggle');
        const synth = window.speechSynthesis;
        
        let welcomeAudioObj = null;
        const welcomeAudioUrl = '{{ $welcomeAudio ?? "" }}';

        function updateAccessibilityUI() {
            if (accessibilityMode) {
                fabBtn.classList.add('active');
                if (welcomeAudioUrl && !welcomeAudioObj) {
                    welcomeAudioObj = new Audio(welcomeAudioUrl);
                    // Gunakan interaksi pertama untuk trigger autoplay policy
                    document.body.addEventListener('click', () => {
                        if (welcomeAudioObj.paused) welcomeAudioObj.play().catch(e => console.log('Autoplay blocked:', e));
                    }, {once: true});
                }
            } else {
                fabBtn.classList.remove('active');
                synth.cancel();
                if (welcomeAudioObj) {
                    welcomeAudioObj.pause();
                    welcomeAudioObj.currentTime = 0;
                }
            }
        }

        function speakText(text) {
            if (!accessibilityMode) return;
            synth.cancel(); // Hentikan ucapan sebelumnya
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 1.0;
            synth.speak(utterance);
        }

        if (fabBtn) {
            fabBtn.addEventListener('click', () => {
                accessibilityMode = !accessibilityMode;
                localStorage.setItem('accessibilityMode', accessibilityMode);
                updateAccessibilityUI();
                
                if (accessibilityMode) {
                    speakText("Mode aksesibilitas diaktifkan. Klik di mana saja pada halaman untuk memulai panduan audio.");
                } else {
                    // Canceled by updateAccessibilityUI
                }
            });
        }

        // Event listener untuk Web Speech API pada input form
        document.addEventListener('focusin', (e) => {
            if (accessibilityMode && e.target.tagName === 'INPUT') {
                const label = e.target.getAttribute('aria-label') || e.target.placeholder || e.target.name;
                const typeInfo = e.target.type === 'password' ? 'kolom kata sandi' : 'kolom teks';
                speakText("Anda berada di " + typeInfo + ". Silakan masukkan " + label);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateAccessibilityUI();
            
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
        // --- END ACCESSIBILITY MODE LOGIC ---
    </script>
</body>
</html>
