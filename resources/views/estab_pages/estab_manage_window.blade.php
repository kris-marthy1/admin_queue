@extends('estab_template')

@section('content')

<p class="text-4xl mb-7">Choose window...</p>
  
<div class="overflow-x-auto">
    <table class="table-auto w-full text-sm text-left text-gray-500 bg-white border-gray-200 shadow-md rounded-lg">
        <thead class="bg-gray-200 text-gray-700 uppercase text-2xl font-semibold">
            <tr>
                <th class="px-4 py-2">Window Name</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 text-2xl">
           
            @foreach($tables as $table)
            
            <form action="{{ url('/manage_queue') }}" method="POST" >
                @csrf
                <input type="hidden" value="{{ $table }}" name="table_name">
                    <tr>
                    <td class="px-4 py-2 ">
                        <button type="submit" class="p-2 rounded-lg hover:bg-gray-200 w-2/4 text-left">
                            {{ $table }}
                        </button>
                        <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700 w-2/4">
                    </td>
                    </tr>
            </form>
        @endforeach
        </tbody>
    </table>
</div>

@endsection