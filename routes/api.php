<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\InvitationController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/accept-invitation/{token}', function ($token) {

    $user = User::where('invite_token', $token)->firstOrFail();

    $user->accepted = true;
    $user->accepted_at = now();
    $user->invite_token = null;
    $user->save();

    return response()->json([
        'message' => 'Invitation acceptée'
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/invite-user', [InvitationController::class, 'invite']);

});

require __DIR__.'/auth.php';