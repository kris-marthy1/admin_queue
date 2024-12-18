<?php
namespace App\Http\Controllers;

use App\Models\DynamicTable;
use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DatabaseController extends Controller
{
    
    

    public function store(Request $request)
    {
        $request->validate([
            'arrived_at' => 'required|date',
            'queue_id' => 'required|string',
            'window_name' => 'required|string',
        ]);
        
        $window = DB::connection('clientone')->table('window')
        ->where('window_name', $request->window_name)
        ->first();

        if (!$window) {
            return response()->json(['error' => 'Invalid window'], 400);
        }

        $window_id = $window->window_id;

        // Assuming you have a Report model to handle the saving
        $report = Reports::create([
            'arrived_at' => $request->arrived_at,
            'queue_id' => $request->queue_id,
            'window_id' => $window_id,
        ]);
    
        return response()->json(['message' => 'Report created successfully', 'report' => $report]);
    }
    
    // Method to return all table names
    public function getTables()
    {
        // $databaseName = env('DB_DATABASE');
        
        // Get all table names
        // $tables = collect(DynamicTable::getAllTableNames())
            // ->map(function($table) {
                // Convert stdClass to array and get the first value
            //     $array = (array)$table;
            //     return array_values($array)[0];
            // })
            // ->filter(function($table) {
            //     // Define the excluded tables
            //     $excludedTables = [
            //         'migrations', 
            //         'account_infos', 
            //         'history', 
            //         'cache', 
            //         'cache_locks', 
            //         'window', 
            //         'model_has_permissions', 
            //         'model_has_roles', 
            //         'permissions', 
            //         'roles', 
            //         'role_has_permissions',
            //         'password_reset_tokens',
            //         'sessions',
            //         'user_infos',
            //         'user_sessions'
            //     ];
                
            //     return !in_array($table, $excludedTables);
            // })
            // ->values()
            // ->all();
            // Get the currently logged-in user
            $user = Auth::user();

            // Fetch the windows associated with the logged-in user
            $tables = DB::table('window')
                ->join('account_infos', 'window.window_id', '=', 'account_infos.window_id')
                ->where('account_infos.account_id', $user->account_id)
                ->select('window.*')
                ->get();

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


// public function skip_queue(Request $request) {


//     $validated = $request->validate([
//         'queueId' => 'required|numeric',
//         'tableName' => 'required|string|max:255'
//     ]);

//     $queueId = $validated['queueId'];
//     $tableName = $validated['tableName'];


//     // Fetch the data from the specified table
//     $tableData = DynamicTable::getTableData($tableName);

//     // Find the record with the specified queue ID
//     $recordToDelete = $tableData->firstWhere('queue_id', $queueId);

//     // If the record does not exist, return a 404 response
//     if (!$recordToDelete) {
//         return response()->json(['message' => 'Record not found.'], 404);
//     }
    
//     // Delete the record
//     $deleted = DB::table($tableName)->where('queue_id', $queueId)->delete();
    

//     $window = DB::connection('clientone')->table('window')
//         ->where('window_name', $tableName)
//         ->first();
    
//     // Get the user queue details
//     // Validate inputs

//     if (!$window) {
//         return response()->json(['error' => 'Invalid window'], 400);
//     }

//     $window_id = $window->window_id;

//     // Attempt to delete from user_sessions
//     $sessionDeleted = DB::connection('clientone')->table('user_sessions')
//         ->where('window_id', $window_id)
//         ->where('queue_id', $queueId)
//         ->delete();


//     // Check if the delete was successful
//     if ($deleted && $sessionDeleted) {
//         return response()->json(['message' => 'Queue item skipped successfully.']);
//     } else {
//         return response()->json(['message' => 'Error deleting the record.'], 500);
//     }
// }
public function skip_queue(Request $request) {
    // Validate request data
    $validated = $request->validate([
        'queueId' => 'required|numeric',
        'tableName' => 'required|string|max:255'
    ]);

    $queueId = $validated['queueId'];
    $tableName = $validated['tableName'];

    \Log::info("Attempting to skip queue: queueId={$queueId}, tableName={$tableName}");

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Fetch the data from the specified table
        $tableData = DynamicTable::getTableData($tableName);
        $recordToDelete = $tableData->firstWhere('queue_id', $queueId);

        if (!$recordToDelete) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        // Delete the record from the dynamic table
        $deleted = DB::table($tableName)->where('queue_id', $queueId)->delete();
        if (!$deleted) {
            \Log::error("Failed to delete record from table: {$tableName} with queue_id={$queueId}");
            DB::rollBack(); // Rollback the transaction if deletion fails
            return response()->json(['message' => 'Error deleting the record from table.'], 500);
        }

        // Get the window details for validation
        $window = DB::connection('clientone')->table('window')
            ->where('window_name', $tableName)
            ->first();

        if (!$window) {
            \Log::error("Invalid window for tableName={$tableName}");
            DB::rollBack(); // Rollback the transaction if window is not found
            return response()->json(['error' => 'Invalid window'], 400);
        }

        $window_id = $window->window_id;

        // Attempt to delete from user_sessions
        $sessionDeleted = DB::connection('clientone')->table('user_sessions')
            ->where('window_id', $window_id)
            ->where('queue_id', $queueId)
            ->delete();

        if (!$sessionDeleted) {
            \Log::error("Failed to delete from user_sessions for queue_id={$queueId} and window_id={$window_id}");
            DB::rollBack(); // Rollback if deletion fails
            return response()->json(['message' => 'Error deleting from user_sessions.'], 500);
        }

        // Commit the transaction if all deletions were successful
        DB::commit();
        return response()->json(['message' => 'Queue item skipped successfully.']);

    } catch (\Exception $e) {
        DB::rollBack(); // Rollback the transaction in case of any unexpected error
        \Log::error("Error skipping queue: {$e->getMessage()}");
        return response()->json(['message' => 'Error deleting the record.'], 500);
    }
}


}
