@extends('estab_template')

@section('content')

<!-- Button to open Add Account modal -->
<div class="flex justify-between items-center mb-4">
    <button data-modal-target="addAccountModal" data-modal-toggle="addAccountModal" 
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none">
        Add New Account
    </button>
</div>

<!-- Edit Account Modal -->
<div id="editAccountModal" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                    Edit Account
                </h3>
                <button type="button" data-modal-hide="editAccountModal" 
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <!-- Modal body -->
            <form id="editAccountForm" action="{{ url('/editStaffAccount/{id}') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6 p-6">
                    <input type="hidden" name="account_id" id="edit_account_id">
                    <div>
                        <label for="edit_account_user" class="block mb-2 text-sm font-medium">Username</label>
                        <input type="text" name="account_user" id="edit_account_user" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5" required>
                    </div>
                    <div>
                        <label for="edit_account_password" class="block mb-2 text-sm font-medium">Password</label>
                        <input type="password" name="account_password" id="edit_account_password" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5">
                    </div>
                    <div>
                        <label for="edit_account_name" class="block mb-2 text-sm font-medium">Account Name</label>
                        <input type="text" name="account_name" id="edit_account_name" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5" required>
                    </div>
                    <div>
                        <label for="edit_window_id" class="block mb-2 text-sm font-medium">Assign Window</label>
                        <select id="edit_window_id" name="window_name" class="bg-gray-50 border text-sm rounded-lg block w-full p-2.5" required>
                            <option value="">Select a window</option>
                            @foreach($tables as $table)
                                <option value="{{ $table }}">{{ $table }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Update Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Account Modal -->
<div id="addAccountModal" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                    Add New Account
                </h3>
                <button type="button" data-modal-hide="addAccountModal" 
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ url('addStaffAccount') }}" method="POST" class="space-y-6 p-6">
                @csrf
                <div>
                    <label for="account_user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Username</label>
                    <input type="text" name="account_user" id="account_user" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="account_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Password</label>
                    <input type="password" name="account_password" id="account_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="account_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Account Name</label>
                    <input type="text" name="account_name" id="account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="window_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Assign Window</label>
                    <select id="window_id" name="window_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select a window</option>
                        @foreach($tables as $table)
                            <option value="{{ $table }}">{{ $table }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger ">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-red-700 border border-black bg-gray-400 mb-3 p-1 rounded-lg">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('successDel')) 
    <div class="alert alert-success text-red-600 border border-green-400 bg-green-200 mb-3 p-1 rounded-lg">
        {{ session('successDel') }}
    </div>
@endif
@if (session('successUpd')) 
    <div class="alert alert-success text-yellow-700 border border-green-400 bg-green-200 mb-3 p-1 rounded-lg">
        {{ session('successUpd') }}
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success text-green-800 border  border-green-400 bg-green-200 mb-3 p-1 rounded-lg">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger text-red-600  text-gray-800 border border-black bg-gray-600 mb-3 p-1 rounded-lg">
        {{ session('error') }}
    </div>
@endif
<!-- Table for displaying accounts -->
<div class="overflow-x-auto">
    <table class="table-auto w-full text-sm text-left text-gray-500 bg-white border border-gray-200 shadow-md rounded-lg">
        <thead class="bg-gray-200 text-gray-700 uppercase text-lg font-semibold">
            <tr>
                <th class="px-4 py-2">Account Name</th>
                <th class="px-4 py-2">Account Username</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 text-lg">
            @foreach ($staffAccounts as $account)
            <tr class="border-b hover:bg-gray-100" data-id="{{ $account->account_id }}">
                <td class="px-4 py-2">{{ $account->account_name }}</td>
                <td class="px-4 py-2">{{ $account->account_user }}</td>
                <td class="px-4 py-2 flex">
                    <button data-modal-target="editAccountModal" data-modal-toggle="editAccountModal" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</button>
                    <form action="{{ url('/deleteStaffAccount/'.$account->account_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- @push('scripts')
<script>
    function togglePassword(button) {
        const passwordSpan = button.previousElementSibling;
        passwordSpan.classList.toggle('hidden');
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ url('addStaffAccount') }}"]');
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            this.submit(); // Submit the form with the correct method
        });
    });
</script>
--->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('button[data-modal-toggle="editAccountModal"]');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = button.closest('tr');
            const accountName = row.querySelector('td:nth-child(1)').textContent;
            const accountUser = row.querySelector('td:nth-child(2)').textContent;
            const accountId = row.getAttribute('data-id');
            
            // Set form values
            document.getElementById('edit_account_id').value = accountId;
            document.getElementById('edit_account_user').value = accountUser;
            document.getElementById('edit_account_name').value = accountName;
            
            // Set the form action to include the account ID
            document.getElementById('editAccountForm').action = `{{ url('editStaffAccount') }}/${accountId}`;
        });
    });
});

</script>
@endpush
@stack('scripts')
@endsection
