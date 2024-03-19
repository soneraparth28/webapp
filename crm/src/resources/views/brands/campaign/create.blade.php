@extends('layout.brand')

@section('title', trans('default.campaign'))

@section('contents')
    <app-campaign-create-edit
        :tab-init="{{ $tabInit }}"
        tags="{{ json_encode(config('template.campaign.available_tag')) }}"
        @if(isset($id))
            selected-url="brands/{{brand()->id}}/campaigns/{{ $id }}"
            id="{{ $id }}"
            d-message="{{ $message }}"
        @endif
    ></app-campaign-create-edit>
@endsection
