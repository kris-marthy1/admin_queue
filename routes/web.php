
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\LoginVerifyController;


Route::post('/edit_window/{tableName}', [MainController::class, 'edit'])->name('windows.edit');
Route::put('/update_window', [MainController::class, 'update'])->name('windows.update');
Route::delete('/windows/delete-column', [MainController::class, 'deleteColumn'])->name('windows.deleteColumn');
Route::get('/edit_window/{tableName}', [MainController::class, 'deleteColumnPage'])->name('windows.deleteColumnPage');

Route::get('/staff', [StaffController::class, 'showStaffAccounts']);
Route::post('/addStaffAccount', [StaffController::class, 'addStaffAccount']);
Route::put('/editStaffAccount/{id}', [StaffController::class, 'updateAccount']);
Route::delete('/deleteStaffAccount/{id}', [StaffController::class, 'deleteAccount'])->name('deleteStaffAccount');


// move from window to reports table
Route::post('/reports', [DatabaseController::class, 'store']);


// Route to get table names and display the form with buttons
Route::post('/tables', [DatabaseController::class, 'getTables'])->name('tables');

// Route to get data from the selected table (will return JSON data)
// Route::post('/view-table-data', [DatabaseController::class, 'getTableData'])->name('view.table.data');
Route::post('/view.table.data', [DatabaseController::class, 'getTableData']);
Route::post('/manage_queue', [DatabaseController::class, 'getTableDataView']);
Route::get('/manage_queue/{table_name}', [DatabaseController::class, 'showTableData'])->name('showTableData');



Route::get('/create-table', function () {
    return view('create_table');
});


// Route::get('/', function () {
//     return view('welcome');
// });

Route::delete('queues/{queueId}/{tableName}', [DatabaseController::class, 'skip_queue']);

//===============================
// Establishment routes
Route::get('/estab_dashboard', function () {
    return view('estab_pages/estab_dashboard');
});
Route::get('/estab_manage_queue', function () {
    return view('estab_pages/estab_manage_queue');
});

// GET for displaying windows
// Route::get('/add_window', [TableController::class, 'add_window']);
// Route::get('/delete_window', [TableController::class, 'delete_window']);
Route::get('/add_window', [MainController::class, 'estab_add_windows']);
Route::get('/add_windows_form', [MainController::class, 'estab_add_windows_form']);
Route::post('/estab_manage_window', [MainController::class, 'estab_manage_window']);

// POST for window update and delete actions
Route::post('/tables/update', [MainController::class, 'updateTableName'])->name('tables.update');
Route::post('/tables/delete', [MainController::class, 'deleteTable'])->name('tables.delete');
// create window
Route::post('/create-table', [MainController::class, 'createTable'])->name('createTable');

// Other routes
Route::controller(LoginVerifyController::class)->group(function (){
    Route::post('verify_login', 'login');
});




// Route::controller(LoginVerifyController::class)->group(function () {
//     Route::get('dashboard', [LoginVerifyController::class, 'dashboard'])->name('dashboard');
//     Route::post('login', [LoginVerifyController::class, 'login'])->name('login');
// });
Route::controller(LoginVerifyController::class)->group(function () {
    // Route for displaying the login form (accessible without being logged in)
    Route::get('/', 'showLoginForm')->middleware('auth.session')->name('login');

    // Route for handling the login form submission
    Route::post('/login', 'login')->name('login.submit');

    // Protect the dashboard route with the 'auth.session' middleware
    Route::get('/dashboard', 'dashboard')->middleware('auth.session')->name('dashboard');

    // Route for logging out the user
    Route::get('/logout', 'logout')->name('logout');
});