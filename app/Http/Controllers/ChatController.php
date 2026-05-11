<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Document;
use App\Services\AgentOrchestratorService;
use App\Services\ConversationService;
use App\Services\DocumentService;
use App\Services\TranscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ChatController
 *
 * Responsibility: Accept HTTP requests and delegate to the appropriate Services.
 * Contains ZERO business logic – only request handling, validation, and response building.
 */
class ChatController extends Controller
{
    public function __construct(
        private readonly ConversationService      $conversationService,
        private readonly DocumentService          $documentService,
        private readonly TranscriptionService     $transcriptionService,
    ) {}

    /**
     * GET /chat
     * Render the main chat interface.
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $sessions = ChatSession::where('user_id', $user->id)
            ->with('document')
            ->latest()
            ->limit(20)
            ->get();

        $documents = Document::where('user_id', $user->id)
            ->where('status', 'processed')
            ->latest()
            ->get();

        // If a ?session=ID is specified in the URL, load that specific session
        $requestedId = $request->query('session');

        if ($requestedId) {
            $activeSession = ChatSession::where('id', $requestedId)
                ->where('user_id', $user->id)
                ->with(['document', 'messages' => fn($q) => $q->orderBy('created_at', 'asc')])
                ->first();
        }

        // Fallback: create a fresh session if no session is requested or found
        if (empty($activeSession)) {
            $activeSession = ChatSession::create([
                'user_id' => $user->id,
                'title'   => 'Sesi ' . now()->format('d M Y H:i'),
            ]);
            
            // Redirect to the new session's URL to keep it consistent
            return redirect()->route('chat.index', ['session' => $activeSession->id]);
        }

        return view('chat', compact('sessions', 'documents', 'activeSession'));
    }

    /**
     * POST /chat/session
     * Create a new chat session (optionally linked to a document).
     */
    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_id' => 'nullable|exists:documents,id',
            'title'       => 'nullable|string|max:255',
        ]);

        $session = ChatSession::create([
            'user_id'     => Auth::id(),
            'document_id' => $validated['document_id'] ?? null,
            'title'       => $validated['title'] ?? 'Sesi Baru ' . now()->format('H:i'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->load('document'),
        ]);
    }

    /**
     * POST /chat/analyse
     * Main endpoint: receive transcription, trigger multi-agent debate, return decision.
     */
    public function analyse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:chat_sessions,id',
            'transcription' => 'nullable|string|max:10000',
            'attached_files'=> 'nullable|array',
        ]);

        $session = ChatSession::with('document')->findOrFail($validated['session_id']);

        // Authorisation: user must own this session
        if ($session->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Sesi ini bukan milik Anda (User ID mismatch). Silakan buat sesi baru.'
            ], 403);
        }

        $result = $this->conversationService->process($session, $validated['transcription'] ?? '', $validated['attached_files'] ?? []);

        return response()->json([
            'success' => true,
            'type'    => $result['type'],
            'message' => $result['message'] ?? null,
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
     * POST /chat/upload-document
     * Upload a CSV dataset and link it to a session.
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_file' => 'required|file|mimes:csv,txt,pdf,docx,doc,xlsx,xls,jpg,png,jpeg|max:10240',
            'session_id'    => 'nullable|exists:chat_sessions,id',
        ]);

        $document = $this->documentService->uploadAndProcess(
            file: $request->file('document_file'),
            user: Auth::user(),
        );

        // Optionally link the document to an existing session
        if (! empty($validated['session_id'])) {
            ChatSession::where('id', $validated['session_id'])
                ->where('user_id', Auth::id())
                ->update(['document_id' => $document->id]);
        }

        return response()->json([
            'success'  => true,
            'document' => $document,
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
     * GET /chat/sessions
     * Return a list of the authenticated user's sessions.
     */
    public function sessions(): JsonResponse
    {
        $sessions = ChatSession::where('user_id', Auth::id())
            ->with('document')
            ->latest()
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'sessions' => $sessions]);
    }
    /**
     * DELETE /chat/session/{session}
     * Delete a session and all its messages.
     */
    public function deleteSession(ChatSession $session): JsonResponse
    {
        abort_if($session->user_id !== Auth::id(), 403, 'Akses ditolak.');

        $session->messages()->delete();
        $session->agentWorkflowLogs()->delete();
        $session->delete();

        return response()->json(['success' => true]);
    }
}
