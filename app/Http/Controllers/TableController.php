<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Window;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
   
     
 
 
     // error handler for post and get routing for updating and deleting table name AHAHHAHAAH
    //  public function add_window(){ 
    //       // Get all table names from the database
    //       $tables = DB::select('SHOW TABLES');
 
    //       // Extract the table names (assuming MySQL)
    //       $tables = array_map(function($table) {
    //           return current((array) $table); // Replace with your actual DB name
    //       }, $tables);
  
    //       // Exclude specific tables
    //       $excludedTables = ['migrations', 'account_infos', 'reports', 'cache', 'cache_locks', 'window', 'model_has_permissions', 'model_has_roles', 'permissions', 'roles', 'role_has_permissions'];
    //       $filteredTables = array_filter($tables, function($table) use ($excludedTables) {
    //           return !in_array($table, $excludedTables);
    //       });
  
    //       // Display the filtered table names
    //       return view('estab_pages/estab_add_windows', ['tables' => $filteredTables]);
 
    //  }
    //  public function delete_window(){
    //      // Get all table names from the database
    //      $tables = DB::select('SHOW TABLES');
 
    //      // Extract the table names (assuming MySQL)
    //      $tables = array_map(function($table) {
    //          return current((array) $table); // Replace with your actual DB name
    //      }, $tables);
 
    //      // Exclude specific tables
    //      $excludedTables = ['migrations', 'account_infos', 'reports', 'cache', 'cache_locks', 'window', 'model_has_permissions', 'model_has_roles', 'permissions', 'roles', 'role_has_permissions'];
    //      $filteredTables = array_filter($tables, function($table) use ($excludedTables) {
    //          return !in_array($table, $excludedTables);
    //      });
 
    //      // Display the filtered table names
    //      return view('estab_pages/estab_add_windows', ['tables' => $filteredTables]);
 
    // }





}
