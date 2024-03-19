@extends('layout.app')

@section('title', trans('default.users'))

@section('contents')
    <app-users-roles
        alias="app"
    ></app-users-roles>
@endsection
