<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\DynamicTable;


class StaffController extends Controller
{
    
    public function dashboard(Request $request) {
        $historyRecords = DB::table('history')
            ->join('window', 'history.window_id', '=', 'window.window_id')
            ->select(
                'history.report_id', 
                'history.queue_id', 
                'history.arrived_at', 
                'window.window_name', 
                'history.created_at'
            )
            ->get();
    
        return view('estab_pages/estab_dashboard', compact('historyRecords'));
    }
    
    

    public function showStaffAccounts()
    {
        // Get staff accounts
        $staffAccounts = AccountInfo::role('staff', 'web')->get();
    
        // Get filtered tables
        $databaseName = env('DB_DATABASE');
        $tables = collect(DynamicTable::getAllTableNames())
            ->map(function ($table) {
                $array = (array) $table;
                return array_values($array)[0];
            })
            ->filter(function ($table) {
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
                    'user_sessions',
                ];
                return !in_array($table, $excludedTables);
            })
            ->values()
            ->all();
    
        // Add `window_name` to each staff account
        $staffAccounts = $staffAccounts->map(function ($staffAccount) {
            $window = DB::connection('clientone')
                ->table('window')
                ->where('window_id', $staffAccount->window_id)
                ->first();
    
            $staffAccount->window_name = $window ? $window->window_name : null;
    
            return $staffAccount;
        });
    
       return view('estab_pages/estab_manage_staff', compact('staffAccounts', 'tables'));

    }

public function addStaffAccount(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'account_user' => 'required|unique:account_infos,account_user',
        'account_password' => 'required|string|min:8',
        'account_name' => 'required|string',
        'window_name' => 'required|exists:window,window_name', // Corrected validation rule
    ]);

    try {
        // Find the window_id based on the window_name
        $window = DB::table('window')->where('window_name', $request->input('window_name'))->first();
        
        if (!$window) {
            return redirect()->back()
                ->with('error', 'Window not found')
                ->withInput($request->except('account_password'));
        }

        // Create the account with the found window_id
        $AccountInfo = AccountInfo::create([
            'account_user' => $request->account_user,
            'account_password' => Hash::make($request->account_password),
            'account_name' => $request->account_name,
            'window_id' => $window->window_id,
        ]);
        
        // Assign the staff role
        $AccountInfo->assignRole('staff');
        
        return redirect()->back()->with('success', 'New staff account added successfully!'.$AccountInfo);
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error creating account: ' . $e->getMessage())
            ->withInput($request->except('account_password'));
    }
}

    
public function updateAccount(Request $request, $id)
{
    $validated = $request->validate([
        'account_user' => 'required|string|max:255',
        'account_password' => 'nullable|string|min:8',
        'account_name' => 'required|string|max:255',
        'window_name' => 'required|exists:window,window_name'
    ]);

    $window = DB::table('window')->where('window_name',$validated['window_name'])->first();

    $account = AccountInfo::findOrFail($id);
    $account->account_user = $validated['account_user'];
    $account->account_name = $validated['account_name'];
    $account->window_id = $window->window_id;


    if (!empty($validated['account_password'])) {
        $account->account_password = Hash::make($validated['account_password']);
    }

    $account->save();

    return redirect()->back()->with('successUpd', 'Account updated successfully!');
}

public function deleteAccount($id)
    {
        // Find the account by its ID
        $account = AccountInfo::find($id);

        if ($account) {
            $account->delete();
            return redirect()->back()->with('successDel', 'Account deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Account not found.');
        }
    }

}
