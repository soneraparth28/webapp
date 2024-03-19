@extends('auth.layout.layout')

@section('title', trans('default.login'))

@section('contents')

    <app-auth-login
            logo-url="{{ !empty(config()->get('settings.application.company_logo')) ? asset(config()->get('settings.application.company_logo')) : asset('/images/logo.png') }}"company-name="{{ config()->get('app.name') }}"
            previous-page="{{ $previous_page ?? '/' }}"
            demo="{{ count($demoCredentials) ? json_encode($demoCredentials) : ''}}"
    ></app-auth-login>
@endsection

