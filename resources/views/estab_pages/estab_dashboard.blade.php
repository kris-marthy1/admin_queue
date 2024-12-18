@extends('estab_template')

@section('content')
@if (Auth::check())
    <p>Current Role: {{ Auth::user()->getRoleNames()->first() }}</p>
@endif

@if (Session::has('success'))
    <div class="bg-green-100 text-green-700 p-4 rounded">
        {{ Session::get('success') }}
    </div>
@endif 

<p class="text-3xl font-bold mb-4">Reports</p>

<div class="flex flex-col p-2">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 shadow-md rounded">
            <thead>
                <tr class="bg-gray-100 border-b text-center">
                    <th class="px-4 py-2 text-gray-600 font-medium">Report No.</th>
                    <th class="px-4 py-2 text-gray-600 font-medium">Queue No.</th>
                    <th class="px-4 py-2 text-gray-600 font-medium">Arrived in queue at</th>
                    <th class="px-4 py-2 text-gray-600 font-medium">Service window name</th>
                    <th class="px-4 py-2 text-gray-600 font-medium">Exited queue at</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyRecords as $row)
                    <tr class="border-b hover:bg-gray-50 text-center">
                        <td class="px-4 py-2 text-gray-700">{{ $row->report_id }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $row->queue_id }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $row->arrived_at }}</td>
                        <td class="px-4 py-2 text-gray-700">
                            @if($row->window_name)
                                {{ $row->window_name }}
                            @else
                                <i>Null</i>
                            @endif 
                        </td>
                        <td class="px-4 py-2 text-gray-700">{{ $row->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-gray-700 text-center">No reports found.</td>
                    </tr>
                @endforelse
                
            </tbody>
        </table>
    </div>

    {{-- Pagination Links with Flowbite/Tailwind Styling --}}
    <div class="mt-4 flex justify-between items-center">
        {{-- Showing X to Y of Z results --}}
        <div class="text-sm text-gray-700">
            Showing 
            {{ $historyRecords->firstItem() }} 
            to 
            {{ $historyRecords->lastItem() }} 
            of 
            {{ $historyRecords->total() }} 
            results
        </div>

        {{-- Pagination Navigation --}}
        <nav aria-label="Page navigation" class="inline-flex">
            @if ($historyRecords->hasPages())
                <ul class="inline-flex -space-x-px text-sm">
                    {{-- Previous Page Link --}}
                    @if ($historyRecords->onFirstPage())
                        <li>
                            <span class="cursor-not-allowed bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 ml-0 rounded-l-lg leading-tight border border-gray-300 py-2 px-3 inline-flex">Previous</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $historyRecords->previousPageUrl() }}" class="bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 ml-0 rounded-l-lg leading-tight border border-gray-300 py-2 px-3 inline-flex">Previous</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($historyRecords->getUrlRange(1, $historyRecords->lastPage()) as $page => $url)
                        @if ($page == $historyRecords->currentPage())
                            <li>
                                <span class="bg-blue-50 border border-blue-300 text-blue-600 py-2 px-3 inline-flex">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 leading-tight border border-gray-300 py-2 px-3 inline-flex">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($historyRecords->hasMorePages())
                        <li>
                            <a href="{{ $historyRecords->nextPageUrl() }}" class="bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-r-lg leading-tight border border-gray-300 py-2 px-3 inline-flex">Next</a>
                        </li>
                    @else
                        <li>
                            <span class="cursor-not-allowed bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-r-lg leading-tight border border-gray-300 py-2 px-3 inline-flex">Next</span>
                        </li>
                    @endif
                </ul>
            @endif
        </nav>
    </div>
</div>
@endsection