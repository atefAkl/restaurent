<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;

class Permission extends Model
{
    use HasFactory;
    use Blameable;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
