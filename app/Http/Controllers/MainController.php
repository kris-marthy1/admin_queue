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
public function edit($tableName)
    {
        // Check if table exists
        if (!Schema::hasTable($tableName)) {
            return redirect()->back()->with('error', 'Table does not exist.');
        }

        // Get column information
        $columns = DB::select("SHOW COLUMNS FROM $tableName");

        return view('estab_pages/estab_edit_windows_form', [
            'tableName' => $tableName,
            'columns' => $columns
        ]);
    }

    public function deleteColumnPage($tableName)
    {
        // Check if table exists
        if (!Schema::hasTable($tableName)) {
            return redirect()->back()->with('error', 'Table does not exist.');
        }

        // Get column information
        $columns = DB::select("SHOW COLUMNS FROM $tableName");

        return view('estab_pages/estab_edit_windows_form', [
            'tableName' => $tableName,
            'columns' => $columns
        ]);
    }


    public function update(Request $request)
    {
        $request->validate([
            'original_table_name' => 'required|string',
            'new_table_name' => 'required|string',
            'columns' => 'required|array',
            'types' => 'required|array',
        ]);

        $originalTableName = $request->input('original_table_name');
        $newTableName = $request->input('new_table_name');
        $columns = $request->input('columns');
        $types = $request->input('types');

        if (count($columns) !== count($types)) {
            return redirect()->back()->with('error', 'Mismatch between columns and types.');
        }

        try {
            // Step 1: Rename the table
            if ($originalTableName !== $newTableName) {
                DB::statement("RENAME TABLE `$originalTableName` TO `$newTableName`");
            }

            // Step 2: Update columns
            $currentColumns = DB::select("
                SELECT COLUMN_NAME, COLUMN_TYPE
                FROM information_schema.columns
                WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?
            ", [$newTableName, env('DB_DATABASE')]);

            $currentColumnsMap = collect($currentColumns)->keyBy('COLUMN_NAME');

            foreach ($columns as $index => $newColumnName) {
                $oldColumnName = array_keys($currentColumnsMap->toArray())[$index] ?? null;
                $newType = $types[$index];

                if (!$oldColumnName) {
                    // Add new column
                    DB::statement("ALTER TABLE `$newTableName` ADD COLUMN `$newColumnName` $newType");
                } elseif ($oldColumnName !== $newColumnName || $currentColumnsMap[$oldColumnName]->COLUMN_TYPE !== $newType) {
                    // Rename or change type of existing column
                    DB::statement("ALTER TABLE `$newTableName` CHANGE `$oldColumnName` `$newColumnName` $newType");
                }
            }
            return redirect('/add_window')
            ->with('success', "Service window `$newTableName` updated successfully.");
            
        
        } catch (\Exception $e) {
            return redirect('/edit_window/' . $oldTableName)
            ->with('error', "Table `$oldTableName` failed to update.");
        }
    }

    public function deleteColumn(Request $request)
    {
        $tableName = $request->input('table_name');
        $columnName = $request->input('column_name');
    
        try {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
    
            
            return redirect('/edit_window/' . $tableName)
            ->with('success', "Table `$tableName` updated successfully.");
        } catch (\Exception $e) {
            return redirect('/edit_window/' . $tableName)
            ->with('error', "Table `$tableName` failed to update.");
        }
    }

    

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
                    return redirect()->back()->with('error', 'Service window already exists!');
                }
            }
        ],
        'entities' => 'sometimes|array',
        'entities.*.name' => [
            'required',
            'string',
            'distinct'
        ],
        'entities.*.type' => 'required|in:VARCHAR(255),DECIMAL(10,2),INT'  // Validating the type
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

                // Map frontend data types to database types
                switch ($type) {
                    case 'VARCHAR(255)':
                        $table->string($name);  // Text
                        break;
                    case 'DECIMAL(10,2)':
                        $table->decimal($name, 10, 2);  // Number with 2 decimals
                        break;
                    case 'INT':
                        $table->integer($name);  // Whole number (Integer)
                        break;
                    default:
                        $table->string($name);  // Default to string type if invalid
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
        return redirect()->back()->with('error', 'Failed to create table: Make sure to check if the service window with the same name already exists.');
    }
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
