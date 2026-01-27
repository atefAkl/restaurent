<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'room_id',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
