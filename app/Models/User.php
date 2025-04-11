<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    // Os campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',  // Adicionar o campo 'phone' para o número de telefone
        'is_supervisor',  // Adicionar o campo 'is_supervisor' para definir o supervisor
    ];

    // Campos que devem ser ocultados da resposta JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Campos que devem ser convertidos para tipos específicos
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relacionamento com as reuniões do usuário
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
