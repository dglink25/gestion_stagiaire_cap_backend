<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'accepted',
        'accepted_at',
        'id_user_invite',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array  {
        return [
            'email_verified_at' => 'datetime',
            'accepted_at' => 'datetime',
            'accepted' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function binome1() {
        return $this->hasOne(Binome::class, 'id_binome1');
    }

    public function binome2() {
        return $this->hasOne(Binome::class, 'id_binome2');
    }

    public function mentoredBinomes() {
        return $this->hasMany(Binome::class, 'id_mentor');
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isMentor() {
        return $this->role === 'mentor';
    }

    public function isStagiaire() {
        return $this->role === 'stagiaire';
    }

    public function isAccepted() {
        return $this->accepted === true;
    }
}