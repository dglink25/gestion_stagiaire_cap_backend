<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Binome;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BinomeInvitationMail;


class RegisteredUserController extends Controller{
    
    public function store(Request $request): \Illuminate\Http\JsonResponse{

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'binome' => ['required', 'string', 'max:6'],
        ]);

        if($request->binome === "oui"){

            $request->validate([
                'name2' => ['required', 'string', 'max:255'],
                'email2' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password2' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role' => "stagiaire",
            ]);

            $token = Str::random(60);

            $hashedToken = hash('sha256', $token);

            $user2 = User::create([
                'name' => $request->name2,
                'email' => $request->email2,
                'password' => Hash::make($request->string('password2')),
                'role' => "stagiaire",
                'accepted' => false,
                'invite_token' => $hashedToken,
                'id_user_invite' => $user->id
            ]);

            Binome::create([
                'id_binome1' => $user->id,
                'id_binome2' => $user2->id
            ]);

            Mail::to($user2->email)->send(new BinomeInvitationMail($user2));

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'message' => 'Compte créé. Invitation envoyée.'
            ]);
        }

        else{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role'    => "stagiaire",
            ]);

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'message' => 'Inscription avec succès, attendez l\'attribution d\'un mentor.'
            ]);
        }

    }
}
