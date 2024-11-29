@extends('estab_template')

@section('content')
   
<p class="text-4xl mb-7 ">Choose window...</p>
<div class="flex justify-between items-center mb-4">
    <a href="{{ url('/add_windows_form') }}"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none">
        Add New Window
    </a>
</div>
        



    <div class="overflow-x-auto">
    <table class="table-auto w-full text-sm text-left text-gray-500 bg-white border border-gray-200 shadow-md rounded-lg">
        <thead class="bg-gray-200 text-gray-700 uppercase text-2xl font-semibold">
            <tr>
                <th class="px-4 py-2">Window Name</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 text-2xl text-left">
            @foreach($tables as $table)
            <tr class="border-b hover:bg-gray-100">
                <td class="px-4 py-2">{{ $table }}</td>
                <td class="flex px-4 py-2 gap-1">
                <form action="{{ route('windows.edit', ['tableName' => $table]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Edit
                    </button>
                </form>
                    <form action="{{ route('tables.delete', ['tableName' => $table]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?')">
                    @csrf
                        <input type="hidden" name="table_name" value="{{ $table }}">
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




<script>

    // Confirm delete action
    function confirmDelete(message) {
        return confirm(message || 'Are you sure you want to delete this table?');
    }
</script>



  
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
<div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add new window
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="static-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
            <form action="{{ route('createTable') }}" method="POST">
                    @csrf
                    <div>
                        <label class="dark:text-white">Table Name:</label>
                        <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="table_name" required>
                    </div>

                    <div id="columns">
                        <!-- <label class="dark:text-white">Columns:</label> -->
                        <div class="flex">
                             <input type="hidden" value="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="columns[]" placeholder="Column Name" required>
                            <select style="visibility: hidden" value="Integer" class="bg-gray-50 border border-white-900 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-white-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="types[]">
                                <option value="integer">Integer</option>
                            </select>
                        </div>
                    </div>

                    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" type="submit">Create Table</button>
                </form>
            </div>
         
        </div>
    </div>
</div>


@endsection