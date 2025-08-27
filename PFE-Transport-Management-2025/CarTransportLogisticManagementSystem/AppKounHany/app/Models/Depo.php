<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depo extends Model
{
    use HasFactory;

    protected $table = 'depo';

    protected $fillable = [
        'name',
        'email'
    ];

    public function transports()
    {
        return $this->hasMany(Transport::class, 'id_depo');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'id_depo');
    }
}