<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    public function auctions() {
        return $this->hasMany(Auctions::class, 'owner_id', 'id');
    }

    public function transactions() {
        return $this->hasMany(Transactions::class, 'user_id', 'id');
    }

    public function won_auctions() {
        return $this->hasMany(Auctions::class, 'winner_id', 'id');
    }
}
