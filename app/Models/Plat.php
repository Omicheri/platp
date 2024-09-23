<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class Plat extends Model
{
    use HasFactory,Notifiable;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function recette(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }

    protected $fillable = [
        'titre',
        'recette',
        'likes',
        'Image',
        'user_id'
    ];

    public function favoris(){
        return $this->belongsToMany(User::class,'favoris');
    }




}
