@extends('estab_template')

@section('content')
   <div class="relative p-4 w-full max-w-2xl max-h-full">
        <form id="tableCreateForm" action="{{ route('createTable') }}" method="POST">
            @csrf
            <div>
                <label class="dark:text-white text-2xl">Enter Service Window name:</label>
                <input class="mt-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="table_name" required>
            </div>

            @if (session('success'))
                <div style="color: green;">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div style="color: red;">{{ session('error') }}</div>
            @endif

            <div class="flex items-center mt-10">
                <p class="text-2xl">Input fields for Customer:</p>
            </div>

            <div class="flex items-center mt-8">
                <button id="addEntityBtn" type="button" class="text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    +
                </button>
                <span class="text-gray-600">Add field</span>
            </div>
            <div id="entityContainer" class="mt-4 space-y-2">
            </div>

            <button class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" type="submit">Create Table</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addEntityBtn = document.getElementById('addEntityBtn');
        const entityContainer = document.getElementById('entityContainer');
        let entityCount = 0;

        addEntityBtn.addEventListener('click', function() {
            entityCount++;
            
            const entityWrapper = document.createElement('div');
            entityWrapper.className = 'flex items-center space-x-2';

            const entityInput = document.createElement('input');
            entityInput.type = 'text';
            entityInput.name = `entities[${entityCount}][name]`;
            entityInput.placeholder = 'Field Name';
            entityInput.className = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500';
            entityInput.required = true;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '✖';
            removeBtn.className = 'text-red-500 hover:text-red-700 font-bold';
            removeBtn.addEventListener('click', function() {
                entityContainer.removeChild(entityWrapper);
            });

            entityWrapper.appendChild(entityInput);
            entityWrapper.appendChild(removeBtn);
            entityContainer.appendChild(entityWrapper);
        });
    });
    </script>
@endsection