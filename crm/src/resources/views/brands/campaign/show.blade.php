@extends('layout.brand')

@section('title', trans('default.campaign'))

@section('contents')
    <app-campaigns-show
        @if(isset($id)) selected-url="brands/{{brand()->id}}/campaigns/{{ $id }}?counts=true" @endif
    />
@endsection
