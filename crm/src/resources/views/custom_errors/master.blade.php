@extends('layout.master')

@push('before-styles')
<style>
    html, body {
        height: 100%;
    }
</style>
@endpush
@section('master')
    @section('class', 'h-100')
    @yield('error-content')
@endsection

