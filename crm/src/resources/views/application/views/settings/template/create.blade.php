@extends('layout.app')

@section('title', trans('default.brands').' '.strtolower(trans('default.settings')))

@section('contents')
    <app-template-create-edit
        @if(isset($id)) selected-url="admin/app/templates/{{ $id }}" @endif
        @if(isset($action)) action="{{$action}}" @endif
        view="{{ request()->get('view') }}"
        tags="{{ json_encode(config('template.campaign.available_tag')) }}"
        alias="app"
    >
    </app-template-create-edit>
@endsection
