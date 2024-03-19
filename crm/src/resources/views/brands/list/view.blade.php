@extends('layout.brand')

@section('title', trans('default.lists'))

@section('contents')
    <app-lists-show
        @if(isset($id)) selected-url="brands/{{brand()->id}}/lists/{{ $id }}" @endif>
    </app-lists-show>
@endsection
