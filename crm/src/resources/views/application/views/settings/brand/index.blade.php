@extends('layout.app')

@section('title', trans('default.brands').' '.strtolower(trans('default.settings')))

@section('contents')
    <app-brand-setting-layout settings-permissions="{{ json_encode($permissions) }}"></app-brand-setting-layout>
@endsection
