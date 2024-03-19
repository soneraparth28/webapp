@extends('layout.brand')

@section('title', trans('default.dashboard'))

@section('contents')
    <app-dashboard :alias="true" :permissions="{{ json_encode([
            'email_statistics' => auth()->user()->can('email_statistics_brands'),
            'subscribers_count' => auth()->user()->can('subscribers_count_brands'),
            'campaigns_count' => auth()->user()->can('campaigns_count_brands'),
            'view_brand_segment_counts' => auth()->user()->can('view_brand_segment_counts'),
        ]) }}"></app-dashboard>
@endsection
