@extends('layout.brand')

@section('title', trans('default.subscribers'))

@section('contents')
    <app-subscriber-import :valid-keys="{{$validKeys}}"></app-subscriber-import>
@endsection
