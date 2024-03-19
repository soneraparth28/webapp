@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection
@php use App\Helper; @endphp

@section('content')

    <?php $pageTitle = "Dashboard"; ?>

    <div class="session-main-wrapper" id="page-dashboard">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row mt-3">
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="p-3 item-container-box bg-dark">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <p class="font-18 mb-0 color-light">Total Balance</p>
                                    <p class="font-25 font-weight-bold color-light mt-2">
                                        {{ Helper::amountFormatDecimal(Auth::user()->balance) }}
                                    </p>
                                </div>
                                <div class="flex-col-start">
                                    <a href="{{url('settings/withdrawals')}}" class="light-btn py-2 px-3 font-16">
                                        <i class="fa fa-receipt font-20 mr-2"></i>
                                        Withdraw
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-4 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <div class="flex-row-start flex-align-center">
                                        <i class="fa fa-bank d-none d-sm-inline-block"></i>
                                        <p class="font-18 mb-0 ml-2">Total Revenue</p>
                                    </div>
                                    <p class="font-25 font-weight-bold mt-2">
                                        {{ Helper::amountFormatDecimal($earningNetUser)  }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-4 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-row-between">
                                <div class="flex-col-start">
                                    <div class="flex-row-start flex-align-center">
                                        <i class="fa fa-check-circle d-none d-sm-inline-block"></i>
                                        <p class="font-18 mb-0 ml-2">Active Subscriptions</p>
                                    </div>
                                    <p class="font-25 font-weight-bold mt-2">
                                        {{ Helper::formatNumber($subscriptionsActive) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-2">

                    <div class="col-6 col-lg-3 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-money-bill d-none d-sm-inline-block"></i>
                                    <p class="font-18 mb-0 ml-2">Tip Earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold mt-2 mb-0">
                                    {{ Helper::amountFormatDecimal($earningsFromTips) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-arrows-rotate d-none d-sm-inline-block"></i>
                                    <p class="font-18 mb-0 ml-2">Subscription Earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold mt-2 mb-0">
                                    {{ Helper::amountFormatDecimal($earningsFromSubscriptions) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-lock d-none d-sm-inline-block"></i>
                                    <p class="font-18 mb-0 ml-2">Paid Content Earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold mt-2 mb-0">
                                    {{ Helper::amountFormatDecimal($earningsFromPpv) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 mt-1">
                        <div class="p-3 item-container-box">
                            <div class="flex-col-start">
                                <div class="flex-row-start flex-align-center">
                                    <i class="fa fa-graduation-cap d-none d-sm-inline-block"></i>
                                    <p class="font-18 mb-0 ml-2">Course Earnings</p>
                                </div>
                                <p class="font-25 font-weight-bold mt-2 mb-0">{{ Helper::amountFormatDecimal(0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

                <style>
                    div.google-visualization-tooltip {
                        background: var(--dark);
                        color: var(--light) !important;
                        border-radius: 15px;
                        padding: .5rem .75rem;
                    }
                    div.google-visualization-tooltip * {
                        color: var(--light) !important;
                    }
                </style>

                <div class="row mt-2">
                    <div class="col-12 col-lg-5 mt-2">
                        <p class="font-22 font-weight-bold">Monthly Revenue</p>

                        <div class="" id="columnchart_values" style="width: 100%;"></div>
                    </div>
                    <div class="col-12 col-lg-7 mt-2">
                        <p class="font-22 font-weight-bold">Customer by Country</p>
                        <div style="height: 535px;">
                            <div class="" id="regions_div" style="width: 100%;  height: 275px"></div>
                            <div class="flex-col-start mt-2">
                                <p class="font-22 font-weight-bold mb-0">Recent orders</p>


                                <div class="table-responsive border border-med-light-gray border-radius-5px">
                                    <table class="table table-hover prettyTable mb-0" id="">
                                        <thead>
                                            <tr class="font-weight-bold">
                                                <th class="pr-0"></th>
                                                <th class="pl-1">Name</th>
                                                <th>Time</th>
                                                <th>Price</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(!$latestTransactions->isEmpty())
                                            @foreach($latestTransactions->reverse() as $transaction)
                                                <tr class="border-med-light-gray" style="border-top-style: dashed; border-top: 1px dashed var(--med-light-gray)">
                                                    <td class="pr-0 text-center align-middle">
                                                        <img class="square-25 border-radius-50 noSelect" src="{{Helper::getFile(config('path.avatar').$transaction->user->avatar)}}" />
                                                    </td>
                                                    <td class="pl-1 align-middle">{{ucfirst($transaction->user->name)}}</td>
                                                    <td class=" align-middle">{{Helper::timeAgo(strtotime($transaction->created_at))}}</td>
                                                    <td class=" align-middle">{{Helper::amountFormatDecimal($transaction->amount)}}</td>
                                                    <td class=" align-middle">
                                                        <div class="py-2 px-3 font-16 bg-med-light-gray border-radius-10px text-center">
                                                            {{$transaction->subtype !== "normal" ? ucfirst($transaction->subtype) :
                                                                (strtolower($transaction->type) === "ppv" ? "Paid Content" : ucfirst($transaction->type))}}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!auth()->check())
                    @include("includes.modal-login")
                @endif

            </div>
        </div>
    </div>

@endsection
