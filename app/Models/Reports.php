<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;
    protected $table='history';

    protected $fillable = [
        'arrived_at',
        'email',
        'school_id',
        'window_name',
    ];

}
