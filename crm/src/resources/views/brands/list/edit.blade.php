@extends('layout.brand')

@section('title', trans('default.lists'))

@section('contents')

    <app-lists-create-edit
    @if(isset($id)) selected-url="brands/{{brand()->id}}/lists/{{ $id }}" @endif
    >

    </app-lists-create-edit>
@endsection
