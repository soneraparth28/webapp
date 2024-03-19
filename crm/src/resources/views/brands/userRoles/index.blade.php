@extends('layout.brand')

@section('title', trans('default.users'))

@section('contents')
    <app-users-roles
        alias="brand"
    ></app-users-roles>
@endsection
