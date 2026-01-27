<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAccountant()
    {
        return $this->role === 'accountant';
    }

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function isKitchen()
    {
        return $this->role === 'kitchen';
    }

    public function canManageOrders()
    {
        return in_array($this->role, ['admin', 'seller']);
    }

    public function canViewReports()
    {
        return in_array($this->role, ['admin', 'accountant']);
    }

    public function canManageInventory()
    {
        return in_array($this->role, ['admin', 'accountant']);
    }

    public function canManageClients()
    {
        return in_array($this->role, ['admin', 'seller']);
    }

    public function canManageSettings()
    {
        return in_array($this->role, ['admin']);
    }

    public function canManageCashierSessions()
    {
        return in_array($this->role, ['admin', 'seller']);
    }
}
