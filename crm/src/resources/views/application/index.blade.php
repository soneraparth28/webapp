@extends('layout.app')

@section('title', trans('default.dashboard'))

@section('contents')
    <app-dashboard
        :permissions="{{ json_encode([
            'email_statistics' => auth()->user()->can('email_statistics_app'),
            'subscribers_count' => auth()->user()->can('subscribers_count_app'),
            'campaigns_count' => auth()->user()->can('campaigns_count_app'),
        ]) }}"></app-dashboard>
@endsection
