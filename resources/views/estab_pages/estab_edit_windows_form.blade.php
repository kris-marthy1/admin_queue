@extends('estab_template')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl mb-7">Edit Window: {{ $tableName }}</h1>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('windows.update') }}" method="POST" class="max-w-lg" id="editWindowForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="original_table_name" value="{{ $tableName }}">

    <div class="mb-4">
        <label for="new_table_name" class="block text-gray-700 text-sm font-bold mb-2">
            Window Name
        </label>
        <input 
            type="text" 
            name="new_table_name" 
            id="new_table_name"
            value="{{ $tableName }}"
            required 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
    </div>

    <h2 class="text-2xl mb-4">Columns</h2>
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <table class="w-full" id="columnsTable">
            <thead>
                <tr>
                    <th class="text-left p-1">Current Name</th>
                    <th class="text-left p-1">New Name</th>
                    <th class="text-left p-1">Actions</th>
                </tr>
            </thead>
            <tbody id="columnsTableBody">
                @foreach($columns as $column)
                    <tr class="border-b">
                        <td class="py-2 p-3">{{ $column->Field }}</td>
                        <td class="py-2">
                            <input 
                                type="text" 
                                name="columns[]" 
                                value="{{ $column->Field }}"
                                class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            >
                        </td>
                        <td class="py-2 p-3">
                            <button 
                                type="button" 
                                class="delete-column text-red-500 hover:text-red-700"
                                data-column-name="{{ $column->Field }}"
                                data-table-name="{{ $tableName }}"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            @if (session('success'))
                <div style="color: green;">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div style="color: red;">{{ session('error') }}</div>
            @endif
        <div class="flex items-center mt-8">
            <button id="addColumnBtn" type="button" class="text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                +
            </button>
            <span class="text-gray-600">Add field</span>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Update Window
        </button>
        <a href="{{ url('/add_window') }}" class="inline-block align-baseline font-bold text-sm bg-red-500 rounded py-2 px-4 text-white hover:text-blue-800">
            Cancel
        </a>
    </div>
</form>

<form id="deleteColumnForm" method="POST" action="{{ route('windows.deleteColumn') }}" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="column_name" id="deleteColumnName">
    <input type="hidden" name="table_name" value="{{ $tableName }}">
</form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addColumnBtn = document.getElementById('addColumnBtn');
    const columnsTableBody = document.getElementById('columnsTableBody');
    const deleteColumnForm = document.getElementById('deleteColumnForm');
    const deleteColumnNameInput = document.getElementById('deleteColumnName');

    addColumnBtn.addEventListener('click', function() {
        const row = document.createElement('tr');
        row.className = 'border-b';

        row.innerHTML = `
            <td class="py-2 p-3">New Column</td>
            <td class="py-2">
                <input type="text" name="columns[]" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </td>
            <td class="py-2 p-3">
                <button type="button" class="delete-row text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;

        columnsTableBody.appendChild(row);

        row.querySelector('.delete-row').addEventListener('click', function() {
            this.closest('tr').remove();
        });
    });

    document.querySelectorAll('.delete-column').forEach(button => {
        button.addEventListener('click', function() {
            const columnName = this.getAttribute('data-column-name');
            deleteColumnNameInput.value = columnName;
            deleteColumnForm.submit();
        });
    });

    document.getElementById('editWindowForm').addEventListener('submit', function(e) {
        const columnInputs = document.querySelectorAll('input[name="columns[]"]');
        columnInputs.forEach(input => {
            input.value = input.value.replace(/\s+/g, '_').toLowerCase();
        });
    });
});

</script>
@endsection