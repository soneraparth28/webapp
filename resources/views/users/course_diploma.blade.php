@extends('layouts.app')

@section('content')


    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script>


    <section class="section section-sm" id="course-diploma-page" data-course-id="{{$update->id}}">
        <div class="container container-lg-3 pt-2">
            <div class="row">

                <div class="col-sm-12 mt-3">





                    <div class="card bg-theme-card text-word-break mb-2 mt-2 ">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="w-100 bg-theme-bg p-4" id="diploma">
                                        <div class="diploma-wrapper">
                                            <div class="flex-col-start flex-align-center" id="diploma-content-wrapper">

                                                <div class="row flex-row-around w-100">
                                                    <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                                                        <div class="flex-col-start flex-align-center position-relative">
                                                            <img src="https://mator.io/wp-content/uploads/2022/02/2.png" class="noSelect mxw-200px mxh-100px"/>
                                                            <div class="flex-col-start flex-align-center mt-2 position-relative" id="diploma-center-content">
                                                                <div class="diploma-center-image-wrapper"></div>
                                                                <div class="flex-col-start flex-align-center" id="course-text-content">
                                                                    <p class="diploma-course-title mb-0">{{$update->title}}</p>
                                                                    <p class="diploma-course-text font-25">Diploma</p>


                                                                    <p class="diploma-course-name mt-4">{{$user->name}}</p>
                                                                    <div class="diploma-horizontal-bg-line"></div>

                                                                    <p class="diploma-course-text mt-4">Awarded on {{date("M d. Y", strtotime($completionDetails->created_at))}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="diploma-course-bottom-text">
                                                    Having completed the assessment of the knowledge acquired in the course of the supervised mentoring period,
                                                    it is processed through the issue of this certificate - diploma of studies
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                    <div id="downloadCanvas" data-target-id="diploma"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>
@endsection

@section('javascript')

    @if (session('noty_error'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ trans('general.already_sent_report') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('noty_success'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.thanks') }}",
                text: "{{ trans('general.reported_success') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('success_verify'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.welcome') }}",
                text: "{{ trans('users.account_validated') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('error_verify'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ trans('users.code_not_valid') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

@endsection
