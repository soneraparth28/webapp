@extends('layouts.app')

@section('title') {{trans('general.referrals')}} -@endsection

@section('content')

    <?php $pageTitle = "Course Completed"; ?>

    <div class="session-main-wrapper" id="course-completion-page" data-course-id="{{$update->id}}">
        @include("includes.menus.main-creator-menu")

        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">
                <div class="container container-lg-3 border border-med-light-gray border-radius-10px p-4">
                    <div class="row flex-row-around">

                        <div class="col-sm-12 col-md-8 col-lg-6 col-xl-5 mt-3">
                            <div class="flex-col-start flex-align-center">
                                <svg viewBox="0 0 550 150" id="svg_curve" class="noSelect">
                                    <path id="curve" d="M 1.2 38 C 5.2 31.9 98.9 -0.5 270.5 1.5 C 403.6 4.3 541.8 37.4 541.8 36.6" transform="translate(12,80)"/>
                                    <text>
                                        <textPath xlink:href="#curve">
                                            Congratulations
                                        </textPath>
                                    </text>
                                </svg>
                                <p class="font-20 text-center mb-0">You completed</p>
                                <p class="font-25 font-weight-bold text-uppercase text-center">{{$update->title}}</p>

                                <p class="font-16 text-center mt-4">Your certificate is now available in your account</p>
                                <div class="flex-row-around flex-align-center">
                                    <a href="{{url("/")}}" class="theme-btn-reversed pt-3 pb-3 pl-4 pr-4">Dashboard</a>
                                    <a href="{{route("view-course-diploma", $update->id)}}" class="theme-btn ml-3 pt-3 pb-3 pl-4 pr-4">Download</a>
                                    {{--                            <div class="theme-btn ml-3 pt-3 pb-3 pl-4 pr-4">Download</div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

