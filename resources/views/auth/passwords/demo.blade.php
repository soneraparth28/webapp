@extends('layouts.app')

@section('title') {{trans('auth.login')}} -@endsection
<style>
    .vertical-middle{
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }
    
    #max-size-image-container{
        background-image: url({{asset('public/img/Login.jpg')}});
        background-repeat: no-repeat;
        background-size: cover;
        background-position: bottom;
    }
    
    video{
        
        min-width: 100%;
        min-height: 100%;
        object-fit: fill;
    }
</style>
@section('content')

    <div class="page-wrapper">

        <div class="row mnh-100vh mr-0">
            <div class="col-12 col-lg-6 xol-xl-5 mnh-100">
                <div class="flex-col-start flex-align-center vertical-middle p-4">
                    <div class="flex-col-start flex-align-center ">
                        <img src="{{asset('public/img/bm-logo.png')}}" class="noSelect w-250px" />
                        <p class="font-25 font-weight-bold mt-2 mb-0">Welcome back</p>
                        <p class="font-20">Please enter your details</p>
                    </div>

                    <form method="POST" action="{{ url('login') }}" id="formLoginRegister" class="mt-3 mxw-100 mnw-75">
                        @csrf
                        <input type="hidden" name="return" value="{{ count($errors) > 0 ? old('return') : url()->previous() }}">

                        @if ($settings->captcha == 'on')
                        @captcha
                        @endif


                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.username_or_email')}}</label>
                            <div class="input-group input-theme mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                </div>
                                <input id="name" name="username_email" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Enter here">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.password')}}</label>
                            <div class="input-group input-theme mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                </div>
                                <input id="password" name="password" class="theme form-control"  tabindex="2" value="" aria-required="true" type="password" placeholder="Enter password here" required="">
                            </div>
                        </div>


                        <div class="flex-row-between flex-align-center flex-wrap mb-2">
                            <div class="flex-row-start flex-align-center mt-1">
                                <input type="checkbox" class="square-20" style="border: 2px" id="customCheckLogin" {{ old('remember') ? 'checked' : '' }} />
                                <p class="mb-0 ml-2 font-16">Remember me</p>
                            </div>

                            <a href="{{url('password/reset')}}" class="forgot-pass font-16 color-dark mt-1">Forgot Password ?</a>
                        </div>


                        <button type="submit" class="dark-btn w-100 mt-2" id="btnLoginRegister" style="border-radius: 10px !important;"><i></i>{{trans('auth.login')}}</button>


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

                    <a href="{{url("signup")}}" class="mt-4 color-dark font-16 text-center">{{trans("auth.not_have_account")}} <span class="font-weight-bold">{{trans("auth.signup")}}</span></a>

                    <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin">
                        <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"></ul>
                    </div>

                    @include('errors.errors-forms')
                </div>
            </div>


            <div class="d-none d-lg-flex col-lg-6 xol-xl-7 p-0 mnh-100 position-relative">
                <div id="max-size-image-container">
                    <!--<img src="{{asset('public/img/Login.jpg')}}" class="noSelect" />-->
                    <!--<video autoplay muted loop id="myVideo">-->
                    <!--    <source src="{{asset('public/videos/login-video.mp4')}}" type="video/mp4">-->
                    <!--    Your browser does not support HTML5 video.-->
                    <!--</video>-->
                </div>
            </div>
        </div>


        @if(session('login_required'))
        <div class="alert alert-danger" id="dangerAlert">
            <i class="fa fa-exclamation-triangle"></i> {{trans('auth.login_required')   }}
        </div>
        @endif





    </div>


@endsection
