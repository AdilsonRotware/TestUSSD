<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    // Definindo os campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id', // Relacionamento com o usuário
        'date',
        'time',
        'reason',
        'status', // Pode ser 'pendente', 'aceita', 'rejeitada'
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
