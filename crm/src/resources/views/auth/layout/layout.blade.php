@extends('layout.master')
@section('master')
    <div class="root-preloader position-absolute overlay-loader-wrapper">
        <div class="spinner-bounce d-flex align-items-center justify-content-center h-100">
            <span class="bounce1 mr-1"></span>
            <span class="bounce2 mr-1"></span>
            <span class="bounce3 mr-1"></span>
            <span class="bounce4"></span>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-8">
                @php
                    $banner = config()->get('settings.application.company_banner');
                    $banner = $banner ? asset($banner) : asset('/images/default_banner.png');
                @endphp
                <div class="back-image"
                     style="background-image: url({{ $banner }})"></div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 pl-0">
                @yield('contents')
            </div>
        </div>
    </div>
@endsection

<script>
    window.addEventListener('load', function() {
        document.querySelector('.root-preloader').remove();
    });
</script>
