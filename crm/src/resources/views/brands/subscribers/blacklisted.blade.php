@extends('layout.brand')

@section('title', trans('default.subscribers'))

@section('contents')
    <app-blacklisted-subscribers></app-blacklisted-subscribers>
@endsection
