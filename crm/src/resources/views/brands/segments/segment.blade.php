@extends('layout.brand')

@section('title', trans('default.segments'))

@section('contents')
    <app-store-update-segment
        @if(isset($id) || isset($action)) action-url="brands/{{ brand()->id }}/segments/{{ $id }}" @endif
        @if(isset($action)) action="{{ $action }}" @endif
        ></app-store-update-segment>
@endsection
