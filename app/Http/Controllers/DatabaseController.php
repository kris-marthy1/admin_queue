<?php
namespace App\Http\Controllers;

use App\Models\DynamicTable;
use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DatabaseController extends Controller
{
    
    

    public function store(Request $request)
    {
        $request->validate([
            'arrived_at' => 'required|date',
            'school_id' => 'required|string',
            'email' => 'required|string',
            'window_name' => 'required|string',
        ]);
    
        // Assuming you have a Report model to handle the saving
        $report = Reports::create([
            'arrived_at' => $request->arrived_at,
            'school_id' => $request->school_id,
            'email' => $request->email,
            'window_name' => $request->window_name,
        ]);
    
        return response()->json(['message' => 'Report created successfully', 'report' => $report]);
    }
    
    // Method to return all table names
    public function getTables()
    {
        $databaseName = env('DB_DATABASE');
        
        // Get all table names
        $tables = collect(DynamicTable::getAllTableNames())
            ->map(function($table) {
                // Convert stdClass to array and get the first value
                $array = (array)$table;
                return array_values($array)[0];
            })
            ->filter(function($table) {
                // Define the excluded tables
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
                
                return !in_array($table, $excludedTables);
            })
            ->values()
            ->all();
    
        return view('estab_pages/estab_manage_window', compact('tables'));
    }


    public function getTableData(Request $request)
    {
        $tableName = $request->input('table_name');

        // Validate if table name is provided
        if (!$tableName) {
            return response()->json(['error' => 'Table name is required'], 400);
        }
    
        // Fetch data from the specified table
        try {
            $tableData = DynamicTable::getTableData($tableName);

            // Return table data as JSON
            return response()->json(['tableData' => $tableData]);
            // return view('estab_pages/estab_manage_queue', compact('tableData', 'tableName'));
        } catch (\Exception $e) {
            // Handle error gracefully, log the error, and return JSON response
            return response()->json(['error' => 'Error fetching table data: ' . $e->getMessage()], 500);
        }
    }


    public function getTableDataView(Request $request)
    {
        $tableName = $request->input('table_name');
        
        // Redirect to the route that accepts table_name as a parameter
        return redirect()->route('showTableData', ['table_name' => $tableName]);
    }

    public function showTableData($table_name)
    {
        // Pass the selected table name to the Blade view
        return view('estab_pages.estab_manage_queue', ['tableName' => $table_name]);
    }


public function skip_queue($queueId, $tableName) {

    // Fetch the data from the specified table
    $tableData = DynamicTable::getTableData($tableName);

    // Find the record with the specified queue ID
    $recordToDelete = $tableData->firstWhere('queue_id', $queueId);

    // If the record does not exist, return a 404 response
    if (!$recordToDelete) {
        return response()->json(['message' => 'Record not found.'], 404);
    }

    // Delete the record
    $deleted = DB::table($tableName)->where('queue_id', $queueId)->delete();

    // Check if the delete was successful
    if ($deleted) {
        return response()->json(['message' => 'Queue item skipped successfully.']);
    } else {
        return response()->json(['message' => 'Error deleting the record.'], 500);
    }
}




}
