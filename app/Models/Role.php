<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;

class Role extends Model
{
    use HasFactory;
    use Blameable;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }
}
