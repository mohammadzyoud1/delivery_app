<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // ==================== Conversation List ====================

    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $user
            ->conversations()
            ->with([
                'participants',
                'order',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->latest()
            ->get();

        return inertia('Messages/Messages', [
            'conversations' => $conversations,
        ]);
    }

    // ==================== Conversation Access ====================

    private function canAccessConversation($user, $conversation)
    {
        // ==================== Admin Access ====================

        if ($user->hasRole('admin')) {
            return true;
        }

        // ==================== Participant Check ====================

        $isParticipant = $conversation
            ->participants()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return false;
        }

        // ==================== Support Conversation ====================

        if ($conversation->type === 'support') {
            return true;
        }

        // ==================== Delivery Conversation ====================

        if ($conversation->type === 'delivery') {
            $order = $conversation->order;

            if (!$order) {
                return false;
            }

            // ==================== Customer Access ====================

            if ($order->customer_id === $user->id) {
                return true;
            }

            // ==================== Delivery Access ====================

            return $order
                ->deliveryAssignment()
                ->where('delivery_user_id', $user->id)
                ->whereNull('completed_at')
                ->exists();
        }

        // ==================== Unknown Conversation Type ====================

        return false;
    }

    // ==================== Create Support Conversation ====================

    public function startSupport(Request $request)
    {
        $user = $request->user();

        // ==================== Find Existing Support Conversation ====================

        $conversation = $user
            ->conversations()
            ->where('type', 'support')
            ->first();

        // ==================== Create Conversation ====================

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'support',
            ]);

            $conversation->participants()->attach($user->id);
        }

        return redirect()->route('messages.show', $conversation);
    }

    // ==================== Show Conversation ====================

    public function show(
        Request $request,
        Conversation $conversation
    ) {
        $user = $request->user();

        // ==================== Check Access ====================

        if (!$this->canAccessConversation($user, $conversation)) {
            abort(403);
        }

        // ==================== Load Conversation ====================

        $conversation->load([
            'participants',
            'order',
            'messages.sender',
        ]);

        // ==================== Return Conversation ====================

        if ($user->hasRole('admin')) {
            return inertia('Admin/Conversation', [
                'conversation' => $conversation,
            ]);
        }

        return inertia('Messages/Conversation', [
            'conversation' => $conversation,
        ]);
    }

    // ==================== Send Message ====================

    public function send(
        Request $request,
        Conversation $conversation
    ) {
        $user = $request->user();

        // ==================== Check Access ====================

        if (!$this->canAccessConversation($user, $conversation)) {
            abort(403);
        }

        // ==================== Validate Message ====================

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        // ==================== Create Message ====================

        $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => $validated['message'],
        ]);

        // ==================== Return To Conversation ====================

        return redirect()->route(
            'messages.show',
            $conversation
        );
    }

    // ==================== Admin Conversation List ====================

    public function adminIndex(Request $request)
    {
        // ==================== Admin Access ====================

        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        // ==================== Support Conversations ====================

        $conversations = Conversation::query()
            ->where('type', 'support')
            ->with([
                'participants',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->latest()
            ->get();

        return inertia('Admin/Messages', [
            'conversations' => $conversations,
        ]);
    }
}
