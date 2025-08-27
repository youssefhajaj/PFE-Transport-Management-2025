<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'isSecretaire',
        'isChef',
        'isResponsable',
        'isLogistic',
        'isMetteurAuMain',
        'isAssistantLogistic',
        'societe',
        'tree',
        'cachet',
        'id_depo',
        'depo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'isChef' => 'boolean',
            'isSecretaire' => 'boolean',
            'isResponsable' => 'boolean',
            'isLogistic' => 'boolean',
            'isMetteurAuMain' => 'boolean',
            'isAssistantLogistic' => 'boolean',
        ];
    }

    public function depo()
    {
        return $this->belongsTo(Depo::class, 'id_depo');
    }

    public function getIsChefAttribute(): bool
    {
        return (bool) $this->attributes['isChef'];
    }

    public function getIsSecretaireAttribute(): bool
    {
        return (bool) $this->attributes['isSecretaire'];
    }

    public function getIsResponsableAttribute(): bool
    {
        return (bool) $this->attributes['isResponsable'];
    }

    public function getIsLogisticAttribute(): bool
    {
        return (bool) $this->attributes['isLogistic'];
    }

    public function getRole()
    {
        if ($this->isChef) return 'chef';
        if ($this->isSecretaire) return 'secretaire';
        if ($this->isResponsable) return 'responsable';
        if ($this->isLogistic) return 'logistic';
        if ($this->isMetteurAuMain) return 'metteur_au_main'; 
        return 'user';
    }

    public function transportsAsName()
    {
        return $this->hasMany(Transport::class, 'name_id');
    }

    public function transportsAsSociete()
    {
        return $this->hasMany(Transport::class, 'societe_id');
    }

    public function transportsAsTree()
    {
        return $this->hasMany(Transport::class, 'tree_id');
    }

    public function transportsAsChef()
    {
        return $this->hasMany(Transport::class, 'chefname_id');
    }

    public function transportsAsResponsable()
    {
        return $this->hasMany(Transport::class, 'responsablename_id');
    }
}
