@extends('estab_template')

@section('content')

<p class="text-4xl mb-7 mt-6">Your assigned window...</p>
  
<div class="overflow-x-auto">
    <table class="table-auto w-full text-sm text-left text-gray-500 bg-white border-gray-200 shadow-md rounded-lg">
        <thead class="bg-gray-200 text-gray-700 uppercase text-2xl font-semibold">
            <tr>
                <th class="px-4 py-2 text-center">Window Name</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 text-2xl">
           
            @foreach($tables as $table)
            
                <form action="{{ url('/manage_queue') }}" method="POST" >
                    @csrf
                    <input type="hidden" value="{{ $table->window_name }}" name="table_name">
                        <tr  >
                            <td class="px-4 py-2  text-center">
                                <button type="submit" class="p-2 rounded-lg hover:bg-gray-200 w-1/4 ">
                                    {{ $table->window_name }}
                                </button>
                                <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700 w-4/4">
                            </td>
                            <td class="px-4 py-2 flex flex-col space-y-1">
                                <form action="{{ url('/open_queue') }}" method="POST" >
                                    @csrf
                                    <input type="hidden" value="{{ $table->window_id }}" name="table_id">
                                    <button type="submit" class="p-2 rounded-lg text-left hover:bg-green-400 w-1/4 border border-gray-800 bg-green-500 ">
                                        Open
                                    </button>
                                </form>
                                <form action="{{ url('/hold_queue') }}" method="POST" >
                                    @csrf        
                                    <input type="hidden" value="{{ $table->window_id }}" name="table_id">
                                    <button type="submit" class="p-2 rounded-lg hover:bg-yellow-400 w-1/4 text-left border border-gray-800 bg-yellow-500 ">
                                        Hold
                                    </button>
                                </form>
                                <form action="{{ url('/close_queue') }}" method="POST" >
                                    @csrf
                                    <input type="hidden" value="{{ $table->window_id }}" name="table_id">
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-400 w-1/4 text-left border border-gray-800 bg-red-500 ">
                                        Close
                                    </button>
                                </form>
                            </td>
                        </tr>
                </form>
            @endforeach
        </tbody>
    </table>
</div>

@endsection