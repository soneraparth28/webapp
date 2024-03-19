@extends('auth.layout.layout')

@section('title', trans('default.confirm'))

@section('contents')
    <app-user-invite-confirm
            :user="{{ json_encode($user)  }}"
            logo-url="{{ !empty(config()->get('settings.application.company_logo')) ? asset(config()->get('settings.application.company_logo')) : asset('/images/logo.png') }}"
            company-name="{{config()->get('app.name')}}"
    ></app-user-invite-confirm>
@endsection
