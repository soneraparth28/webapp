@extends('layouts.app')

@section('title') Email flow @endsection

@section('content')

    <?php $pageTitle = "Email flow"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row">
                    <div class="col-12">
                        <div class="flex-row-end flex-align-center mt-2">
                            <button name="toggle_flow_form" class="light-btn px-3 py-2" style="">Create new template</button>

                        </div>
                        <form action="{{url("course/" . $update->id . "/email-flow")}}" method="post" id="email_flow_form" style="display: none">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" />
                            <div class="bg-dark p-3 color-light my-2 d-none" id="error-field"></div>
                            <input type="hidden" name="flow_id" value="0" />
                            <input type="hidden" name="course_id" value="{{$update->id}}" />
                            <div class="flex-row-start flex-align-center flex-wrap mb-2">
                                <p class="mb-0 mt-2 mr-3 font-16">Template name</p>
                                <input type="text" name="template_name" placeholder="Name of the email template" class="form-control mt-2" />
                            </div>
                            @include("includes.rich-text-editor")
                            <div class="flex-row-end flex-align-center mt-2">
                                <button name="toggle_flow_form" class="light-btn px-3 py-2 mr-3" style="">Close</button>
                                <button name="save_email_flow" class="dark-btn px-3 py-2" style="color: var(--light) !important;">Save</button>
                            </div>
                        </form>

                    </div>





                    @if(!$flows->isEmpty())
                        @foreach($flows as $flow)
                            <div class="col-12 mt-3 dataParentContainer">
                                <div class="card border-radius-5px">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-6 col-xl-4 mt-1">
                                                <div class="flex-col-start mr-5">
                                                    <p class="mb-0 mr-5 font-16 font-weight-bold">{{$flow->title}}</p>

                                                    <div class="flex-row-start flex-align-center flex-nowrap mt-2">
                                                        <input type="text" name="days_after_unlock" data-flow-id="{{$flow->id}}" data-course-id="{{$update->id}}" data-dest-url="{{url("course/$update->id/update-email-flow-interval")}}"
                                                               class="text-center w-50px h-30px p-1 font-16 mr-1 overflow-hidden" value="{{$flow->send_days_after_unlock}}"/>
                                                        <sup class="fa fa-question-circle font-16 mr-4" data-toggle="tooltip"
                                                           title="Days after unlocking the course from which this email will be sent. Range -1 to 50, where -1 is 'never'.
                                                                Only works for courses that are unlocked by a single purchase"></sup>

                                                        <div class="flex-row-start flex-align-center flex-nowrap cursor-pointer edit-flow-content">
                                                            <i class="fa fa-pencil-ruler mr-2"></i>
                                                            <p class="font-16 mb-0">Edit</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-xl-8 mt-1">
                                                <div class="bg-med-light-gray w-100 p-3 border-radius-10px mxh-100px overflow-hidden flow-html-content"
                                                     data-flow-id="{{$flow->id}}" data-flow-title="{{$flow->title}}">
                                                    @php require_once $_SERVER["DOCUMENT_ROOT"] . $flow->template_node @endphp
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
{{--                    <p class="font-16">Create a new email template</p>--}}
                    @endif
                </div>

            </div>



        </div>
    </div>

@endsection
