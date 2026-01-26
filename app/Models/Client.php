<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
   
    protected $fillable = [
        'name',
        'type',
        'phone',
        'email',
        's_number',
        'status',
        'updated_by',
        'created_by',
    ];

    // generate a unique serial number for every client
    public static function newSerialNumber()
    {
        $lastClient = self::orderBy('s_number', 'desc')->first();
        if ($lastClient && $lastClient->s_number) {
            $lastNumber = (int) $lastClient->s_number;
            $newNumber = str_pad($lastNumber + 1, 14, 'CLIENT00000000', STR_PAD_LEFT);
        } else {
            $newNumber = str_pad(1, 14, 'CLIENT00000000', STR_PAD_LEFT);
        }
        return $newNumber;
    }

    protected $casts = [
        'status' => 'boolean',
    ];

    public $timestamps = true;



}
