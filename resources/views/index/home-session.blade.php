@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('content')

    <?php $pageTitle = "Home"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">


                <div class="row">
                    <div class="col-12 second wrap-post">

                        @if (auth()->user()->verified_id == 'yes')
                            @include('includes.form-post')
                        @endif

                        @if($updates->total() != 0)
                            @php
                                $counterPosts = ($updates->total() - $settings->number_posts_show);
                            @endphp

                            <div class="grid-updates position-relative" id="updatesPaginator">
                                @include('includes.updates')
                            </div>
                        @else
                        
                       
                            <div class="grid-updates position-relative" id="updatesPaginator"></div>
                            <div class="my-5 text-center no-updates">
                              <span class="btn-block mb-3">
                                <i class="fa fa-photo-video ico-no-result"></i>
                              </span>
                                <h4 class="font-weight-light">{{trans('general.no_posts_posted')}}</h4>
                            </div>

                        @endif

                    </div>
                </div>






            </div>
        </div>
    </div>

@endsection
