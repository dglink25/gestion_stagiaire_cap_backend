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

class RegisteredUserController extends Controller{
    
    public function store(Request $request): Response{
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'binome' => ['required', 'string', 'max:6'],
        ]);

        if($request->binome ==="oui"){
            $request->validate([
                'name2' => ['required', 'string', 'max:255'],
                'email2' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password2' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role'    => "stagiaire",
            ]);
            $user2 = User::create([
                'name' => $request->name2,
                'email' => $request->email2,
                'password' => Hash::make($request->string('password2')),
                'role'    => "stagiaire",
                'accepted' => true,
            ]);

            Binome::create([
                'id_binome1' => $user->id,
                'id_binome2' => $user2->id
            ]);
        }

        else{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'role'    => "stagiais",
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
