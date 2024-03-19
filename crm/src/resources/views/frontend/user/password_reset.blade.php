@extends('auth.layout.layout')

@section('title', trans('default.reset_password'))

@section('contents')
    <app-password-reset
            logo-url="{{ !empty(config()->get('settings.application.company_logo')) ? asset(config()->get('settings.application.company_logo')) : asset('/images/logo.png') }}"
            company-name="{{ config()->get('app.name') }}"></app-password-reset>
@endsection
