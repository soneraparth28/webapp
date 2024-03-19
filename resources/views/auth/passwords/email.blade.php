@extends('layouts.app')

@section('title') {{trans('auth.password_recover')}} -@endsection

@section('css')
  <script type="text/javascript">
      var error_scrollelement = {{ count($errors) > 0 ? 'true' : 'false' }};
  </script>
@endsection
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
                        <!--<p class="font-25 font-weight-bold mt-2 mb-0">Welcome back</p>-->
                        <!--<p class="font-20">Please enter your details</p>-->
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" id="formLoginRegister" class="mt-3 mxw-100 mnw-75">
                        @csrf
                        <input type="hidden" name="return" value="{{ count($errors) > 0 ? old('return') : url()->previous() }}">

                        @if ($settings->captcha == 'on')
                        @captcha
                        @endif


                        <div class="form-group">
                            <label class="font-16 font-weight-bold mb-0">{{trans('auth.email')}}</label>
                            <div class="input-group input-theme mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                </div>
                                <input  id="name" name="email" class="theme form-control @if (count($errors) > 0) is-invalid @endif" value="{{ old('email')}}" tabindex="1" aria-required="true" required type="email" placeholder="{{trans('auth.email')}}">
                            </div>
                        </div>

            



                        <button type="submit" class="dark-btn w-100 mt-2" id="btnLoginRegister" style="border-radius: 10px !important;"><i></i>{{trans('auth.send_pass_reset')}}</button>


                       

                    </form>

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
        
            
        @if (session('status'))
        <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin" bis_skin_checked="1" style="display: block;">
                        <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"><li class="color-light"><i class="far fa-times-circle color-light"></i>  {{{ session('status') }}}</li></ul>
                    </div>
                    @endif


        @if(session('login_required'))
        <div class="alert alert-danger" id="dangerAlert">
            <i class="fa fa-exclamation-triangle"></i> {{trans('auth.login_required')   }}
        </div>
        @endif





    </div>


@endsection
