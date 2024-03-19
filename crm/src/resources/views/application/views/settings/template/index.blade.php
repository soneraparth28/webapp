@extends('layout.app')

@section('title', trans('default.brands').' '.strtolower(trans('default.settings')))

@section('contents')
    <app-template-settings></app-template-settings>
@endsection
