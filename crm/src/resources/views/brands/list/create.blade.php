@extends('layout.brand')

@section('title', trans('default.lists'))

@section('contents')
    <app-lists-create-edit
        @if(isset($id) || isset($action)) selected-url="brands/{{brand()->id}}/lists/{{ $id }}/view" @endif
        @if(isset($action)) action="copy" @endif
    ></app-lists-create-edit>




@endsection
