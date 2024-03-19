@extends('layout.app')

@section('title', trans('default.app').' '.strtolower(trans('default.settings')))

@section('contents')
    <app-setting-layout settings-permissions="{{ json_encode($permissions)  }}"></app-setting-layout>
@endsection
