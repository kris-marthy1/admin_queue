@extends('estab_template')

@section('content')

<div class="container">
    <h1 class="text-3xl">Manage {{ $tableName }}</h1>

    <!-- Pass the table name dynamically as a data attribute -->
    <div id="table-data" data-table-name="{{ $tableName }}"></div>

    <!-- Load Vite React Scripts -->
    @viteReactRefresh
    @vite('resources/js/app.ts')
</div>

@endsection
