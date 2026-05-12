<?php

namespace App\Http\Controllers;

use App\Models\AgentPrompt;
use App\Models\ChatSession;
use App\Services\TranscriptionService;
use App\Services\KimiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChatController extends Controller
{
    public function __construct(
        private readonly TranscriptionService     $transcriptionService,
        private readonly KimiApiService           $kimiApiService,
    ) {}

    /**
     * GET /chat
     * Render the main chat interface.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        
        $requestedId = $request->query('session');

        if ($requestedId) {
            $activeSession = ChatSession::where('id', $requestedId)
                ->where('user_id', Auth::id())
                ->with(['messages' => fn($q) => $q->orderBy('created_at', 'asc')])
                ->first();
            
            if (!$activeSession) {
                return redirect()->route('chat.index');
            }
        }

        if (empty($activeSession)) {
            $activeSession = ChatSession::create([
                'user_id' => $user->id,
                'title'   => 'Sesi ' . now()->format('d M Y H:i'),
            ]);
            
            return redirect()->route('chat.index', ['session' => $activeSession->id]);
        }

        return view('chat', compact('activeSession'));
    }

    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:100',
        ]);

        $session = ChatSession::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'] ?? 'Sesi Baru ' . now()->format('H:i'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    /**
     * STEP 1: Analisa Suara (Save Raw Transcription)
     */
    public function analyseStep1(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
            'transcription' => 'required|string',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);
        $session->update(['raw_transcription' => $validated['transcription']]);

        // Add user message to history
        $session->messages()->create([
            'role'    => 'user',
            'content' => $validated['transcription'],
        ]);

        return response()->json([
            'success' => true,
            'step'    => 1,
            'data'    => ['transcription' => $validated['transcription']]
        ]);
    }

    /**
     * STEP 2: Merapikan Hasil Suara (Refine)
     */
    public function analyseStep2(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);
        if (!$session->raw_transcription || $session->raw_transcription === '-') {
            return response()->json([
                'success' => false,
                'message' => 'Teks asli tidak ditemukan. Silakan upload audio kembali.',
            ]);
        }

        $agentPrompt = AgentPrompt::getActivePrompt('text_cleaner');
        $result = $this->kimiApiService->complete($agentPrompt->system_prompt, $session->raw_transcription);
        $parsed = json_decode($result['content'], true);

        if (!$parsed || !isset($parsed['refined_text'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal merapikan teks: ' . ($result['content'] ?? 'Respons tidak valid'),
            ]);
        }

        $session->update(['refined_transcription' => $parsed['refined_text']]);

        return response()->json([
            'success' => true,
            'step'    => 2,
            'data'    => $parsed,
        ]);
    }

    /**
     * STEP 3: Pencocokan Suara (Matching)
     */
    public function analyseStep3(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);
        $agentPrompt = AgentPrompt::getActivePrompt('text_matcher');
        
        $input = "Teks Asli: " . $session->raw_transcription . "\n\nTeks Rapih: " . $session->refined_transcription;
        $result = $this->kimiApiService->complete($agentPrompt->system_prompt, $input);
        $parsed = json_decode($result['content'], true);

        if (!$parsed || !isset($parsed['matched_text'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencocokkan teks: ' . ($result['content'] ?? 'Respons tidak valid'),
            ]);
        }

        $session->update(['matched_transcription' => $parsed['matched_text']]);

        return response()->json([
            'success' => true,
            'step'    => 3,
            'data'    => $parsed,
        ]);
    }

    /**
     * STEP 4: Advice Giving
     */
    public function analyseStep4(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);
        $agentPrompt = AgentPrompt::getActivePrompt('advice_classifier');
        
        $result = $this->kimiApiService->complete($agentPrompt->system_prompt, $session->matched_transcription);
        $parsed = json_decode($result['content'], true);

        if (!$parsed || !isset($parsed['category'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal klasifikasi advice: ' . ($result['content'] ?? 'Respons tidak valid'),
            ]);
        }

        $session->update(['advice_category' => $parsed['category']]);

        return response()->json([
            'success' => true,
            'step'    => 4,
            'data'    => $parsed,
        ]);
    }

    /**
     * STEP 5: Karakter Relasi
     */
    public function analyseStep5(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);
        $agentPrompt = AgentPrompt::getActivePrompt('character_classifier');
        
        $result = $this->kimiApiService->complete($agentPrompt->system_prompt, $session->matched_transcription);
        $parsed = json_decode($result['content'], true);

        if (!$parsed || !isset($parsed['category'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal klasifikasi karakter: ' . ($result['content'] ?? 'Respons tidak valid'),
            ]);
        }

        $session->update(['character_category' => $parsed['category']]);

        return response()->json([
            'success' => true,
            'step'    => 5,
            'data'    => $parsed,
        ]);
    }

    /**
     * STEP 6: Intonasi & Insights
     */
    public function analyseStep6(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
        ]);

        $session = ChatSession::findOrFail($validated['session_id']);

        $promptInt = AgentPrompt::getActivePrompt('intonation_detector');
        $input = "Teks Rapih: " . $session->refined_transcription . "\n\nTeks Matched: " . $session->matched_transcription;
        $resInt = $this->kimiApiService->complete($promptInt->system_prompt, $input);
        $parsedInt = json_decode($resInt['content'], true);
        
        $promptIns = AgentPrompt::getActivePrompt('kimi_insights');
        $resIns = $this->kimiApiService->complete($promptIns->system_prompt, $session->matched_transcription);
        $parsedIns = json_decode($resIns['content'], true);

        if (!$parsedInt || !$parsedIns) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan insight: ' . ($resInt['content'] ?? $resIns['content'] ?? 'Respons tidak valid'),
            ]);
        }

        $session->update([
            'intonation_analysis' => $parsedInt['intonation'] ?? '-',
            'summary_domain'      => $parsedIns['summary'] ?? '-',
            'aim_target'          => $parsedIns['aim'] ?? '-',
            'suggestions'         => $parsedIns['suggestion'] ?? '-',
        ]);

        $content = "### 📊 Hasil Analisis Akhir\n\n";
        $content .= "**1. Klasifikasi Advice:** " . ($session->advice_category ?? '-') . "\n";
        $content .= "**2. Karakter Relasi:** " . ($session->character_category ?? '-') . "\n";
        $content .= "**3. Intonasi Terdeteksi:** " . ($session->intonation_analysis ?? '-') . "\n\n";
        $content .= "--- \n";
        $content .= "### 🧠 BIMA Insights\n";
        $content .= "**Summary Domain:** " . ($session->summary_domain ?? '-') . "\n";
        $content .= "**Arah Tujuan:** " . ($session->aim_target ?? '-') . "\n";
        $content .= "**Saran Perbaikan:** " . ($session->suggestions ?? '-') . "\n";

        $session->messages()->create([
            'role'    => 'assistant',
            'content' => $content,
        ]);

        return response()->json([
            'success' => true,
            'step'    => 6,
            'data'    => [
                'intonation' => $parsedInt,
                'insights'   => $parsedIns
            ],
            'full_message' => $content
        ]);
    }

    /**
     * POST /chat/transcribe
     * Accept a raw audio file blob, transcribe it, and return the text.
     */
    public function transcribeAudio(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,ogg,wav,mp3,mp4,m4a,aac|max:20480', // 20MB max
        ]);

        $transcription = $this->transcriptionService->transcribeAudio($request->file('audio'));

        return response()->json([
            'success'       => true,
            'transcription' => $transcription,
        ]);
    }

    /**
     * GET /chat/session/{session}/messages
     * Fetch message history for a session.
     */
    public function sessionMessages(ChatSession $session): JsonResponse
    {
        abort_if($session->user_id !== Auth::id(), 403);

        return response()->json([
            'success'  => true,
            'messages' => $session->messages()->orderBy('created_at', 'asc')->get(),
            'logs'     => $session->agentWorkflowLogs,
        ]);
    }

    /**
     * DELETE /chat/session/{session}
     * Delete a session and all its messages.
     */
    public function deleteSession(ChatSession $session): JsonResponse
    {
        if ((int)$session->user_id !== (int)Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak berhak menghapus sesi ini.'
            ], 403);
        }

        $session->messages()->delete();
        $session->agentWorkflowLogs()->delete();
        $session->delete();

        return response()->json(['success' => true]);
    }
    
    /**
     * GET /chat/session/{id}/data
     * Return raw session data for Alpine state.
     */
    public function getSessionData($id): JsonResponse
    {
        $session = ChatSession::findOrFail($id);
        if ((int)$session->user_id !== (int)Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'session' => [
                'raw_transcription'   => $session->raw_transcription ?? '-',
                'refined_transcription' => $session->refined_transcription ?? '-',
                'matched_transcription' => $session->matched_transcription ?? '-',
                'advice_category'     => $session->advice_category ?? '-',
                'character_category'  => $session->character_category ?? '-',
                'intonation_analysis' => $session->intonation_analysis ?? '-',
                'summary_domain'      => $session->summary_domain ?? '-',
                'aim_target'          => $session->aim_target ?? '-',
                'suggestions'         => $session->suggestions ?? '-',
            ]
        ]);
    }

    /**
     * GET /chat/sessions
     * List all sessions for the current user.
     */
    public function listSessions(): JsonResponse
    {
        $sessions = ChatSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }
}
