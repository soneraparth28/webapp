@extends('layouts.app')

@section('title') {{trans('general.referrals')}} -@endsection

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
                                    <i class="bi-person-plus font-22"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">Referrals</p>
                                </div>
                                <p class="mb-0 color-gray font-18">Welcome to your referral panel</p>


                                <div class="mt-4 card border-radius-5px">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="mt-1 col-12 col-lg-6">
                                                <div class="flex-col-start">
                                                    <p class="color-gray font-16 mb-0">Share your link and earn 5% of your referrals, be it a subscription, Tip or a PPV!</p>
                                                    <p class="color-gray font-14 mb-0">* You will earn 5% for each transaction of your referral.</p>
                                                </div>
                                            </div>
                                            <div class="mt-1 col-12 col-lg-6">
                                                <div class="flex-row align-items-center justfiy-content-start justify-content-lg-end">
                                                    <div class="flex-col-start">
                                                        <p class="color-gray font-16 mb-0">Your referral link is: </p>
                                                        <p class="color-gray font-14 mb-0 font-weight-bold">{{ url('/?ref='.auth()->user()->id) }}</p>
                                                    </div>

                                                    <button class="d-none copy-url" id="copyLink" data-clipboard-text="{{ url('/?ref='.auth()->user()->id) }}"></button>
                                                    <button class="dark-btn pt-1 pb-1 pl-2 pr-2 ml-4" data-toggle="tooltip" data-placement="top"
                                                            title="{{trans('general.copy_link')}}" onclick="$('#copyLink').trigger('click')">
                                                        <i class="far fa-clone"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="mt-1 col-12 col-md-4">
                                        <div class="card border-radius-5px">
                                            <div class="card-body">
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center">
                                                        <i class="bi-person font-16"></i>
                                                        <p class="font-16 mb-0 ml-2">{{ trans('general.total_registered_users') }}</p>
                                                    </div>
                                                    <o class="font-22 font-weight-bold mb-0">{{ number_format(auth()->user()->referrals()->count()) }}</o>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1 col-12 col-md-4">
                                        <div class="card border-radius-5px">
                                            <div class="card-body">
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center">
                                                        <i class="bi-list font-16"></i>
                                                        <p class="font-16 mb-0 ml-2">{{ trans('general.total_transactions') }}</p>
                                                    </div>
                                                    <o class="font-22 font-weight-bold mb-0">{{ number_format(auth()->user()->referralTransactions()->count()) }}</o>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1 col-12 col-md-4">
                                        <div class="card border-radius-5px">
                                            <div class="card-body">
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center">
                                                        <i class="bi-currency-dollar font-16"></i>
                                                        <p class="font-16 mb-0 ml-2">{{ trans('general.earnings_total') }}</p>
                                                    </div>
                                                    <o class="font-22 font-weight-bold mb-0">{{ Helper::amountFormatDecimal(auth()->user()->referralTransactions()->sum('earnings')) }}</o>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border-radius-5px">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped m-0">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{trans('admin.type')}}</th>
                                                            <th scope="col">{{trans('admin.date')}}</th>
                                                            <th scope="col">{{trans('general.earnings')}}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>

                                                        @if ($transactions->count() != 0)
                                                            @foreach ($transactions as $referred)
                                                                <tr>
                                                                    <td>{{ __('general.'.$referred->type) }}</td>
                                                                    <td>{{ Helper::formatDate($referred->created_at) }}</td>
                                                                    <td>{{ Helper::amountFormatDecimal($referred->earnings) }}</td>
                                                                </tr>
                                                            @endforeach

                                                        @else
                                                            <tr>
                                                                <td>{{ trans('general.no_transactions_yet') }}</td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        @endif

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
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
