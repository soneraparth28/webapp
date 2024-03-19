<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{--{{ auth()->check() && auth()->user()->dark_mode = 'on'; }}--}}

<?php if(auth()->check()) auth()->user()->dark_mode = "on"; ?>
<head>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description_custom')@if(!Request::route()->named('seo') && !Request::route()->named('profile')){{trans('seo.description')}}@endif">
    <meta name="keywords" content="@yield('keywords_custom'){{ trans('seo.keywords') }}" />
    <meta name="theme-color" content="{{ auth()->check() && auth()->user()->dark_mode == 'on' ? '#100f0f' : $settings->color_default }}">
    {{--  <meta name="theme-color" content="{{ auth()->check() && auth()->user()->dark_mode == 'on' ? '#303030' : $settings->color_default }}">--}}
    <title>{{ auth()->check() && User::notificationsCount() ? '('.User::notificationsCount().') ' : '' }}@section('title')@show @if( isset( $settings->title ) ){{$settings->title}}@endif</title>
    <!-- Favicon -->
    <link href="{{ url('public/img', $settings->favicon) }}" rel="icon">

    @include('includes.css_general')


    {{--  @laravelPWA--}}

    @yield('css')

    @if($settings->google_analytics != '')
        {!! $settings->google_analytics !!}
    @endif

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QH6K6W259J"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-QH6K6W259J');
    </script>
    <script>!function(e,o,t){var n={sandbox:"https://sandbox-merchant.revolut.com/embed.js",prod:"https://merchant.revolut.com/embed.js",dev:"https://merchant.revolut.codes/embed.js"},r={sandbox:"https://sandbox-merchant.revolut.com/upsell/embed.js",prod:"https://merchant.revolut.com/upsell/embed.js",dev:"https://merchant.revolut.codes/upsell/embed.js"},l=function(e){var n=function(e){var t=o.createElement("script");return t.id="revolut-checkout",t.src=e,t.async=!0,o.head.appendChild(t),t}(e);return new Promise((function(e,r){n.onload=function(){return e()},n.onerror=function(){o.head.removeChild(n),r(new Error(t+" failed to load"))}}))},u=function(){if(window.RevolutCheckout===i||!window.RevolutCheckout)throw new Error(t+" failed to load")},c={},d={},i=function o(r,d){return c[d=d||"prod"]?Promise.resolve(c[d](r)):l(n[d]).then((function(){return u(),c[d]=window.RevolutCheckout,e[t]=o,c[d](r)}))};i.payments=function(o){var r=o.mode||"prod",d={locale:o.locale||"auto",publicToken:o.publicToken||null};return c[r]?Promise.resolve(c[r].payments(d)):l(n[r]).then((function(){return u(),c[r]=window.RevolutCheckout,e[t]=i,c[r].payments(d)}))},i.upsell=function(e){var o=e.mode||"prod",n={locale:e.locale||"auto",publicToken:e.publicToken||null};return d[o]?Promise.resolve(d[o](n)):l(r[o]).then((function(){if(!window.RevolutUpsell)throw new Error(t+" failed to load");return d[o]=window.RevolutUpsell,delete window.RevolutUpsell,d[o](n)}))},e[t]=i}(window,document,"RevolutCheckout");</script>
</head>

<body>
<input type="hidden" name="_token" id="token_item" value="{{ csrf_token() }}" />
@if ($settings->disable_banner_cookies == 'off')
    <div class="btn-block text-center showBanner padding-top-10 pb-3 display-none">
        <i class="fa fa-cookie-bite"></i> {{trans('general.cookies_text')}}
        @if ($settings->link_cookies != '')
            <a href="{{$settings->link_cookies}}" class="mr-2 text-white link-border" target="_blank">{{ trans('general.cookies_policy') }}</a>
        @endif
        <button class="btn btn-sm btn-primary" id="close-banner">{{trans('general.go_it')}}
        </button>
    </div>
@endif

{{--  <div id="mobileMenuOverlay" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"></div>--}}

@auth
    @if (! request()->is('messages/*') && ! request()->is('live/*'))
        @include('includes.menu-mobile')
    @endif
@endauth

@if ($settings->alert_adult == 'on')
    <div class="modal fade" tabindex="-1" id="alertAdult">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <p>{{ __('general.alert_content_adult') }}</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="https://google.com" class="btn e-none p-0 mr-3">{{trans('general.leave')}}</a>
                    <button type="button" class="btn btn-primary" id="btnAlertAdult">{{trans('general.i_am_age')}}</button>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="popout popout-error font-default"></div>

@if (auth()->guest() && request()->path() == '/' && $settings->home_style == 0
    || auth()->guest() && request()->path() != '/' && $settings->home_style == 0
    || auth()->guest() && request()->path() != '/' && $settings->home_style == 1
    || auth()->check()
    )
    @include('includes.navbar')
@endif

<main @if (request()->is('messages/*') || request()->is('live/*')) class="h-100" @endif role="main">
    @yield('content')



    @guest

        @if (request()->is('/')
            && $settings->home_style == 0
            || request()->is('creators')
            || request()->is('creators/*')
            || request()->is('category/*')
            || request()->is('p/*')
            || request()->is('blog')
            || request()->is('blog/post/*')
            || request()->route()->named('profile')
            )

            @include('includes.modal-login')

        @endif
    @endguest

    @auth

        @if ($settings->disable_tips == 'off')
            @include('includes.modal-tip')
        @endif

        @include('includes.modal-payperview')

        @if ($settings->live_streaming_status == 'on')
            @include('includes.modal-live-stream')
        @endif

    @endauth

    @guest
        @include('includes.modal-2fa')
    @endguest
</main>

@include('includes.javascript_general')


{{-- <script src="{{ asset('public/js/utility.js')}}?v={{$settings->version}}" type="text/javascript"></script> --}}
<script src="{{ asset('public/js/utility.js')}}?v={{$settings->version}}.1.1" type="text/javascript"></script>
@yield('javascript')

@auth
    <div id="bodyContainer"></div>
@endauth
</body>
</html>
