<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountInfo extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $primaryKey = 'account_id';
    protected $table = 'account_infos';
    protected $fillable = [
        'account_user',
        'account_password',
        'account_name',
        'window_id', // Add this line
    ];

    protected $hidden = [
        'account_password',
    ];

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the window associated with the account.
     */
    public function window(): BelongsTo
    {
        return $this->belongsTo(Window::class, 'window_id', 'window_id');
    }
}