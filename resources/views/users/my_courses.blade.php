@extends('layouts.app')

@section('content')


    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script>


    <section class="section section-sm" id="creator-my-courses" data-user-id="{{$userId}}">
        <div class="container container-lg-3 pt-2">
            <div class="card bg-theme-card">
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->verified_id !== 'yes' || request()->is('my-courses'))
                        <div class="col-sm-12">
                            <p class="font-30 font-weight-bold">My courses</p>


                            <div class="table-responsive container-fluid overflow-x-hidden mt-3">
                                <table class="table table-hover dataTable prettyTable" id="">
                                    <thead>
                                        <tr>
                                            <th class="font-16">Title</th>
                                            <th class="font-16 hideOnMobileTableCell">Status</th>
                                            <th class="font-16 hideOnMobileTableCell">Diploma</th>
{{--                                            <th class="font-16">Link</th>--}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($courses as $course)
                                            <tr class="border-top">
                                                <td class="">
                                                    <a href="{{url("course/$course->id")}}" target="_blank">{{$course->title}}
                                                    <i class="ml-2 bi bi-arrow-right"></i></a>
                                                </td>
                                                <td class="hideOnMobileTableCell">
                                                    {{$course->completed === "yes" ? "Completed" : "Started"}}
                                                </td>
                                                <td class="hideOnMobileTableCell">
                                                    @if($course->completed === "yes")
                                                        <a href="{{route("view-course-diploma", $course->id)}}" class="">Download</a>
                                                    @else
                                                        Not available
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>




                        </div>
                        @else
                            <div class="col-sm-12">
                                <p class="font-30 font-weight-bold">Manage courses</p>


                                <div class="table-responsive container-fluid overflow-x-hidden mt-3">
                                    <table class="table table-hover dataTable prettyTable" id="">
                                        <thead>
                                        <tr>
                                            <th class="font-16">Title</th>
                                            <th class="font-16 hideOnMobileTableCell">Created</th>
                                            <th class="font-16 hideOnMobileTableCell">Completions</th>
                                            {{--                                            <th class="font-16">Link</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($courses as $course)
                                            <tr class="border-top">
                                                <td class="">
                                                    <a href="{{url("course/$course->id")}}" target="_blank">{{$course->title}}
                                                        <i class="ml-2 bi bi-arrow-right"></i></a>
                                                </td>
                                                <td class="hideOnMobileTableCell">{{date("d M, Y", strtotime($course->date))}}</td>
                                                <td class="hideOnMobileTableCell">{{$course->completions->count()}}</td>
                                                {{--                                                <td>Copy link</td>--}}
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>




                            </div>
                        @endif
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
