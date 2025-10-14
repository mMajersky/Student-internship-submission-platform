<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Ak názov tabuľky nie je "companies", nastav ho
    // protected $table = 'companies';

    // Primárny kľúč
    protected $primaryKey = 'id';

    // Ak primárny kľúč nie je auto-increment integer, treba nastaviť $incrementing a $keyType
    public $incrementing = true;
    protected $keyType = 'int';

    // Ak nechceš, aby Laravel automaticky spracovával created_at a updated_at
    public $timestamps = true;

    // Hromadne priraditeľné polia
    protected $fillable = [
        'name',
        'statutary',
        'address_id',
        'user_id',
    ];

    // Príklad vzťahu na adresu (ak máš model Address)
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Príklad vzťahu na používateľa (ak máš model User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
