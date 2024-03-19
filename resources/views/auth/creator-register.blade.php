@extends('layouts.app')

@section('title') {{trans('auth.sign_up')}} -@endsection

@section('content')

    <style>
    
        #max-size-image-container{
            /*position: absolute;*/
            /*top: 10px;*/
            /*right: 10px;*/
            /*bottom: 10px;*/
            /*left: 10px;*/
            /*overflow: hidden;*/
            /*padding: 0;*/
            /*border-radius: 40px 0px 40px 0px;*/
        }
    
        .text-box{
            position: absolute;
            top: calc(100vh - 190px);
            left: 50px;
        }
        
        .text-box p{
            color: white !important;
            margin: 0px;
            font-size: 28px;
            font-weight: 700;
            width: 90%;
            letter-spacing: 2px;
            text-align: right;
        }
        
        .text-box p.quotes{
            font-size: 65px;
            margin-bottom: -30px;
        }

        .vertical-middle{
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }
        
        video{
            min-width: 100%;
            min-height: 100%;
            object-fit: fill;
            position: absolute;
            left: -50%;
        }
        
        @media screen and (max-width: 1400px) {
            video{
                left: -90%;
            }
        }
        
    </style>

    <div class="page-wrapper">

        <div class="row mnh-100vh mr-0">
            <div class="col-12 col-lg-6 xol-xl-6 mnh-100">
                <div class="flex-col-start flex-align-center vertical-middle p-4">
                    <div class="flex-col-start flex-align-center w-60">
                        <img src="{{asset('public/img/bm-logo.png')}}" class="noSelect w-250px" />
                        <p class="font-25 font-weight-bold mt-2 mb-0">Create A Creator Account</p>
                        <p class="font-20">Let’s get started</p>
                    </div>

                    <form method="POST" action="{{ route('creator-register') }}" id="formLoginRegister" class="mt-3 mxw-100 mnw-75">
                        @csrf
                        <input type="hidden" name="return" value="{{ count($errors) > 0 ? old('return') : url()->previous() }}">

                        @if ($settings->captcha == 'on')
                            @captcha
                        @endif


                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.full_name')}}</label>
                            <div class="input-group input-theme mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                </div>
                                <input id="name" value="{{ old('name')}}" name="name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Enter here">
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.email')}}</label>
                            <div class="input-group input-theme mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                </div>
                                <input id="email" name="email" class="theme form-control" tabindex="2" aria-required="true" required type="text" value="{{ old('email')}}"  placeholder="Enter here">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.password')}}</label>
                            <div class="input-group input-theme mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                </div>
                                <input id="pass" name="password" class="theme form-control"  tabindex="3" value="" aria-required="true" type="password" placeholder="Enter password here" required="">
                            </div>
                        </div>


                        <div class="flex-row-between flex-align-center flex-wrap">
                            <div class="flex-row-start flex-align-center mt-1">
                                <input type="checkbox" name="agree_gdpr"  class="square-20" style="border: 2px" id="customCheckRegister" />
                                <span class="mb-0 ml-2 font-16">
                                    {{trans('admin.i_agree_gdpr')}}
                                    <a href="https://mator.io/privacy-policy/" target="_blank" class="font-14"> {{trans('admin.privacy_policy')}}</a>
                                </span>
                            </div>
                        </div>


                        <button type="submit" class="dark-btn w-100 mt-2" id="btnLoginRegister"><i></i>{{trans('auth.sign_up')}}</button>
                        <div class="alert alert-success mt-3 py-2 px-3 border-radius-5px display-none" id="checkAccount"></div>


                        @if($settings->facebook_login == 'on' || $settings->google_login == 'on' || $settings->apple_login == 'on')
                            <div class="flex-col-start flex-align-center mt-3">
                                <p class="text-center text-uppercase mb-0 font-16">Or</p>

                                <div class="flex-col-start flex-align-center mt-3 w-100">
                                    @if($settings->google_login == 'on')
                                        <a href="{{url('oauth/google')}}" class="light-btn w-100" style="border-radius: 10px !important;">
                                            <i class="fa-brands fa-google mr-2"></i>
                                            Login with Google
                                        </a>
                                    @endif

                                    @if($settings->facebook_login == 'on')
                                        <a href="{{url('oauth/facebook')}}" class="light-btn w-100 mt-3" style="border-radius: 10px !important;">
                                            <i class="fa-brands fa-facebook mr-2"></i>
                                            Login with Facebook
                                        </a>
                                    @endif

                                    @if($settings->apple_login == 'on')
                                        <a href="{{url('oauth/apple')}}" class="light-btn w-100 mt-3" style="border-radius: 10px !important;">
                                            <i class="fa-brands fa-apple mr-2"></i>
                                            Login with Apple
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </form>

                    <a href="{{url("login")}}" class="mt-4 color-dark font-16 text-center">{{trans("auth.already_have_an_account")}} <span class="font-weight-bold">{{trans("auth.login")}}</span></a>

                    <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin">
                        <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"></ul>
                    </div>
                    @include('errors.errors-forms')

                </div>
            </div>


            <div class="d-none d-lg-flex col-lg-6 xol-xl-6 p-0 mnh-100 position-relative">
                <div id="max-size-image-container">
                    <!--<img src="{{asset('public/img/advert/creator_1.jpg')}}" class="noSelect" />-->
                    <video autoplay muted loop id="myVideo">
                        <source src="{{asset('public/videos/video1.mp4')}}" type="video/mp4">
                        Your browser does not support HTML5 video.
                    </video>
                </div>
                <div class="text-box">
                    <!--<p class="quotes">&ldquo;</p>-->
                    <!--<p class="text">Mator.io was a game-changer for content revenue, fuelling growth and engagement.</p>-->
                </div>
            </div>
        </div>







    </div>


@endsection
