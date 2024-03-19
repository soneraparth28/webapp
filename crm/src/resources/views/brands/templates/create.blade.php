@extends('layout.brand')

@section('title', trans('default.templates'))

@section('contents')
    <app-template-create-edit
        @if(isset($id)) selected-url="brands/{{ brand()->id }}/templates/{{ $id }}" @endif
        @if(isset($action)) action="{{$action}}" @endif
        view="{{ request()->get('view') }}"
        tags="{{ json_encode(config('template.campaign.available_tag')) }}"
        alias="brand"
    >
    </app-template-create-edit>
@endsection
