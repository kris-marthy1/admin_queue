<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DynamicTable extends Model
{               
    // Disable timestamps for dynamic tables
    // public $timestamps = false;

    // Fetch data from a specific table
    public static function getTableData(string $tableName)
    {
        return DB::table($tableName)->get();
    }

    // Fetch all table names
    public static function getAllTableNames()
    {
        return DB::select('SHOW TABLES');
    }
}