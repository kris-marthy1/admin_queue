<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Window extends Model
{
    use HasFactory;
    protected $table='window';

    protected $fillable =[
        'window_name',
        'status'
    ];

    /**
     * Get the accounts associated with the window.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(AccountInfo::class, 'window_id', 'window_id');
    }
}
