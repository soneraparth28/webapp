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
                                <div class="row">
                                    <div class="col-12 col-lg-6 mt-1">
                                        <div class="flex-col-start">
                                            <div class="flex-row-start flex-align-center">
                                                <i class="bi bi-receipt font-25"></i>
                                                <p class="font-25 font-weight-bold mb-0 ml-3">Payments</p>
                                            </div>
                                            <p class="color-gray font-14 mb-0">History of all payments {{request()->is('my/payments/received') ? "received" : "made"}}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 mt-2 mt-lg-1 mb-1 mb-lg-0">
                                        <div class="flex-row align-items-center justify-content-start justify-content-lg-end">
                                            @if ($transactions->count() != 0 && auth()->user()->verified_id == 'yes' && !(str_contains(request()->getRequestUri(), 'panel/admin/invoices') && auth()->user()->role == 'admin'))
                                                <span>{{trans('general.filter_by')}}</span>
                                                <select class="ml-2 custom-select w-auto" id="filter">
                                                    <option @if (request()->is('my/payments')) selected @endif value="{{url('my/payments')}}">{{trans('general.payments_made')}}</option>
                                                    <option @if (request()->is('my/payments/received')) selected @endif value="{{url('my/payments/received')}}">{{trans('general.payments_received')}}</option>
                                                </select>

                                            @elseif((str_contains(request()->getRequestUri(), 'panel/admin/invoices') && auth()->user()->role == 'admin'))
                                                <div class="flex-row-start flex-align-center mb-3">
                                                    <input type="text" class="isNumber form-control" id="invoice-lookup" placeholder="{{trans("admin.type_invoice")}}"/>
                                                    <button class="btn btn-primary" id="invoiceLookupBtn" style="border-radius: 0 !important;">{{trans("general.lookup")}}</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                @if ($transactions->count())
                                    @if (session('error_message'))
                                        <div class="alert alert-danger mb-3">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true"><i class="far fa-times-circle"></i></span>
                                            </button>

                                            <i class="fa fa-exclamation-triangle mr-2"></i> {{ trans('general.please_complete_all') }}
                                            <a href="{{ url('settings/page') }}#billing" class="text-white link-border">{{ trans('general.billing_information') }}</a>
                                        </div>
                                    @endif

                                    <div class="card">
                                        <div class="table-responsive">
                                            <table class="table table-striped m-0">
                                                <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    @if (request()->is('my/payments'))
                                                        <th scope="col">{{trans('general.paid_to')}}</th>
                                                    @endif
                                                    <th scope="col">{{trans('admin.date')}}</th>
                                                    <th scope="col">{{trans('admin.amount')}}</th>
                                                    <th scope="col">{{trans('admin.type')}}</th>
                                                    @if (request()->is('my/payments/received'))
                                                        <th scope="col">{{trans('general.paid_by')}}</th>
                                                        <th scope="col">{{trans('general.earnings')}}</th>
                                                    @endif
                                                    <th scope="col">{{trans('admin.status')}}</th>
                                                    @if (request()->is('my/payments') || (str_contains(request()->getRequestUri(), 'panel/admin/invoices') && auth()->user()->role == 'admin'))
                                                        <th> {{trans('general.invoice')}}</th>
                                                    @endif
                                                </tr>
                                                </thead>

                                                <tbody>

                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ str_pad($transaction->id, 4, "0", STR_PAD_LEFT) }}</td>
                                                        @if (request()->is('my/payments'))
                                                            <td>{{ $transaction->subscribed()->username ?? trans('general.no_available')}}</td>
                                                        @endif
                                                        <td>{{ Helper::formatDate($transaction->created_at) }}</td>
                                                        <td>{{ Helper::amountFormatDecimal($transaction->amount) }}</td>
                                                        <td>{{ __('general.'.$transaction->type) }}</td>
                                                        @if (request()->is('my/payments/received'))
                                                            <td>{{ $transaction->user()->username ?? trans('general.no_available') }}</td>
                                                            <td>
                                                                {{ Helper::amountFormatDecimal($transaction->earning_net_user) }}

                                                                @if ($transaction->percentage_applied)
                                                                    <a tabindex="0" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="{{trans('general.percentage_applied')}} {{ $transaction->percentage_applied }} {{trans('general.platform')}} @if ($transaction->direct_payment) ({{ __('general.direct_payment') }}) @endif">
                                                                        <i class="far fa-question-circle"></i>
                                                                    </a>

                                                                @endif
                                                            </td>
                                                        @endif
                                                        <td>
                                                            @if ($transaction->approved == '1')
                                                                <span class="badge badge-pill p-2 text-uppercase" style="background: #E9E9E9">{{trans('general.success')}}</span>
                                                            @elseif ($transaction->approved == '2')
                                                                <span class="badge badge-pill text-uppercase" style="background: #E9E9E9">{{trans('general.canceled')}}</span>
                                                                @if (request()->is('my/payments/received'))
                                                                    <a tabindex="0" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="{{trans('general.payment_canceled')}}">
                                                                        <i class="far fa-question-circle"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-pill badge-warning text-uppercase">{{trans('general.pending')}}</span>
                                                            @endif
                                                        </td>
                                                        @if (request()->is('my/payments') || (str_contains(request()->getRequestUri(), 'panel/admin/invoices') && auth()->user()->role == 'admin'))
                                                            <td>
                                                                @if ($transaction->approved == '1')
                                                                    <a href="{{url((str_contains(request()->getRequestUri(), 'panel/admin/invoices') ? 'panel/admin/invoices' : 'my/payments/invoice'), $transaction->id)}}"
                                                                       target="_blank"><i class="far fa-file-alt"></i> {{trans('general.invoice')}}</a>
                                                            </td>
                                                        @else
                                                            {{trans('general.no_available')}}
                                                        @endif
                                                        @endif
                                                    </tr>
                                                @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- card -->

                                    @if ($transactions->hasPages())
                                        {{ $transactions->onEachSide(0)->links() }}
                                    @endif

                                @else
                                    <div class="my-5 text-center">
                                <span class="btn-block mb-3">
                                  <i class="bi bi-receipt ico-no-result"></i>
                                </span>
                                        @if (request()->is('my/payments'))
                                            <h4 class="font-weight-light">{{trans('general.not_payment_made')}}</h4>
                                        @else
                                            <h4 class="font-weight-light">{{trans('general.not_payment_received')}}</h4>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
