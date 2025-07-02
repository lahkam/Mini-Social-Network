<?php

namespace App\Http\Controllers;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    public function __construct()
    {
        // Cette partie gère le middleware dans les routes, pas dans le constructeur
    }

    /**
     * Envoyer une invitation à un autre utilisateur
     */
    public function sendInvitation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invitee_email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'La validation a échoué',
                'errors' => $validator->errors()
            ], 422);
        }

        $inviter = Auth::user();
        $invitee = User::where('email', $request->invitee_email)->first();

        // Vérifie si l'utilisateur essaie de s'inviter lui-même
        if ($inviter->id === $invitee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas vous inviter vous-même'
            ], 400);
        }

        // Vérifie si une invitation existe déjà
        $existingInvitation = Invitation::where('inviter_id', $inviter->id)
            ->where('invitee_id', $invitee->id)
            ->first();

        if ($existingInvitation) {
            if ($existingInvitation->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation déjà envoyée et en attente'
                ], 400);
            }

            if ($existingInvitation->status === 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'utilisateur a déjà accès à vos publications'
                ], 400);
            }

            // Si refusée ou révoquée, met à jour l'invitation existante
            $existingInvitation->update([
                'status' => 'pending',
                'responded_at' => null,
                'sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $existingInvitation->load(['inviter', 'invitee']),
                'message' => 'Invitation renvoyée avec succès'
            ]);
        }

        // Créer une nouvelle invitation
        $invitation = Invitation::create([
            'inviter_id' => $inviter->id,
            'invitee_id' => $invitee->id,
            'status' => 'pending'
        ]);

        $invitation->load(['inviter', 'invitee']);

        return response()->json([
            'success' => true,
            'data' => $invitation,
            'message' => 'Invitation envoyée avec succès'
        ], 201);
    }

    /**
     * Accepter une invitation
     */
    public function acceptInvitation($invitationId): JsonResponse
    {
        $invitation = Invitation::findOrFail($invitationId);

        if ($invitation->invitee_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à accepter cette invitation'
            ], 403);
        }

        if (!$invitation->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'L\'invitation n\'est plus en attente'
            ], 400);
        }

        $invitation->accept();
        $invitation->load(['inviter', 'invitee']);

        return response()->json([
            'success' => true,
            'data' => $invitation,
            'message' => 'Invitation acceptée avec succès'
        ]);
    }

    /**
     * Refuser une invitation
     */
    public function declineInvitation($invitationId): JsonResponse
    {
        $invitation = Invitation::findOrFail($invitationId);

        if ($invitation->invitee_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à refuser cette invitation'
            ], 403);
        }

        if (!$invitation->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'L\'invitation n\'est plus en attente'
            ], 400);
        }

        $invitation->decline();
        $invitation->load(['inviter', 'invitee']);

        return response()->json([
            'success' => true,
            'data' => $invitation,
            'message' => 'Invitation refusée avec succès'
        ]);
    }

    /**
     * Révoquer l'accès (supprimer l'invitation)
     */
    public function revokeAccess($invitationId): JsonResponse
    {
        $invitation = Invitation::findOrFail($invitationId);

        if ($invitation->inviter_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé à révoquer cette invitation'
            ], 403);
        }

        if ($invitation->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Peut uniquement révoquer les invitations acceptées'
            ], 400);
        }

        $invitation->revoke();
        $invitation->load(['inviter', 'invitee']);

        return response()->json([
            'success' => true,
            'data' => $invitation,
            'message' => 'Accès révoqué avec succès'
        ]);
    }

    /**
     * Obtenir les invitations en attente pour l'utilisateur authentifié
     */
    public function getPendingInvitations(): JsonResponse
    {
        $user = Auth::user();
        
        $invitations = $user->pendingInvitations()
            ->with(['inviter'])
            ->orderBy('sent_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $invitations,
            'message' => 'Invitations en attente récupérées avec succès'
        ]);
    }

    /**
     * Obtenir les invitations envoyées par l'utilisateur authentifié
     */
    public function getSentInvitations(): JsonResponse
    {
        $user = Auth::user();
        
        $invitations = $user->sentInvitations()
            ->with(['invitee'])
            ->orderBy('sent_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $invitations,
            'message' => 'Invitations envoyées récupérées avec succès'
        ]);
    }
}