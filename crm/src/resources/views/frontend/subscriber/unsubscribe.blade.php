@extends('install.layout')

@section('title', ucfirst(trans('default.unsubscribe')))

@section('contents')
    <app-unsubscribe :subscriber="{{ $subscriber }}" ></app-unsubscribe>
@endsection
