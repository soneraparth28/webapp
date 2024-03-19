@extends('layout.brand')

@section('title', trans('default.settings'))

@section('contents')
    <app-brand-setting :permissions="{{ json_encode($permissions) }}"></app-brand-setting>
@endsection
