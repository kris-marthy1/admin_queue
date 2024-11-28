@extends('estab_template')

@section('content')
   <div class="relative p-4 w-full max-w-2xl max-h-full">
        <form id="tableCreateForm" action="{{ route('createTable') }}" method="POST">
            @csrf
            <div>
                <label class="dark:text-white text-2xl ">Enter Service Window name:</label>
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
                <!-- Dynamic entities will be added here -->
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

        // Create container for this entity input
        const entityWrapper = document.createElement('div');
        entityWrapper.className = 'flex items-center space-x-2';

        // Create input for entity name
        const entityInput = document.createElement('input');
        entityInput.type = 'text';
        entityInput.name = `entities[${entityCount}][name]`; // name input will be entities[1][name], entities[2][name], etc.
        entityInput.placeholder = 'Entity Name';
        entityInput.className = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500';
        entityInput.required = true;

        // Create dropdown for data type
        const dataTypeSelect = document.createElement('select');
        dataTypeSelect.name = `entities[${entityCount}][type]`; // type input will be entities[1][type], entities[2][type], etc.
        dataTypeSelect.className = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500';
        
        // Populate data type options
        const dataTypes = [
            { value: 'VARCHAR(255)', text: 'Text' }, 
            { value: 'DECIMAL(10,2)', text: 'Number with 2 Decimals' },
            { value: 'INT', text: 'Whole number' }
        ];
        
        dataTypes.forEach(type => {
            const option = document.createElement('option');
            option.value = type.value;
            option.textContent = type.text;
            dataTypeSelect.appendChild(option);
        });

        // Create remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '✖';
        removeBtn.className = 'text-red-500 hover:text-red-700 font-bold';
        removeBtn.addEventListener('click', function() {
            entityContainer.removeChild(entityWrapper);
        });

        // Append elements to wrapper
        entityWrapper.appendChild(entityInput);
        entityWrapper.appendChild(dataTypeSelect);
        entityWrapper.appendChild(removeBtn);

        // Add to container
        entityContainer.appendChild(entityWrapper);
    });
});
</script>

@endsection
