@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('content')

    <?php $pageTitle = "View course"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")


 

        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">
                @if($accessAction === "payment")
                    @include("includes.modal-payperview")
                @elseif($accessAction === "subscribe")
                    @include("includes.modal-subscription")
                @else
                    <script>
                        var courseItemList = ["1", "1-1"];
                    </script>

                    <div class="row" id="course-page" data-update-id="{{$update->id}}">
                    <div class="col-12 col-xl-3 mt-2 desktopOnlyFlex">
                        @include("includes.view-course-sidebar")
                    </div>


                    <div class="col-12 col-xl-9 mt-2">

                        <div class="flex-row-start flex-align-center flex-nowrap overflow-auto hideScrollBar mb-3 hideOnDesktopFlex">

                            @foreach($modules as $moduleId => $module)
                                <p class="mb-0 font-16 px-2 pb-3 border-bottom openModuleMobileView cursor-pointer hover-opacity-8 font-weight-bold nowrap
                                    @if((int)$moduleId === 1) course-btn-active border-dark @else border-med-light-gray @endif" data-open-id="{{$moduleId}}">
                                    {{$module["module_title"]}}
                                </p>
                            @endforeach
                        </div>



                        <div class="row dataParentContainer">
                            <div class="col-12 second" id="courseContent">
                                <div>

                                <?php
                                    $random = [
                                        'https://fastly.picsum.photos/id/155/1166/656.jpg?hmac=_OIBV7n376qvnbLEJoPHaW5UUnPKibVINtmvqeejdPE',
                                        'https://fastly.picsum.photos/id/910/1166/656.jpg?hmac=JqQl5Za8RBFpKra4tECK60Y7fue6LN1NdjnTqJHMPx8',
                                        'https://fastly.picsum.photos/id/1020/1166/656.jpg?hmac=u08BznsbK5gMcIwYvmCP1M2FlunKb70xuoMlsr9NK4I',
                                        'https://fastly.picsum.photos/id/49/1166/656.jpg?hmac=b1JYLv2pV591ii5WDAWc-rL7bHNj5SlHproRbckeT3g'
                                    ];
                                     ?>
                                
                                    @foreach($allLessons as $lesson)
                                        <?php
                                            $user = auth()->user()->myPayments()->orderBy('id','desc')->first();
                                            if($user)
                                            {
                                                $tdate = date("Y-m-d H:i:s", strtotime($user->created_at));
                                                $fdate = date("Y-m-d H:i:s");
                                                $datetime1 = new DateTime($tdate);
                                                $datetime2 = new DateTime($fdate);
                                                $interval = $datetime1->diff($datetime2);
                                                $days = $interval->format('%a');
                                                $weeks = round($days/7);
                                            }
                                            else
                                            {
                                                $weeks = "";
                                            }
                                        ?>
                                        @if($weeks >= intval($lesson->duration) || auth()->user()->id == $lesson->user_id)
                                            <div class="flex-col-start content-parent dataParentContainer @if($lesson->previous !== null) d-none  @endif"
                                                data-content-type="lesson" data-content-id="{{$lesson->current_id}}" data-video-id="{{$lesson->media_id}}" data-module-id="{{$lesson->module_id}}">

                                            <div class="w-100 mt-1">
                                                @if(!$lesson->is_embed && $lesson->media != "")
                                                    {{--<input type="hidden" name="fileuploader-list-photo" class="preload-file" value='[{"file":"{{(explode("updates/videos/", $lesson->media)[1])}}"}]'>
                                                    <video class="w-100 noSelect course-video-track border-radius-tl-tr-20px"  controls data-video-id="{{$lesson->media_id}}" poster="{{$random[array_rand($random)]}}">--}}
                                                    <video class="w-100 noSelect course-video-track border-radius-tl-tr-20px"  controls loop muted playsinline data-video-id="{{$lesson->media_id}}">
                                                        <source src="{{$lesson->media}}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else


                                                        @if (in_array(Helper::videoUrl($lesson->media), array('youtube.com','www.youtube.com','youtu.be','www.youtu.be')))
                                                            <div class="embed-responsive embed-responsive-16by9 mb-2">
                                                                <iframe class="embed-responsive-item" height="360" src="https://www.youtube.com/embed/{{ Helper::getYoutubeId($lesson->media) }}" allowfullscreen></iframe>
                                                            </div>
                                                        @endif

                                                        @if (in_array(Helper::videoUrl($lesson->media), array('vimeo.com','player.vimeo.com')))
                                                            <div class="embed-responsive embed-responsive-16by9">
                                                                <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/{{ Helper::getVimeoId($lesson->media) }}" allowfullscreen></iframe>
                                                            </div>
                                                        @endif

                                                    @endif
                                                </div>

                                                <div class="flex-row-between flex-align-center flex-wrap">
                                                    <div class="flex-col-start mt-3" style="width:85%">
                                                        <p class="font-20 font-weight-bold mb-0">{{$update->title}}</p>
                                                        <p class="font-16 font-weight-bold mb-0 mt-1">{{$lesson->lesson_title}}</p>
                                                    </div>
                                                    @if($lesson->lesson_file != "")
                                                    @php $fileExt = pathinfo($lesson->lesson_file, PATHINFO_EXTENSION); @endphp
                                                    <a href="{{ asset('public/uploads/updates/files/'.$lesson->lesson_file) }}" download="{{strtolower( ($update->title . "_" . $lesson->lesson_title) )  . "." . $fileExt}}"
                                                    class="lightest-gray-btn square-40 cursor-pointer flex-row-around align-items-center mt-3">
                                                        <i class="feather icon-file font-25"></i>
                                                    </a>
                                                    @endif
                                                    @if(!$lesson->is_embed && $update->media_downloadable == 'yes')
                                                        @php $fileExt = pathinfo($lesson->media, PATHINFO_EXTENSION); @endphp
                                                        <a href="{{$lesson->media}}" download="{{strtolower( ($update->title . "_" . $lesson->lesson_title) )  . "." . $fileExt}}"
                                                        class="lightest-gray-btn square-40 cursor-pointer flex-row-around align-items-center mt-3">
                                                            <i class="feather icon-download font-25"></i>
                                                        </a>
                                                    @endif

                                                    
                                                    

                                                </div>


                                                <div class="p-3 border-radius-10px border border-med-light-gray color-light-gray font-14 mt-2" style="letter-spacing: 1px;">
                                                    {{$lesson->lesson_description}}
                                                </div>
                                                
                                                <div class="@if($lesson->previous !== null) flex-row-between @else flex-row-end @endif flex-align-start mt-5 buttons-box">
                                                    @if($lesson->previous !== null)
                                                        {{--<button class="light-btn font-18 mnw-150px py-2 px-3 lesson-paginator" data-toggle-id="{{$lesson->previous}}">Previous</button>--}}
    {{--                                                    <div class="next-lesson-button noSelect ml-2 mt-5 bg-white position-relative square-30 border-radius-50" data-toggle-id="{{$lesson->previous}}">--}}
    {{--                                                        <i class="feather icon-chevron-right font-30 text-dark"></i>--}}
    {{--                                                    </div>--}}
                                                    @endif
                                                    @if($lesson->next !== null)
                                                        {{--<button class="dark-btn font-18 mnw-150px py-2 px-3 lesson-paginator" data-toggle-id="{{$lesson->next}}">Next</button>--}}
    {{--                                                    <div class="next-lesson-button noSelect ml-2 mt-5 bg-white position-relative square-30 border-radius-50" data-toggle-id="{{$lesson->next}}">--}}
    {{--                                                        <i class="feather icon-chevron-right font-30 text-dark"></i>--}}
    {{--                                                    </div>--}}
                                                    @else

                                                        {{--<div id="course-completion-btn"
                                                            class="dark-btn font-18 mnw-150px py-2 px-3 @if(!$courseCompletion["course_is_completed"]) d-none @endif ">
                                                            <p class="mb-0">Complete</p>
                                                        </div>--}}

                                                    @endif
                                                </div>






                                            </div>
                                        @endif
                                    @endforeach


                                </div>




                            </div>




                        </div>

                    </div>


                </div>
                @endif
            </div>



        </div>

    </div>

    </div>

@endsection

@section('javascript')
    @if($accessAction === "payment")
        <script>
            $('#payPerViewForm').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        </script>
    @elseif($accessAction === "subscribe")
        <script>
            $('#subscriptionForm').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        </script>
    @endif
    
    <script>
    
        let last_opened_module_id = 0;
        let current_module_id = 0;
        
        function manageModulesToggle(){
            current_module_id = $('.course-btn-active').closest('.item-container-box').data('module-id');
            
            if(last_opened_module_id != current_module_id){
                
                if($('.item-container-box[data-module-id='+last_opened_module_id+']').find('.course-heading-container p').attr("aria-expanded") != 'false'){
                    $('.item-container-box[data-module-id='+last_opened_module_id+']').find('.course-heading-container p').click();
                }
                
                if($('.item-container-box[data-module-id='+current_module_id+']').find('.course-heading-container p').attr("aria-expanded") != 'true'){
                    $('.item-container-box[data-module-id='+current_module_id+']').find('.course-heading-container p').click();
                }
                
                last_opened_module_id = current_module_id;
            }
            
        }
    
        $(document).ready(function(){
            manageModulesToggle();
        });
        
        $(document).on("click", ".lesson-paginator", function (){
            
            manageModulesToggle();
            setTimeout(function(){
                generateThumbnail();
            }, 1000);
        });
         
        $(document).on("click", "#course-content-menu .course-btn-element", function(){
            
            $.each($('.course-video-track'), function(index, value) {
              $(this).get(0).pause();
            });
            
            manageModulesToggle();
           
            setTimeout(function(){
                generateThumbnail();
            }, 1000);
        });
        
        window.onload = function () {
            setTimeout(function(){
                generateThumbnail();
            }, 500);
            $("#course-content-menu .item-container-box").each(function(mi){
                $module = $(this);
                let module_id = $module.data('module-id');
                
                $module.find(".navbar-collapse .course-post-link-bar[data-content-type=lesson]").each(function(li){
                    $lesson = $(this);
                    
                    //let video_id = $lesson.data('video-id');
                    let video_id = $lesson.data('content-id');
                    
                    let html = '';
                    
                    if(mi == 0 && li == 0){
                        html = '<button class="dark-btn font-18 mnw-150px py-2 px-3 lesson-paginator">Next</button>';
                    }
                    else if(mi == ($("#course-content-menu .item-container-box").length - 1) && li == ($module.find(".navbar-collapse .course-post-link-bar[data-content-type=lesson]").length - 1)){
                        html = '<div id="course-completion-btn" class="dark-btn font-18 mnw-150px py-2 px-3"> <p class="mb-0">Complete</p> </div>';
                    }
                    else{
                        html = '<button class="light-btn font-18 mnw-150px py-2 px-3 lesson-paginator">Previous</button> <button class="dark-btn font-18 mnw-150px py-2 px-3 lesson-paginator">Next</button>';
                    }
                    
                    //$("#courseContent .content-parent[data-module-id="+module_id+"][data-video-id="+video_id+"] .buttons-box").html(html);
                    $("#courseContent .content-parent[data-module-id="+module_id+"][data-content-id="+video_id+"] .buttons-box").html(html);
                })
            });
        };
        
        function generateThumbnail(){
            
            let video_id = $('.course-btn-active').data('video-id');
              
            $('#courseContent .content-parent[data-video-id='+video_id+'] video').each(function(){
                $this = $(this);
            
                var canvas = document.createElement('canvas');
                canvas.width = 700;
                canvas.height = 394;
                var ctx = canvas.getContext('2d');
                var video = this;
            
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                $this.attr("poster", canvas.toDataURL('image/png'))
            
                
            });
            
        }
        
    </script>

@endsection
