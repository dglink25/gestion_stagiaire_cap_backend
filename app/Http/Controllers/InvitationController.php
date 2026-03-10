<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BinomeInvitationMail;

class InvitationController extends Controller{

    public function invite(Request $request) {
        $authUser = $request->user();

        $request->validate([
            'email' => 'required|email|unique:users,email|unique:invitations,email',
            'role' => 'required|in:mentor,stagiaire',
            'en_binome' => 'required|boolean',
        ]);

        if($request->en_binome && $request->role !== 'stagiaire') {
            return response()->json([
                'message' => 'Seul un stagiaire peut être invité en binôme'
            ], 403);
        }

        if ($authUser->role === 'mentor' && $request->role !== 'stagiaire') {
            return response()->json([
                'message' => 'Un mentor peut inviter seulement un stagiaire'
            ], 403);
        }

        if (!in_array($authUser->role, ['admin','mentor'])) {
            return response()->json([
                'message' => 'Vous n’avez pas la permission'
            ], 403);
        }

        if($request->en_binome && $request->role == 'stagiaire') {
            $request->validate([
                'name_binome' => 'required|string|max:255',
                'email_binome' => 'required|email|unique:users,email|unique:invitations,email',
            ]);

            $token1 = Str::random(60);
            $token2 = Str::random(60);

            $hashedToken1 = hash('sha256', $token1);
            $hashedToken2 = hash('sha256', $token2);

            $invitation = Invitation::create([
                'email' =>  $request->email,
                'role' =>  $request->role,
                'token' => $hashedToken1,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            $invitationBinome = Invitation::create([
                'email' =>  $request->email_binome,
                'role' =>  $request->role,
                'token' => $hashedToken2,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($invitation->email)->send(new BinomeInvitationMail($invitation, $token1));
            Mail::to($invitationBinome->email)->send(new BinomeInvitationMail($invitationBinome, $token2));

            return response()->json([
                'message' => 'Invitations envoyées'
            ]);
        }

        else{
            $token = Str::random(60);

            $hashedToken = hash('sha256', $token);

            $invitation = Invitation::create([
                'email' =>  $request->email,
                'role' =>  $request->role,
                'token' => $hashedToken,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($invitation->email)->send(new BinomeInvitationMail($invitation, $token));

            return response()->json([
                'message' => 'Invitation envoyée'
            ]);
        }

    }
}