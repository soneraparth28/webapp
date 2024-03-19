@extends('layouts.app')

@section('title') {{trans('general.subscription_price')}} -@endsection

@section('content')

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

                                <div class="flex-row-start flex-align-center">
                                    <i class="bi bi-cash-stack font-22"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">Subscription price</p>
                                </div>
                                <p class="mb-0 color-gray font-18">Setup your subscription prices</p>



                                <!--<div class="mt-4 card border-radius-5px">-->
                                <!--    <div class="card-body">-->
                                <!--        <div class="flex-row-start flex-align-center flex-nowrap">-->
                                <!--            <i class="bi bi-info-circle font-14"></i>-->
                                <!--            <p class="mb-0 color-gray font-14 ml-3">You will receive 80% for each transaction (Does not include payment processor fees)</p>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->


                                <div class="row mt-4">
                                    <div class="col-12">

                                        <form method="POST" action="{{ url('settings/subscription') }}">

                                            @csrf

                                            <div class="row">
                                                <div class="mt-1 col-12 col-md-6">
                                                    <div class="form-group">
                                                        <strong>{{trans('general.subscription_price_weekly')}}</strong>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{$settings->currency_symbol}}</span>
                                                            </div>
                                                            <input class="form-control form-control-lg isNumber subscriptionPrice" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject' || auth()->user()->free_subscription == 'yes') disabled @endif name="price_weekly" placeholder="0.00" value="{{$settings->currency_code == 'JPY' ? round(auth()->user()->plan('weekly', 'price')) : auth()->user()->plan('weekly', 'price')}}"  type="text">
                                                            @error('price_weekly')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                            @enderror
                                                        </div>

                                                        <div class="flex-row-start flex-align-center mb-4">
                                                            <label class="form-switch mb-0">
                                                                <input type="checkbox" class="custom-control-input" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif name="status_weekly" value="1" @if (auth()->user()->plan('weekly', 'status')) checked @endif id="customSwitchWeekly">
                                                                <i></i>
                                                            </label>
                                                            <p class="mb-0 ml-2">{{ trans('general.status') }}</p>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="mt-1 col-12 col-md-6">
                                                    <div class="form-group">
                                                        <strong>{{trans('users.subscription_price')}} *</strong>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{$settings->currency_symbol}}</span>
                                                            </div>
                                                            <input class="form-control form-control-lg isNumber subscriptionPrice" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject' || auth()->user()->free_subscription == 'yes') disabled @endif name="price" placeholder="0.00" value="{{$settings->currency_code == 'JPY' ? round(auth()->user()->plan('monthly', 'price')) : auth()->user()->plan('monthly', 'price')}}"  type="text">
                                                            @error('price')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                            @enderror
                                                        </div>
                                                        <div class="flex-row-start flex-align-center mb-4">
                                                            <label class="form-switch mb-0">
                                                                <input type="checkbox" class="custom-control-input" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif name="free_subscription" value="yes" @if (auth()->user()->free_subscription == 'yes') checked @endif id="customSwitchFreeSubscription">
                                                                <i></i>
                                                            </label>
                                                            <p class="mb-0 ml-2">{{ trans('general.free_subscription') }}</p>
                                                        </div>
                                                        @if (auth()->user()->totalSubscriptionsActive() != 0)

                                                            @if (auth()->user()->free_subscription == 'yes')
                                                                <div class="bg-dark color-light p-3 display-none mt-3" role="alert" id="alertDisableFreeSubscriptions">
                                                                    <i class="fas fa-exclamation-triangle mr-2 color-light"></i>
                                                                    <span class="color-light">{{ trans('general.alert_disable_free_subscriptions') }}</span>
                                                                </div>

                                                            @else
                                                                <div class="bg-dark color-light p-3 display-none mt-3" role="alert" id="alertDisablePaidSubscriptions">
                                                                    <i class="fas fa-exclamation-triangle mr-2 color-light"></i>
                                                                    <span class="color-light">{{ trans('general.alert_disable_paid_subscriptions') }}</span>
                                                                </div>
                                                            @endif

                                                        @endif
                                                    </div>
                                                </div>


                                                <div class="mt-1 col-12 col-md-6">
                                                    <div class="form-group">
                                                        <strong>{{trans('general.subscription_price_quarterly')}}</strong>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{$settings->currency_symbol}}</span>
                                                            </div>
                                                            <input class="form-control form-control-lg isNumber subscriptionPrice" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject' || auth()->user()->free_subscription == 'yes') disabled @endif name="price_quarterly" placeholder="0.00" value="{{$settings->currency_code == 'JPY' ? round(auth()->user()->plan('quarterly', 'price')) : auth()->user()->plan('quarterly', 'price')}}"  type="text">
                                                            @error('price_quarterly')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                            @enderror
                                                        </div>

                                                        <div class="flex-row-start flex-align-center mb-4">
                                                            <label class="form-switch mb-0">
                                                                <input type="checkbox" class="custom-control-input" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif name="status_quarterly" value="1" @if (auth()->user()->plan('quarterly', 'status')) checked @endif id="customSwitchQuarterly">
                                                                <i></i>
                                                            </label>
                                                            <p class="mb-0 ml-2">{{ trans('general.status') }}</p>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="mt-1 col-12 col-md-6">
                                                    <div class="form-group">
                                                        <strong>{{trans('general.subscription_price_biannually')}}</strong>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{$settings->currency_symbol}}</span>
                                                            </div>
                                                            <input class="form-control form-control-lg isNumber subscriptionPrice" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject' || auth()->user()->free_subscription == 'yes') disabled @endif name="price_biannually" placeholder="0.00" value="{{$settings->currency_code == 'JPY' ? round(auth()->user()->plan('biannually', 'price')) : auth()->user()->plan('biannually', 'price')}}"  type="text">
                                                            @error('price_biannually')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                            @enderror
                                                        </div>

                                                        <div class="flex-row-start flex-align-center mb-4">
                                                            <label class="form-switch mb-0">
                                                                <input type="checkbox" class="custom-control-input" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif name="status_biannually" value="1" @if (auth()->user()->plan('biannually', 'status')) checked @endif id="customSwitchBiannually">
                                                                <i></i>
                                                            </label>
                                                            <p class="mb-0 ml-2">{{ trans('general.status') }}</p>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="mt-1 col-12 col-md-6">
                                                    <div class="form-group">
                                                        <strong>{{trans('general.subscription_price_yearly')}}</strong>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{$settings->currency_symbol}}</span>
                                                            </div>
                                                            <input class="form-control form-control-lg isNumber subscriptionPrice" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject' || auth()->user()->free_subscription == 'yes') disabled @endif name="price_yearly" placeholder="0.00" value="{{$settings->currency_code == 'JPY' ? round(auth()->user()->plan('yearly', 'price')) : auth()->user()->plan('yearly', 'price')}}"  type="text">
                                                            @error('price_yearly')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                            @enderror
                                                        </div>

                                                        <div class="flex-row-start flex-align-center mb-4">
                                                            <label class="form-switch mb-0">
                                                                <input type="checkbox" class="custom-control-input" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif name="status_yearly" value="1" @if (auth()->user()->plan('yearly', 'status')) checked @endif id="customSwitchYearly">
                                                                <i></i>
                                                            </label>
                                                            <p class="mb-0 ml-2">{{ trans('general.status') }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                            <button class="dark-btn mnw-75" @if (auth()->user()->verified_id == 'no' || auth()->user()->verified_id == 'reject') disabled @endif onClick="this.form.submit(); this.disabled=true; this.innerText='{{trans('general.please_wait')}}';" type="submit">
                                                {{trans('general.save_changes')}}
                                            </button>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
