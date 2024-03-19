@extends('layouts.app')

@section('title') My courses @endsection

@section('content')

    <?php $pageTitle = "My courses"; ?>

    <style>
        .action-icons > div a{
            background-color: #f3f3f3;
            padding: 10px 14px;
            border-radius: 5px;
        }
        
        .action-icons > div a img{
            width: 20px;
        }
        
        .custom-url-field{
            background-color: #efefef;
            border: 0px !important;
            padding-right: 95px !important;
        }
        
        .custom-url-copy-button{
            position: absolute;
            right: 9px;
            top: 7px;
            background-color: white;
            height: 30px;
            border-radius: 5px;
            box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
            -webkit-box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
            -moz-box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
        }
        
        .no-courses{
            width: 100%;
            margin-top: 36vh;
        }
        
    </style>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row">

                    @if(auth()->user()->verified_id == 'yes')
                        @if(!empty($courses))
                            @foreach($courses as $course)
                                <div class="col-12 mt-3">
                                    <div class="card border-radius-5px">
                                        <div class="card-body">
                                            <div class="flex-row-between flex-align-center flex-wrap">
                                                <div class="flex-row-start flex-align-center mr-5">
                                                    <img src="{{Helper::getFile(config('path.images').$course["update"]->image)}}" class="h-100px mr-3 hideOnMobileBlock" />
                                                    <div class="flex-col-start">
                                                        <p class="mb-0 font-18 font-weight-bold">{{$course["update"]->title}}</p>
                                                        <p class="mb-0 mt-1 font-14 color-light-gray">Created {{date("d M Y", strtotime($course["update"]->date))}}</p>
                                                    </div>
                                                </div>
                                                <div class="flex-row-start flex-align-center mr-5">
                                                    <div class="flex-row-start mr-3 p-relative" style="position: relative">
                                                        <input type="text" class="custom-url-field" id="course-invitation-link-{{$course['update']->id}}" value="{{url('course-invitation-signup'.'/'.$course['update']->id)}}">
                                                        <button type="button" class="custom-url-copy-button" onclick="copyCourseInvitationLink({{$course['update']->id}})">Copy Link</button>
                                                    </div>
                                                </div>
                                                <!--<div class="flex-row-start flex-align-center mr-5">-->
                                                <!--    <div class="flex-col-start mr-3">-->
                                                <!--        <p class="mb-0 font-16 font-weight-bold">Purchases</p>-->
                                                <!--        <p class="mb-0 mt-1 font-14">{{$course["courseStatistics"]->purchases}}</p>-->
                                                <!--    </div>-->
                                                <!--    <div class="flex-col-start">-->
                                                <!--        <p class="mb-0 font-16 font-weight-bold">Completions</p>-->
                                                <!--        <p class="mb-0 mt-1 font-14">{{$course["courseStatistics"]->completions}}</p>-->
                                                <!--    </div>-->
                                                <!--</div>-->
                                                <div class="flex-row-end flex-align-center action-icons">
                                                    <!--<div class="flex-col-start">-->
                                                    <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                    <!--        <i class="font-16 fa fa-pencil-ruler mr-1"></i>-->
                                                    <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/edit")}}">Edit</a>-->
                                                    <!--    </div>-->
                                                    <!--    <p class="m-0">&nbsp;</p>-->
                                                    <!--</div>-->
                                                    <!--<div class="flex-col-start">-->
                                                    <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                    <!--        <i class="font-16 fa fa-eye mr-1"></i>-->
                                                    <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id)}}">View</a>-->
                                                    <!--    </div>-->
                                                    <!--    <p class="m-0">&nbsp;</p>-->
                                                    <!--</div>-->

                                                    <!--<div class="flex-col-start">-->
                                                    <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                    <!--        <i class="font-16 fa fa-mail-bulk mr-1"></i>-->
                                                    <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/email-flow")}}">Email flow</a>-->
                                                    <!--    </div>-->
                                                    <!--    <p class="m-0">&nbsp;</p>-->
                                                    <!--</div>-->
                                                    
                                                    <div class="flex-col-start">
                                                        <div class="flex-row-start flex-align-center mr-3">
                                                            <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id)}}">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="flex-col-start">
                                                        <div class="flex-row-start flex-align-center mr-3">
                                                            <a class="mb-0 font-16" href="">
                                                                <i class="fa fa-line-chart"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="flex-col-start">
                                                        <div class="flex-row-start flex-align-center mr-3">
                                                            <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/email-flow")}}">
                                                                <i class="fa fa-envelope
"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="flex-col-start">
                                                        <div class="flex-row-start flex-align-center mr-3">
                                                            <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/edit")}}">
                                                                <i class="fa fa-edit"></i>
                                                              
                                                            </a>
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                        @else
                            <div class="no-courses">
                                <div class="col-12 text-center">
                                    <h5 class="text-muted">You have no courses created</h5>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <a href="{{url('create/course')}}">
                                        <button class="dark-btn">Create Course</button>
                                    </a>
                                </div>
                            </div>

                        @endif
                    @else
                         @if(!empty($courses))
                            @foreach($courses as $course)
                                <div class="col-12 mt-3">
                                <div class="card border-radius-5px">
                                    <div class="card-body">
                                        <div class="flex-row-between flex-align-center flex-wrap">
                                            <div class="flex-row-start flex-align-center mr-5">
                                                <img src="{{Helper::getFile(config('path.images').$course["update"]->image)}}" class="h-100px mr-3 hideOnMobileBlock" />
                                                <div class="flex-col-start">
                                                    <p class="mb-0 font-18 font-weight-bold">{{$course["update"]->title}}</p>
                                                    <p class="mb-0 mt-1 font-14 color-light-gray">Purchased {{date("d M Y", strtotime($course["update"]->date))}}</p>
                                                </div>
                                            </div>
                                            <div class="flex-row-start flex-align-center mr-5">
                                                <div class="flex-row-start mr-3 p-relative" style="position: relative">
                                                    <input type="text" class="custom-url-field" id="course-invitation-link-{{$course['update']->id}}" value="{{url('course-invitation-signup'.'/'.$course['update']->id)}}">
                                                    <button type="button" class="custom-url-copy-button" onclick="copyCourseInvitationLink({{$course['update']->id}})">Copy Link</button>
                                                </div>
                                            </div>
                                            <!--<div class="flex-row-start flex-align-center mr-5">-->
                                            <!--    <div class="flex-col-start mr-3">-->
                                            <!--        <p class="mb-0 font-16 font-weight-bold">Purchases</p>-->
                                            <!--        <p class="mb-0 mt-1 font-14">{{$course["courseStatistics"]->purchases}}</p>-->
                                            <!--    </div>-->
                                            <!--    <div class="flex-col-start">-->
                                            <!--        <p class="mb-0 font-16 font-weight-bold">Completions</p>-->
                                            <!--        <p class="mb-0 mt-1 font-14">{{$course["courseStatistics"]->completions}}</p>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            <div class="flex-row-end flex-align-center action-icons">
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <i class="font-16 fa fa-pencil-ruler mr-1"></i>-->
                                                <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/edit")}}">Edit</a>-->
                                                <!--    </div>-->
                                                <!--    <p class="m-0">&nbsp;</p>-->
                                                <!--</div>-->
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <i class="font-16 fa fa-eye mr-1"></i>-->
                                                <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id)}}">View</a>-->
                                                <!--    </div>-->
                                                <!--    <p class="m-0">&nbsp;</p>-->
                                                <!--</div>-->
    
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <i class="font-16 fa fa-mail-bulk mr-1"></i>-->
                                                <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/email-flow")}}">Email flow</a>-->
                                                <!--    </div>-->
                                                <!--    <p class="m-0">&nbsp;</p>-->
                                                <!--</div>-->
                                                
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center mr-3">
                                                        <a class="mb-0 font-16" href="{{url("course/219")}}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <a class="mb-0 font-16" href="">-->
                                                <!--            <img src="{{url('public/images/icons/1231684.png')}}">-->
                                                <!--        </a>-->
                                                <!--    </div>-->
                                                <!--</div>-->
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/email-flow")}}">-->
                                                <!--            <img src="{{url('public/images/icons/635533.png')}}">-->
                                                <!--        </a>-->
                                                <!--    </div>-->
                                                <!--</div>-->
                                                <!--<div class="flex-col-start">-->
                                                <!--    <div class="flex-row-start flex-align-center mr-3">-->
                                                <!--        <a class="mb-0 font-16" href="{{url("course/" . $course["update"]->id . "/edit")}}">-->
                                                <!--            <img src="{{url('public/images/icons/7398464.png')}}">-->
                                                <!--        </a>-->
                                                <!--    </div>-->
                                                <!--</div>-->
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                         @else
                            <div class="no-courses">
                                <div class="col-12 text-center">
                                    <h5 class="text-muted">You dont have any active courses</h5>
                                </div>
                            </div>
                            @endif
                    @endif
                </div>

            </div>



        </div>
    </div>

@endsection
@section('javascript')
    <script>
        function copyCourseInvitationLink(id){
            var copyText = document.getElementById("course-invitation-link-"+id);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value);
        }
    </script>
@endsection