<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Window;
use Illuminate\Support\Str;

class MainController extends Controller
{
   
    public function __contruct()
    {
        $this->middleware('permission:manage_window', ['only' => ['estab_add_windows', 'estab_manage_window', ''] ]);
    }

// DISPLAY TABLES IN ADD WINDOWS ==========================================================================
    public function estab_add_windows()
    {
        // Get all table names from the database
        $tables = DB::select('SHOW TABLES');

        // Extract the table names (assuming MySQL)
        $tables = array_map(function($table) {
            return current((array) $table); // Replace with your actual DB name
        }, $tables); 

        // Exclude specific tables
        $excludedTables = ['migrations', 'account_infos', 'history', 'cache', 'cache_locks', 'window', 'model_has_permissions', 'model_has_roles', 'permissions', 'roles', 'role_has_permissions','password_reset_tokens', 'sessions', 'user_infos'];        
        $filteredTables = array_filter($tables, function($table) use ($excludedTables) {
            return !in_array($table, $excludedTables);
        });

        // Display the filtered table names
        return view('estab_pages/estab_add_windows', ['tables' => $filteredTables]);
    }
// DISPLAY TABLES IN ADD WINDOWS ==========================================================================
public function estab_add_windows_form()
    {
        return view('estab_pages/estab_add_windows_form');
    }




// DISPLAY TABLES IN MANAGE WINDOWS ==========================================================================
    public function estab_manage_window()
    {
        // Get all table names from the database
        $tables = DB::select('SHOW TABLES');

        // Extract the table names (assuming MySQL)
        $tables = array_map(function($table) {
            return current((array) $table); // Replace with your actual DB name
        }, $tables);

        // Exclude specific tables
        $excludedTables = [
            'migrations', 
            'account_infos', 
            'history', 
            'cache', 
            'cache_locks', 
            'window', 
            'model_has_permissions', 
            'model_has_roles', 
            'permissions', 
            'roles', 
            'role_has_permissions',
            'password_reset_tokens',
            'sessions',
            'user_infos'
        ];
        $filteredTables = array_filter($tables, function($table) use ($excludedTables) {
            return !in_array($table, $excludedTables);
        });

        // Display the filtered table names
        return view('estab_pages/estab_manage_window', ['tables' => $filteredTables]);
    }
// DISPLAY TABLES IN MANAGE WINDOWS ==========================================================================

public function createTable(Request $request)
{
    // Validate table name with a more permissive rule
    $validatedData = $request->validate([
        'table_name' => [
            'required',
            'string',
            'max:64',
            function ($attribute, $value, $fail) {
                // Check if table already exists after converting to snake_case
                $snakeCaseTableName = Str::snake($value);
                if (Schema::hasTable($snakeCaseTableName)) {
                    $fail('A table with this name already exists.');
                }
            }
        ],
        'entities' => 'sometimes|array',
        'entities.*.name' => [
            'required',
            'string',
            'distinct'
        ],
        'entities.*.type' => 'required|in:string,text,integer,bigInteger,float,double,decimal,boolean,date,datetime,timestamp,time'
    ]);

    // Convert table name to snake_case, handling spaces and special characters
    $tableName = Str::snake($validatedData['table_name']); 

    try {
        // Dynamically create the table
        Schema::create($tableName, function (Blueprint $table) {
            // Always have an ID column as primary key
            $table->id('queue_id');

            // Dynamically add entities
            $entities = request()->input('entities', []);
            
            foreach ($entities as $entity) {
                // Convert entity names to snake_case
                $name = Str::snake($entity['name']); 
                $type = $entity['type'];

                // Map Laravel schema types to Blueprint methods
                switch ($type) {
                    case 'string':
                        $table->string($name);
                        break;
                    case 'text':
                        $table->text($name);
                        break;
                    case 'integer':
                        $table->integer($name);
                        break;
                    case 'bigInteger':
                        $table->bigInteger($name);
                        break;
                    case 'float':
                        $table->float($name);
                        break;
                    case 'double':
                        $table->double($name);
                        break;
                    case 'decimal':
                        $table->decimal($name);
                        break;
                    case 'boolean':
                        $table->boolean($name);
                        break;
                    case 'date':
                        $table->date($name);
                        break;
                    case 'datetime':
                        $table->dateTime($name);
                        break;
                    case 'timestamp':
                        $table->timestamp($name);
                        break;
                    case 'time':
                        $table->time($name);
                        break;
                    default:
                        $table->string($name);
                }
            }
        });

        // Create a window record for the new table
        $window = new Window;
        $window->window_name = $tableName;
        $window->status = 'open';
        $window->save();

        return redirect('/add_window')->with('success', "Table '$tableName' created successfully");
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to create table: ' . $e->getMessage());
    }
}
// Delete TABLE =======================================================================

public function deleteTable(Request $request)
{
    $tableName = $request->table_name;

    // Drop the table if it exists
    if (Schema::hasTable($tableName)) {
        Schema::drop($tableName);
        
        // Delete the record directly from window table
        Window::where('window_name', $tableName)->delete();

        return redirect('/add_window')->with('success', 'Table and window record deleted successfully.');
    }

    return redirect('/add_window')->with('success', 'Records deleted successfully.');
}

 // UPDATE TABLE =====================================================================
 public function updateTableName(Request $request)
{
$oldTableName = $request->old_table_name;
$newTableName = $request->new_table_name;

// If the new table name exists, redirect back with an error
if (Schema::hasTable($newTableName)) {
    return redirect()->back()->with('error', 'The table name "' . $newTableName . '" already exists.');
}

// If the old table name exists, rename the table
if (Schema::hasTable($oldTableName)) {
    // Rename the table
    Schema::rename($oldTableName, $newTableName);

    // Update the window_name in the Window table to match the new table name
    Window::where('window_name', $oldTableName)
          ->update(['window_name' => $newTableName]);

    return redirect('/add_window')->with('success', 'Table renamed and window name updated successfully.');
} else {
    return redirect('/add_window')->with('error', 'Table does not exist.');
}
}

 




}// ending
