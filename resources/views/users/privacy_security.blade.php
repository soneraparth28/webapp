@extends('layouts.app')

@section('title') {{trans('general.privacy_security')}} -@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
    <?php $pageTitle = "Privacy"; ?>

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
                                    <i class="bi bi-shield-check font-22"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">Privacy</p>
                                </div>

                                @if (auth()->user()->verified_id == 'yes')

                                    <form method="POST" action="{{ url('privacy/security') }}" class="mt-4">

                                        @csrf

                                        <div class="form-group">
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="hide_profile" value="yes" @if (auth()->user()->hide_profile == 'yes') checked @endif id="customSwitch1">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.hide_profile') }} {{ __('general.info_hide_profile') }}</p>
                                            </div>
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="hide_last_seen" value="yes" @if (auth()->user()->hide_last_seen == 'yes') checked @endif id="customSwitch2">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.hide_last_seen') }}</p>
                                            </div>
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="active_status_online" value="yes" @if (auth()->user()->active_status_online == 'yes') checked @endif id="customSwitch6">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.active_status_online') }}</p>
                                            </div>
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="hide_count_subscribers" value="yes" @if (auth()->user()->hide_count_subscribers == 'yes') checked @endif id="customSwitch3">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.hide_count_subscribers') }}</p>
                                            </div>
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="hide_my_country" value="yes" @if (auth()->user()->hide_my_country == 'yes') checked @endif id="customSwitch4">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.hide_my_country') }}</p>
                                            </div>
                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="show_my_birthdate" value="yes" @if (auth()->user()->show_my_birthdate == 'yes') checked @endif id="customSwitch5">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">{{ __('general.show_my_birthdate') }}</p>
                                            </div>

                                            <h5 class="mt-5">{{ __('general.security') }}</h5>

                                            <div class="flex-row-start flex-align-center mb-4">
                                                <label class="form-switch mb-0">
                                                    <input type="checkbox" class="custom-control-input" name="two_factor_auth" value="yes" @if (auth()->user()->two_factor_auth == 'yes') checked @endif id="customSwitch7">
                                                    <i></i>
                                                </label>
                                                <p class="mb-0 ml-2">
                                                    {{ __('general.two_step_auth') }}

                                                    <i class="bi bi-info-circle text-muted ml-2" data-toggle="tooltip" data-placement="top" title="{{trans('general.two_step_auth_info')}}"></i>
                                                </p>
                                            </div>

                                            <h5 class="mt-5">Blocked countries</h5>

                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                                </div>

                                                <select name="countries[]" multiple="multiple" class="form-control select2Multi" id="select2Countries">
                                                    @foreach (Countries::orderBy('country_name', 'asc')->get() as $country)
                                                        <option @if (in_array($country->country_code, auth()->user()->blockedCountries())) selected="selected" @endif value="{{$country->country_code}}">
                                                            {{ $country->country_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div><!-- End form-group -->



                                        <button class="dark-btn mnw-50" onClick="this.form.submit(); this.disabled=true; this.innerText='{{ __('general.please_wait')}}';" type="submit">{{ __('general.save_changes')}}</button>

                                    </form>
                                @endif

                                @if (! auth()->user()->isSuperAdmin())
                                    {{--                            <h5 class="mt-5">{{ __('general.delete_account') }}</h5>--}}
                                    {{--                            <small class="w-100">{{ __('general.delete_account_alert') }}</small>--}}

                                    {{--                            <div class="w-100 d-block mt-2 mb-5">--}}
                                    {{--                                <a class="btn btn-main btn-danger pr-3 pl-3" href="{{ url('account/delete') }}">--}}
                                    {{--                                    <i class="feather icon-user-x mr-1"></i> {{ __('general.delete_account') }}</small>--}}
                                    {{--                                </a>--}}
                                    {{--                            </div>--}}
                                @endif
                            </div>
                        </div>





                    </div>
                </div>



            </div>

        </div>

    </div>

@endsection

