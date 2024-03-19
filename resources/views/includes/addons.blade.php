@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('public/plugins/datepicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/plugins/select2/select2.min.css') }}?v={{$settings->version}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: var(--theme-dark-card) !important;
        }
    </style>

    <?php $pageTitle = "Settings"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")



        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row">
                    <div class="col-lg-4 col-xl-3 d-none d-lg-flex">
                        @include("includes.cards-settings")
                    </div>


                    <div class="col-12 col-lg-8 col-xl-9">
                        <div class="row">
                            <div class="col-12 d-lg-none">
                                @include("includes.card-settings-mobile")
                            </div>
                            <div class="col-12 mt-3 mt-lg-0">
                                <div class="flex-row-between flex-align-center mb-3">
                                    <div class="flex-col-start">
                                        <div class="flex-row-start flex-align-center">
                                            <i class="bi bi-gift font-25"></i>
                                            <p class="font-25 font-weight-bold mb-0 ml-3">Plan</p>
                                        </div>
                                        <p class="color-gray font-14 mb-0">Details of all plans and addons</p>
                                    </div>
                                </div>
                                @if(request()->has('success'))
                                    <div class="alert alert-success">
                                        {{ request()->get('success') }}
                                    </div>
                                @endif
                                @if(request()->has('error'))
                                    <div class="alert alert-danger">
                                        {{ request()->get('error') }}
                                    </div>
                                @endif
                                
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="card bg-theme-card">
                                    <div class="card-body">
                                        <div class="pb-3">
                                            <p class="font-18 color-light-gray text-uppercase">Your plan</p>
                                             <div class="table-responsive mt-3">
                                                <table class="table table-hover prettyTable " id="addons_table">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width:123px;" class="align-middle pr-0">
                                                                
                                                                    <img src="{{asset("public/img/icons/courses.png")}}" class="square-75 border-radius-50 noSelect" />
                        
                                                            </td>
                                                            <td class="align-middle pl-2 hideOnMobileTableCell">
                                                                <div class="flex-col-start">
                                                                    <p class="font-weight-bold font-18 mb-0">Eduvo Premium</p>
                                                                    <p class="font-16 mb-0">Start creating communities and online courses.</p>
                                                                </div>
                                                            </td>
                                                            @if(isset($plans))
                                                                @if($plans->status == 1 && $plans->is_cancelled == 0)
                                                                    <td class="align-middle">
                                                                        <a href="{{route('cancel_subs')}}" class="light-btn w-150px display-block float-right">Cancel</a>
                                                                    </td>
                                                                @elseif($plans->is_cancelled == 1)
                                                                <td class="align-middle">
                                                                        <a href="{{route("addon", 1)}}" class="light-btn w-150px display-block float-right">Subscribe</a></td>
                                                                @else
                                                                    <td class="align-middle">
                                                                        <a href="{{route("addon", 1)}}" class="light-btn w-150px display-block float-right">Subscribe</a></td>
                                                                @endif
                                                            @else
                                                            <td class="align-middle">
                                                                        <a href="{{route("addon", 1)}}" class="light-btn w-150px display-block float-right">Subscribe</a></td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                         @if(isset($plans))
                                        <!--<div class="pt-3 border-med-light-gray" style="border-top-style: dashed; border-top-width: 1px;">
                                            <p class="font-18 color-light-gray text-uppercase">Your addons</p>
                                            @if($plans->status == 1)
                                            @if($myAddons->isEmpty())
                                                <p class="mt-2 font-18 font-italic">You don't have any addons</p>
                                            @else
                                                @foreach($myAddons as $addon)
                                                    <div class="flex-row-between mt-2">
                                                        <div class="flex-col-start flex-align-start">
                                                            <p class="mb-0 font-weight-bold font-16">{{$addon->title}}</p>
                                                            <p class="mb-0 font-16">{{$addon->description}}</p>
                                                        </div>
                                                        <div class="flex-col-start flex-align-end">
                                                            <p class="mb-0 font-weight-bold font-16">{{ucfirst($addon->interval)}}</p>
                                                            <a class="mb-0 font-16" href="{{url("my/subscriptions")}}">Manage subscription</a>
                                                        </div>
                                                    </div>

                                                @endforeach
                                            @endif
                                            @else
                                            <p class="mt-2 font-18 font-italic">You don't have any addons</p>
                                            @endif
                                        </div>-->
                                        @endif
                                    </div>
                                </div>
                                 @if(isset($plans))
                                @if($plans->status == 1)
                                    <!--<div class="card bg-theme-card mt-4">
                                        <div class="card-body">
                                            <p class="font-18 text-uppercase font-weight-bold mb-0">Get more out of Eduvo</p>
                                            <p class="font-16 mt-1 mb-0 text-uppercase">Try out Add-ons</p>
                                            @if($addons->isEmpty())
                                                <p class="mt-2 font-18 font-italic">Currently no available addons for you</p>
                                            @else
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-hover prettyTable " id="addons_table">
                                                        <tbody>
                                                        @foreach($addons as $addon)
                                                            <tr>
                                                                <td style="width:123px;" class="align-middle pr-0">
                                                                    @if(!empty($addon->icon))
                                                                        <img src="{{asset("public/img/icons/$addon->icon")}}" class="square-75 border-radius-50 noSelect" />
                                                                    @endif
                                                                </td>
                                                                <td class="align-middle pl-2 hideOnMobileTableCell">
                                                                    <div class="flex-col-start">
                                                                        <p class="font-weight-bold font-18 mb-0">App</p>
                                                                        <p class="font-16 mb-0">Create your own app and publish it on app store within 7 days.</p>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <a href="{{route("addons", $addon->id)}}" class="light-btn w-150px display-block float-right">Upgrade</a>
                                                                </td>
                                                            </tr>
                                                            
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>-->
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
