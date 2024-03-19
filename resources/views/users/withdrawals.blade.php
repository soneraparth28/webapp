@extends('layouts.app')

@section('title')
    {{trans('general.withdrawals')}} -
@endsection

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
                                    <i class="bi bi-arrow-left-right font-22"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">Withdrawals</p>
                                </div>
                                <p class="mb-0 color-gray font-18">Withdraw your amount</p>

                                @if(!auth()->user()->payment_gateway)
                                    <div class="mt-4 card border-radius-5px">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="alert alert-warning alert-dismissible" role="alert">
          <span class="alert-inner--text"><i class="far fa-credit-card mr-2"></i> Please select a
            <a href="{{ url('settings/payout/method') }}" class="text-white link-border">Payout method</a>
          </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(auth()->user()->balance >= 50 && auth()->user()->payment_gateway)
                                <div class="modal fade" id="withdrawal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="post" action="{{ url('settings/withdrawals') }}">
                                            {{ csrf_field() }}
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel"> {{trans('general.make_withdrawal')}}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">

                                                        @if($settings->type_withdrawals)
                                                            <div class="form-group">
                                                                <label for="withdrawal_amount">{{ __('general.withdrawal_amount') }}</label>
                                                                <input type="number" step="0.01" required name="amount" class="form-control" id="withdrawal_amount" aria-describedby="withdrawal_amount_help" placeholder="{{ __('general.withdrawal_amount') }}" min="50" max="{{ auth()->user()->balance }}">
                                                                <small id="withdrawal_amount_help" class="form-text text-muted">{{ __('general.withdrawal_amount_help_text', ['min' => Helper::amountFormatDecimal(50), 'max' => Helper::amountFormatDecimal(auth()->user()->balance)]) }}</small>
                                                            </div>
                                                        @else
                                                            <div class="flex-col-start">
                                                                <p class="font-18 mb-0 font-weight-bold">Balance : {{Helper::amountFormatDecimal(auth()->user()->balance)}}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" href="#" class="light-btn" data-dismiss="modal">Close</button>
                                                <button type="submit" class="dark-btn">Make Withdrawal Request</button>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                @endif

                                <div class="mt-4 card border-radius-5px">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                @if (session('status'))
                                                    <div class="alert alert-success">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <i class="bi-check2 mr-2"></i> {{ session('status') }}
                                                    </div>
                                                @endif
                                                @include('errors.errors-forms')
                                            </div>
                                            <div class="col-6">
                                                <div class="flex-col-start">
                                                    <p class="font-18 mb-0 font-weight-bold">Balance</p>
                                                    <p class="font-18 mb-0 font-weight-bold">{{Helper::amountFormatDecimal(auth()->user()->balance)}}</p>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="flex-row-end flex-align-center">
                                                    @if(auth()->user()->balance >= 50 && auth()->user()->payment_gateway)
                                                    <button class="dark-btn font-18 text-nowrap" data-toggle="modal" data-target="#withdrawal_modal">
                                                        {{trans('general.make_withdrawal')}}
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-1 col-12 col-lg-6">
                                                <div class="flex-col-start">
                                                    <p class="color-gray font-16 mb-0">Amount minimum withdrawal $50
                                                        USD</p>
                                                    <p class="color-gray font-14 mb-0">* Your payout will be processed
                                                        on May 15, 2023.</p>
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
                                                            <th scope="col">ID</th>
                                                            <th scope="col">{{trans('admin.amount')}}</th>
                                                            <th scope="col">{{trans('admin.method')}}</th>
                                                            <th scope="col">{{trans('admin.date')}}</th>
                                                            <th scope="col">{{trans('admin.status')}}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>

                                                        @if ($withdrawals->count() != 0)
                                                            @foreach ($withdrawals as $withdrawal)
                                                                <tr>
                                                                    <td>{{$withdrawal->id}}</td>
                                                                    <td>{{Helper::amountFormatDecimal($withdrawal->amount)}}</td>
                                                                    <td>{{$withdrawal->gateway == 'Bank' ? trans('general.bank') : $withdrawal->gateway}}</td>
                                                                    <td>{{Helper::formatDate($withdrawal->date)}}</td>
                                                                    <td>@if ( $withdrawal->status == 'paid' )
                                                                            <span
                                                                                class="border-radius-10px py-1 px-2 text-uppercase"
                                                                                style="background: #E9E9E9">{{trans('general.paid')}}</span>
                                                                        @else
                                                                            <span
                                                                                class="border-radius-10px py-1 px-2 text-uppercase"
                                                                                style="background: #E9E9E9">{{trans('general.pending_to_pay')}}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        @else
                                                            <tr>
                                                                <td>{{ trans('general.no_transactions_yet') }}</td>
                                                                <td></td>
                                                                <td></td>
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
