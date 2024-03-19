@extends('layout.brand')

@section('title', trans('default.subscribers'))

@section('contents')
    <app-subscriber-create-edit
        @if(isset($id)) selected-url="brands/{{ brand()->id }}/subscribers/{{ $id }}" @endif
    ></app-subscriber-create-edit>
@endsection
