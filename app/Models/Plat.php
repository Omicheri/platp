<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Sagalbot\Encryptable\Encryptable;
use Illuminate\Contracts\Encryption\DecryptException;

class Plat extends Model
{
    use HasFactory,Notifiable;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'titre',
        'recette',
        'likes',
    ];

    public function favoris(){
        return $this->belongsToMany(User::class,'favoris');
    }

    use Encryptable;
    protected $encryptable = ['recette',];
    public function getRecetteAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            return $value; // Retourne la valeur brute si la décryption échoue
        }
    }
}
