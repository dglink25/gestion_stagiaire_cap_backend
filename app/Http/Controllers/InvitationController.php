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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
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
                'email_binome' => 'required|email|unique:users,email',
            ]);

            $token = Str::random(60);

            $token1 = hash('sha256', Str::random(60));
            $token2 = hash('sha256', Str::random(60));

            $hashedToken = hash('sha256', $token);

            $Invitation = Invitation::create([
                'email' =>  $request->email,
                'role' =>  $request->role,
                'token' => $hashedToken,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            $InvitationBinome = Invitation::create([
                'email' =>  $request->email_binome,
                'role' =>  $request->role,
                'token' => $hashedToken,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($Invitation->email)->send(new BinomeInvitationMail($Invitation));
            Mail::to($InvitationBinome->email)->send(new BinomeInvitationMail($InvitationBinome));

            return response()->json([
                'message' => 'Invitations envoyées'
            ]);
        }

        else{
            $token = Str::random(60);

            $hashedToken = hash('sha256', $token);

            $Invitation = Invitation::create([
                'email' =>  $request->email,
                'role' =>  $request->role,
                'token' => $hashedToken,
                'invited_by' => $authUser->id,
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($Invitation->email)->send(new BinomeInvitationMail($Invitation));

            return response()->json([
                'message' => 'Invitation envoyée'
            ]);
        }

    }
}