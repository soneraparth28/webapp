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



                                @if (session('status'))
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>

                                        {{ trans('admin.success_update') }}
                                    </div>
                                @endif

                                @include('errors.errors-forms')

                                <form method="POST" action="{{ url('settings/page') }}" id="formEditPage" accept-charset="UTF-8" enctype="multipart/form-data">

                                    @csrf

                                    <input type="hidden" id="featured_content" name="featured_content" value="{{auth()->user()->featured_content}}">

                                    <div class="row">
                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('auth.full_name')}} *</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-user"></i></span>
                                                    </div>
                                                    <input class="form-control" name="full_name" placeholder="{{trans('auth.full_name')}}" value="{{auth()->user()->name}}"  type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>
                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('auth.email')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                                    </div>
                                                    <input class="form-control" placeholder="{{trans('auth.email')}}" {!! auth()->user()->isSuperAdmin() ? 'name="email"' : 'disabled' !!} value="{{auth()->user()->email}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('auth.username')}} *</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text pr-2">{{Helper::removeHTPP(url('/'))}}/</span>
                                                    </div>
                                                    <input class="form-control" name="username" maxlength="25" placeholder="{{trans('auth.username')}}" value="{{auth()->user()->username}}"  type="text">
                                                </div>
                                                @if (auth()->user()->verified_id == 'yes')
                                                    <div class="flex-row-start flex-align-center">
                                                        <label class="form-switch mb-0">
                                                            <input type="checkbox" class="custom-control-input" name="hide_name" value="yes" @if (auth()->user()->hide_name == 'yes') checked @endif id="customSwitch1">
                                                            <i></i>
                                                        </label>
                                                        <p class="mb-0 ml-2">{{ trans('general.hide_name') }}</p>
                                                    </div>
                                                @endif
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.birthdate')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" @if (auth()->user()->birthdate_changed == 'yes') disabled="disabled" @endif><i class="fa fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input class="form-control datepicker" @if (auth()->user()->birthdate_changed == 'yes') disabled  @endif name="birthdate" placeholder="{{trans('general.birthdate')}} *"  value="{{date(Helper::formatDatepicker(), strtotime(auth()->user()->birthdate))}}" autocomplete="off" type="text">
                                                </div>
                                                <p class=" mb-0">
                                                    {{ trans('general.valid_formats') }}
                                                    {{ now()->subYears(18)->format(Helper::formatDatepicker()) }}
                                                </p>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('users.profession_ocupation')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-user-tie"></i></span>
                                                    </div>
                                                    <input class="form-control" name="profession" placeholder="{{trans('users.profession_ocupation')}}" value="{{auth()->user()->profession}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.language')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-language"></i></span>
                                                    </div>
                                                    <select name="language" class="form-control custom-select">
                                                        <option @if (auth()->user()->language == '') selected="selected" @endif value="">({{trans('general.language')}}) {{ __('general.not_specified') }}</option>
                                                        @foreach (Languages::orderBy('name')->get() as $languages)
                                                            <option @if (auth()->user()->language == $languages->abbreviation) selected="selected" @endif value="{{$languages->abbreviation}}">{{ $languages->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.gender')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-venus-mars"></i></span>
                                                    </div>
                                                    <select name="gender" class="form-control custom-select">
                                                        <option @if (auth()->user()->gender == '' ) selected="selected" @endif value="">({{trans('general.gender')}}) {{ __('general.not_specified') }}</option>
                                                        @foreach ($genders as $gender)
                                                            <option @if (auth()->user()->gender == $gender) selected="selected" @endif value="{{$gender}}">{{ __('general.'.$gender) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('users.website')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-link"></i></span>
                                                    </div>
                                                    <input class="form-control" name="website" placeholder="{{trans('users.website')}}"  value="{{auth()->user()->website}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->

                                        </div>
                                        
                                        <div class="col-12 col-lg-6 mt-1">
                                          <div class="form-group">
                                              <label>App name</label>
                                              <div class="input-group mb-2">
                                                  <div class="input-group-prepend">
                                                      <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                                  </div>
                                                  <input class="form-control" name="subdomain" placeholder="App name"  value="{{auth()->user()->subdomain}}" type="text">
                                              </div>
                                          </div><!-- End form-group -->
                                        </div>
                                        {{--                                <div class="col-12 col-lg-6 mt-1">--}}
                                        {{--                                    <div class="form-group">--}}
                                        {{--                                        <label>{{trans('general.category')}}</label>--}}
                                        {{--                                        <div class="input-group mb-2">--}}
                                        {{--                                            <div class="input-group-prepend">--}}
                                        {{--                                                <span class="input-group-text"><i class="far fa-lightbulb"></i></span>--}}
                                        {{--                                            </div>--}}
                                        {{--                                            <select name="categories_id[]" multiple class="form-control categoriesMultiple" >--}}
                                        {{--                                                @foreach (Categories::where('mode','on')->orderBy('name')->get() as $category)--}}
                                        {{--                                                    <option @if (in_array($category->id, $categories)) selected="selected" @endif value="{{$category->id}}">{{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}</option>--}}
                                        {{--                                                @endforeach--}}
                                        {{--                                            </select>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div><!-- End form-group -->--}}
                                        {{--                                </div>--}}
                                        <div class="col-12 mt-1">
                                            <div class="form-group">
                                                <label class="w-100">
                                                    Profile description
                                                    <span id="the-count" class="float-right d-inline">
                                                <span id="current"></span>
                                                <span id="maximum">/ {{$settings->story_length}}</span>
                                            </span>
                                                </label>
                                                <textarea name="story" id="story" style="height: 115px !important;" class="form-control scrollError">{{auth()->user()->story ? auth()->user()->story : old('story') }}</textarea>
                                            </div><!-- End form-group -->
                                        </div>
                                    </div>




                                    <div class="row form-group mb-0">


                                        <div class="col-lg-12 py-2">
                                            <h6 class="font-18">-- {{trans('general.billing_information')}}</h6>
                                        </div>

                                        <div class="col-12 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.company')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-building"></i></span>
                                                    </div>
                                                    <input class="form-control" name="company" placeholder="{{trans('general.company')}}"  value="{{auth()->user()->company}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>
                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.select_your_country')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                                    </div>
                                                    <select name="countries_id" class="form-control custom-select">
                                                        <option value="">{{trans('general.select_your_country')}} *</option>
                                                        @foreach (Countries::orderBy('country_name')->get() as $country)
                                                            <option @if (auth()->user()->countries_id == $country->id ) selected="selected" @endif value="{{$country->id}}">{{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.city')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-pin"></i></span>
                                                    </div>
                                                    <input class="form-control" name="city" placeholder="{{trans('general.city')}}"  value="{{auth()->user()->city}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>


                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.address')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marked-alt"></i></span>
                                                    </div>
                                                    <input class="form-control" name="address" placeholder="{{trans('general.address')}}"  value="{{auth()->user()->address}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>

                                        <div class="col-12 col-lg-6 mt-1">
                                            <div class="form-group">
                                                <label>{{trans('general.zip')}}</label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                                    </div>
                                                    <input class="form-control" name="zip" placeholder="{{trans('general.zip')}}"  value="{{auth()->user()->zip}}" type="text">
                                                </div>
                                            </div><!-- End form-group -->
                                        </div>
                                    </div><!-- End Row Form Group -->

                                    @if (auth()->user()->verified_id == 'yes')
                                        <div class="row form-group mb-0">
                                            <div class="col-lg-12 py-2">
                                                <h6 class="font-18">-- {{trans('admin.profiles_social')}}</h6>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                    </div>
                                                    <input class="form-control" name="facebook" placeholder="https://facebook.com/username"  value="{{auth()->user()->facebook}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                                    </div>
                                                    <input class="form-control" name="twitter" placeholder="https://twitter.com/username"  value="{{auth()->user()->twitter}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->

                                        <div class="row form-group mb-0">
                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                    </div>
                                                    <input class="form-control" name="instagram" placeholder="https://instagram.com/username"  value="{{auth()->user()->instagram}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                                    </div>
                                                    <input class="form-control" name="youtube" placeholder="https://youtube.com/username"  value="{{auth()->user()->youtube}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->

                                        <div class="row form-group mb-0">
                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-pinterest-p"></i></span>
                                                    </div>
                                                    <input class="form-control" name="pinterest" placeholder="https://pinterest.com/username"  value="{{auth()->user()->pinterest}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-github"></i></span>
                                                    </div>
                                                    <input class="form-control" name="github" placeholder="https://github.com/username"  value="{{auth()->user()->github}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->

                                        <div class="row form-group mb-0">
                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="bi bi-snapchat"></i></span>
                                                    </div>
                                                    <input class="form-control" name="snapchat" placeholder="https://www.snapchat.com/add/username"  value="{{auth()->user()->snapchat}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                                    </div>
                                                    <input class="form-control" name="tiktok" placeholder="https://www.tiktok.com/@username"  value="{{auth()->user()->tiktok}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->

                                        <div class="row form-group mb-0">
                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="bi bi-telegram"></i></span>
                                                    </div>
                                                    <input class="form-control" name="telegram" placeholder="https://t.me/username"  value="{{auth()->user()->telegram}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="bi bi-twitch"></i></span>
                                                    </div>
                                                    <input class="form-control" name="twitch" placeholder="https://www.twitch.tv/username"  value="{{auth()->user()->twitch}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->

                                        <div class="row form-group mb-0">
                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="bi bi-discord"></i></span>
                                                    </div>
                                                    <input class="form-control" name="discord" placeholder="https://discord.gg/username"  value="{{auth()->user()->discord}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fab fa-vk"></i></span>
                                                    </div>
                                                    <input class="form-control" name="vk" placeholder="https://vk.com/username"  value="{{auth()->user()->vk}}" type="text">
                                                </div>
                                            </div><!-- ./col-md-6 -->
                                        </div><!-- End Row Form Group -->
                                @endif

                                <!-- Alert -->
                                    <div class="alert alert-danger my-3 display-none" id="errorUdpateEditPage">
                                        <ul class="list-unstyled m-0" id="showErrorsUdpatePage"><li></li></ul>
                                    </div><!-- Alert -->

                                    <button class="dark-btn w-50 mt-2" data-msg-success="{{ trans('admin.success_update') }}" id="saveChangesEditPage" type="submit"><i></i> {{trans('general.save_changes')}}</button>
                                </form>


                            </div>
                        </div>



                    </div>
                </div>



            </div>

        </div>

    </div>

@endsection
