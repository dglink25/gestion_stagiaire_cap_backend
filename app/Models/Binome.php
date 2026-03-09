<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Binome extends Model{
    protected $fillable = [
        'id_binome1',
        'id_binome2',
        'id_mentor',
    ];

    public function user1() {
        return $this->belongsTo(User::class, 'id_binome1');
    }

    public function user2() {
        return $this->belongsTo(User::class, 'id_binome2');
    }

    public function mentor() {
        return $this->belongsTo(User::class, 'id_mentor');
    }
}