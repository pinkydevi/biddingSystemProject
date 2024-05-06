<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auctions extends Model
{
    use HasFactory;

    protected $table = 'auctions';

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function winner() {
        return $this->belongsTo(User::class, 'winner_id', 'id');
    }
}
