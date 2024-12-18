<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Window;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


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
        $excludedTables = ['migrations', 'account_infos', 'history', 'cache', 'cache_locks', 'user_sessions', 'window', 'model_has_permissions', 'model_has_roles', 'permissions', 'roles', 'role_has_permissions','password_reset_tokens', 'sessions', 'user_infos'];        
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
            'user_infos',
            'user_sessions'

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
    if (!Schema::hasTable($tableName)) {
        return redirect()->back()->with('error', 'Table does not exist.');
    }

    // Get all columns
    $columns = DB::select("SHOW COLUMNS FROM $tableName");

    // Get the primary key column
    $primaryKey = DB::select("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'");
    $primaryKeyName = $primaryKey[0]->Column_name ?? null;

    // Filter out the primary key, updated_at, and created_at
    $filteredColumns = array_filter($columns, function ($column) use ($primaryKeyName) {
        return !in_array($column->Field, [$primaryKeyName, 'updated_at', 'created_at']);
    });

    return view('estab_pages/estab_edit_windows_form', [
        'tableName' => $tableName,
        'columns' => $filteredColumns
    ]);
}


public function deleteColumnPage($tableName)
{
    if (!Schema::hasTable($tableName)) {
        return redirect()->back()->with('error', 'Table does not exist.');
    }

    // Get all columns
    $columns = DB::select("SHOW COLUMNS FROM $tableName");

    // Get the primary key column
    $primaryKey = DB::select("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'");
    $primaryKeyName = $primaryKey[0]->Column_name ?? null;

    // Filter out the primary key
    $filteredColumns = array_filter($columns, function ($column) use ($primaryKeyName) {
        return !in_array($column->Field, [$primaryKeyName, 'updated_at', 'created_at']);
    });

    return view('estab_pages/estab_edit_windows_form', [
        'tableName' => $tableName,
        'columns' => $filteredColumns
    ]);
}
 
public function update(Request $request)
{
    DB::beginTransaction();
    try {
        $request->validate([
            'original_table_name' => 'required|string',
            'new_table_name' => 'required|string',
            'columns' => 'required|array'
        ]);

        $originalName = $request->original_table_name;
        $newName = $request->new_table_name;

        // Rename table if name changed
        if ($originalName !== $newName) {
            $newName =  strtolower($newName);
            $newName = Str::snake($newName);

            Schema::rename($originalName, $newName);
            // Update window name
            Window::where('window_name', $originalName)
                ->update(['window_name' => $newName]);
        }

        // Get existing columns
        $existingColumns = Schema::getColumnListing($newName);

        // Get primary key
        $primaryKey = DB::select("SHOW KEYS FROM $newName WHERE Key_name = 'PRIMARY'")[0]->Column_name ?? null;

        $columnsToKeep = ['created_at', 'updated_at'];
        foreach ($request->columns as $index => $columnName) {
            $snakeCaseColumnName = Str::snake($columnName);

            // Skip primary key
            if ($snakeCaseColumnName === $primaryKey) {
                $columnsToKeep[] = $snakeCaseColumnName;
                continue;
            }

            // Rename or add column
            if (in_array($snakeCaseColumnName, $existingColumns)) {
                $columnsToKeep[] = $snakeCaseColumnName;
            } else {
                Schema::table($newName, function (Blueprint $table) use ($snakeCaseColumnName) {
                    $table->string($snakeCaseColumnName)->nullable();
                });
                $columnsToKeep[] = $snakeCaseColumnName;
            }
        }

        // Remove columns not in the new schema
        $columnsToRemove = array_diff($existingColumns, $columnsToKeep);
        foreach ($columnsToRemove as $columnToRemove) {
            if (!in_array($columnToRemove, ['created_at', 'updated_at', $primaryKey])) {
                Schema::table($newName, function (Blueprint $table) use ($columnToRemove) {
                    $table->dropColumn($columnToRemove);
                });
            }
        }

        // Ensure created_at and updated_at exist
        Schema::table($newName, function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn($table->getTable(), 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::commit();
        return redirect('/add_window')->with('success', 'Window updated successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        // Log::error('Window Update Error: ' . $e->getMessage());
        return redirect('/add_window')->with('success', 'Window updated successfully');

    }
}




public function deleteColumn(Request $request)
{
    try {
        Schema::table($request->table_name, function (Blueprint $table) use ($request) {
            $table->dropColumn($request->column_name);
        });
        
        return redirect()->back()->with('success', 'Column deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete column: ' . $e->getMessage());
    }
}

public function createTable(Request $request)
{
    try {
        // Validate request
        $request->validate([
            'table_name' => 'required|string|max:255',
            'entities' => 'required|array',
            'entities.*.name' => 'required|string|max:255'
        ]);

        $tableName = $request->table_name;

        // Create table schema
        Schema::create($tableName, function (Blueprint $table) use ($request) {
            $table->id('queue_id');
            foreach ($request->entities as $entity) {
                // Convert column name to snake case
                $columnName = Str::snake($entity['name']);
                $table->string($columnName)->nullable();
            }
            $table->timestamps();
        });

        // Create a window record
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
