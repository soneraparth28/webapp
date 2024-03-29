@extends('layouts.app')

@section('title')
    {{trans('users.payout_method')}} -
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
                                    <i class="bi bi-credit-card mr-2"></i>

                                    <p class="font-22 font-weight-bold mb-0 ml-3"> {{trans('users.payout_method')}}</p>
                                </div>
                                <p class="mb-0 color-gray font-18"> {{trans('general.default_payout_method')}}:
                                    @if(auth()->user()->payment_gateway != '')
                                        <strong class="text-success">
                                            {{auth()->user()->payment_gateway == 'Bank' ? trans('users.bank_transfer') : auth()->user()->payment_gateway}}
                                        </strong>
                                    @else
                                        <strong class="text-danger">{{trans('general.none')}}</strong>
                                    @endif
                                </p>

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

                                                @if (session('error'))
                                                    <div class="alert alert-danger">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <i class="bi-exclamation-triangle mr-2"></i> {{ session('error') }}
                                                    </div>
                                                @endif

                                                @include('errors.errors-forms')

                                                @if (auth()->user()->verified_id != 'yes' && auth()->user()->balance == 0.00)

                                                    <div class="alert alert-danger mb-3">
                                                        <ul class="list-unstyled m-0">
                                                            <li>
                                                                <i class="fa fa-exclamation-triangle"></i> {{trans('general.verified_account_info')}}
                                                                <a href="{{url('settings/verify/account')}}"
                                                                   class="text-white link-border">{{trans('general.verify_account')}}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif

                                                @if (auth()->user()->verified_id == 'yes' || auth()->user()->balance != 0.00)

                                                    <div class="alert alert-primary alert-dismissible fade show"
                                                         role="alert">
                                                        <i class="fa fa-info-circle mr-2"></i>
                                                        <span> {{ trans('general.payout_method_info') }}
          <small
              class="btn-block">* {{ trans('general.payment_process_days', ['days' => $settings->days_process_withdrawals]) }}</small>
            </span>
                                                    </div>

                                                    @if( $settings->payout_method_paypal == 'on' )
                                                        <!--============ START PAYPAL ============-->
                                                        <div class="custom-control custom-radio mb-3">
                                                            <input name="payment_gateway" value="PayPal" id="radio1"
                                                                   class="custom-control-input"
                                                                   @if (auth()->user()->payment_gateway == 'PayPal') checked
                                                                   @endif type="radio">
                                                            <label class="custom-control-label" for="radio1">
                                    <span><img
                                            src="{{url('public/img/payments', 'paypal.png' )}}"
                                            width="70"/></span>
                                                                <small
                                                                    class="w-100 d-block">* {{trans('general.processor_fees_may_apply')}}</small>
                                                            </label>
                                                        </div>

                                                        <form method="POST"
                                                              action="{{ url('settings/payout/method/paypal') }}"
                                                              id="PayPal"
                                                              @if (auth()->user()->payment_gateway != 'PayPal') class="display-none" @endif>
                                                            @csrf

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="fab fa-paypal"></i></span>
                                                                    </div>
                                                                    <input class="form-control" name="email_paypal"
                                                                           value="{{auth()->user()->paypal_account == '' ? old('email_paypal') : auth()->user()->paypal_account}}"
                                                                           placeholder="{{trans('general.email_paypal')}}"
                                                                           required type="email">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="far fa-envelope"></i></span>
                                                                    </div>
                                                                    <input class="form-control"
                                                                           name="email_paypal_confirmation"
                                                                           placeholder="{{trans('general.confirm_email_paypal')}}"
                                                                           required
                                                                           type="email">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-1 btn-success btn-block"
                                                                    type="submit">{{trans('general.save_payout_method')}}</button>
                                                        </form>
                                                        <!--============ END PAYPAL ============-->
                                                    @endif

                                                    @if( $settings->payout_method_payoneer == 'on' )
                                                        <!--============ START PAYONEER ============-->
                                                        <div class="custom-control custom-radio mb-3 mt-3">
                                                            <input name="payment_gateway" value="Payoneer" id="radio2"
                                                                   class="custom-control-input"
                                                                   @if (auth()->user()->payment_gateway == 'Payoneer') checked
                                                                   @endif type="radio">
                                                            <label class="custom-control-label" for="radio2">
                                    <span><img
                                            src="{{url('public/img/payments', auth()->user()->dark_mode == 'off' ? 'payoneer.png' : 'payoneer-white.png')}}"
                                            width="110"/></span>
                                                                <small
                                                                    class="w-100 d-block">* {{trans('general.processor_fees_may_apply')}}</small>
                                                            </label>
                                                        </div>

                                                        <form method="POST"
                                                              action="{{ url('settings/payout/method/payoneer') }}"
                                                              id="Payoneer"
                                                              @if (auth()->user()->payment_gateway != 'Payoneer') class="display-none" @endif>
                                                            @csrf

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="far fa-envelope"></i></span>
                                                                    </div>
                                                                    <input class="form-control" name="email_payoneer"
                                                                           value="{{auth()->user()->payoneer_account == '' ? old('email_payoneer') : auth()->user()->payoneer_account}}"
                                                                           placeholder="{{trans('general.email_payoneer')}}"
                                                                           required type="email">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="far fa-envelope"></i></span>
                                                                    </div>
                                                                    <input class="form-control"
                                                                           name="email_payoneer_confirmation"
                                                                           placeholder="{{trans('general.confirm_email_payoneer')}}"
                                                                           required
                                                                           type="email">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-1 btn-success btn-block"
                                                                    type="submit">{{trans('general.save_payout_method')}}</button>
                                                        </form>
                                                        <!--============ END PAYONEER ============-->
                                                    @endif

                                                    @if( $settings->payout_method_zelle == 'on' )
                                                        <!--============ START ZELLE ============-->
                                                        <div class="custom-control custom-radio mb-3 mt-3">
                                                            <input name="payment_gateway" value="Zelle" id="radio3"
                                                                   class="custom-control-input"
                                                                   @if (auth()->user()->payment_gateway == 'Zelle') checked
                                                                   @endif type="radio">
                                                            <label class="custom-control-label" for="radio3">
                                    <span><img
                                            src="{{url('public/img/payments', auth()->user()->dark_mode == 'off' ? 'zelle.png' : 'zelle-white.png')}}"
                                            width="50"/></span>
                                                                <small
                                                                    class="w-100 d-block">* {{trans('general.processor_fees_may_apply')}}</small>
                                                            </label>
                                                        </div>

                                                        <form method="POST"
                                                              action="{{ url('settings/payout/method/zelle') }}"
                                                              id="Zelle"
                                                              @if (auth()->user()->payment_gateway != 'Zelle') class="display-none" @endif>
                                                            @csrf

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="far fa-envelope"></i></span>
                                                                    </div>
                                                                    <input class="form-control" name="email_zelle"
                                                                           value="{{auth()->user()->zelle_account == '' ? old('email_zelle') : auth()->user()->zelle_account}}"
                                                                           placeholder="{{trans('general.email_zelle')}}"
                                                                           required type="email">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <div class="input-group mb-4">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i
                                                                                class="far fa-envelope"></i></span>
                                                                    </div>
                                                                    <input class="form-control"
                                                                           name="email_zelle_confirmation"
                                                                           placeholder="{{trans('general.confirm_email_zelle')}}"
                                                                           required
                                                                           type="email">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-1 btn-success btn-block"
                                                                    type="submit">{{trans('general.save_payout_method')}}</button>
                                                        </form>
                                                        <!--============ END ZELLE ============-->
                                                    @endif

                                                    @if( $settings->payout_method_bank == 'on' )
                                                        <!--============ START BANK TRANSFER ============-->
                                                        <div class="custom-control custom-radio mb-3 mt-3">
                                                            <input name="payment_gateway" value="Bank" id="radio4"
                                                                   class="custom-control-input"
                                                                   @if (auth()->user()->payment_gateway == 'Bank') checked
                                                                   @endif type="radio">
                                                            <label class="custom-control-label" for="radio4">
                                                                <span><strong><i
                                                                            class="fa fa-university mr-1 icon-sm-radio"></i> {{trans('users.bank_transfer')}}</strong></span>
                                                                <small
                                                                    class="w-100 d-block">* {{trans('general.processor_fees_may_apply')}}</small>
                                                            </label>
                                                        </div>

                                                        <form method="POST"
                                                              action="{{ url('settings/payout/method/bank') }}"
                                                              id="Bank"
                                                              @if (auth()->user()->payment_gateway != 'Bank') class="display-none" @endif>

                                                            @csrf
                                                            <div class="form-group">
                                    <textarea name="bank_details" rows="5" cols="40" class="form-control" required
                                              placeholder="{{trans('users.bank_details')}}">{{auth()->user()->bank == '' ? old('bank_details') : auth()->user()->bank}}</textarea>
                                                            </div>

                                                            <div class="alert alert-primary alert-dismissible fade show"
                                                                 role="alert">
                                                                <i class="fa fa-info-circle mr-2"></i>
                                                                <span>{{trans('users.bank_details')}}</span>
                                                            </div>

                                                            <button class="btn btn-1 btn-success btn-block"
                                                                    type="submit">{{trans('general.save_payout_method')}}</button>
                                                        </form>
                                                        <!--============ END BANK TRANSFER ============-->
                                                    @endif

                                                @endif

                                                @if (auth()->user()->verified_id == 'yes'
                                                    && $settings->stripe_connect
                                                    && isset(auth()->user()->country()->country_code)
                                                    && in_array(auth()->user()->country()->country_code, $stripeConnectCountries)
                                                    )

                                                    <h6 class="mt-5">Stripe
                                                        Connect @if (auth()->user()->completed_stripe_onboarding)
                                                            <span
                                                                class="badge badge-pill badge-success font-weight-light opacity-75">{{ __('general.connected') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill badge-danger font-weight-light opacity-75">{{ __('general.not_connected') }}</span>
                                                        @endif </h6>
                                                    <small
                                                        class="d-block w-100 mb-3">{{ __('general.stripe_connect_desc') }}</small>


                                                    <a href="{{ route('redirect.stripe') }}"
                                                       class="btn w-100 btn-lg btn-primary btn-arrow">

                                                        @if (! auth()->user()->completed_stripe_onboarding)
                                                            {{ __('general.connect_stripe_account') }}

                                                        @else
                                                            {{ __('general.view_stripe_account') }}
                                                        @endif
                                                    </a>

                                                @endif
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

@section('javascript')
    <script type="text/javascript">

        $('input[name=payment_gateway]').on('click', function () {

            if ($(this).val() == 'PayPal') {
                $('#PayPal').slideDown();
            } else {
                $('#PayPal').slideUp();
            }

            if ($(this).val() == 'Payoneer') {
                $('#Payoneer').slideDown();
            } else {
                $('#Payoneer').slideUp();
            }

            if ($(this).val() == 'Zelle') {
                $('#Zelle').slideDown();
            } else {
                $('#Zelle').slideUp();
            }

            if ($(this).val() == 'Bank') {
                $('#Bank').slideDown();
            } else {
                $('#Bank').slideUp();
            }

        });
    </script>
@endsection
