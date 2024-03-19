@extends('layouts.app')
@section('title') {{$update->title}} -@endsection

@section('content')


    <?php $pageTitle = "Edit Course"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <script>
                    var courseItemList;
                    var editCourse = {{$edit ? 1 : 0}};
                    var courseId = {{$update->id}};
                </script>
                @if($edit)
                    <script>courseItemList = [];</script>
                @else
                    <script>courseItemList = ["1", "1-1"];</script>
                @endif

                <div class="row">
                    <div class="col-12 col-xl-3 mt-2 desktopOnlyFlex" >
                        @include("includes.courses-left-sidebar")
                    </div>

                    <div class="col-12 col-xl-9 mt-2 pb-5">

                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-4">

                                <div class="">
                                    <table class="table m-0">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="pt-0 pr-0 pl-0 w-100px"
                                                onclick="contentViewChange('settings')">
                                                <div class="flex-row-around ">
                                                    <div class="flex-row-around flex-align-center square-40 bg-dark border-radius-50 cursor-pointer stageSettings square">
                                                        <i class="color-light font-20 feather icon-settings stageSettings"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th scope="col" class="pl-0 pr-0 pt-0 align-middle text-center">
                                                <div style="height: 1px; border-top-width: 2px; border-top-style: dashed" class="w-100 border-med-light-gray line stageContent"></div>
                                            </th>
                                            <th scope="col" class="pt-0 pl-0 pr-0 w-100px"
                                                onclick="contentViewChange('content')">
                                                <div class="flex-row-around w-100">
                                                    <div class="flex-row-around flex-align-center square-40 bg-med-light-gray border-radius-50 cursor-pointer stageContent square">
                                                        <i class="font-20 feather icon-copy color-light-gray stageContent"></i>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <td class="font-weight-bold font-16 text-center pt-0 pb-1 pl-0 w-100px pr-0 cursor-pointer stageSettings"
                                            onclick="contentViewChange('settings')">Settings</td>
                                        <td class=" pt-0 pl-0 pr-0"></td>
                                        <td class="font-weight-bold font-16 text-center color-light-gray pt-0 pb-1 pl-0 pr-0 w-100px cursor-pointer stageContent"
                                            onclick="contentViewChange('content')">Content</td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="flex-row-start flex-align-center flex-nowrap overflow-auto hideScrollBar my-3 hideOnDesktopFlex" id="mobileModuleNav">
                                    @if(!$edit)
                                    <p class="mb-0 font-16 px-2 pb-3 border-bottom openModuleMobileView cursor-pointer hover-opacity-8 font-weight-bold
                                        course-btn-active border-dark " data-open-id="1">Module 1</p>
                                    @else
                                        @foreach($modules as $moduleId => $module)
                                            <p class="mb-0 font-16 px-2 pb-3 border-bottom openModuleMobileView cursor-pointer hover-opacity-8 font-weight-bold
                                                @if((string)$moduleId === "1") course-btn-active border-dark @else border-med-light-gray @endif " data-open-id="{{$moduleId}}">{{$module["module_title"]}}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 w-100 hideOnDesktopFlex d-none" id="mobile-lesson-menu">

                                @if(!$edit)
                                    <div class="item-container-box p-3 w-100 color-gray mt-3" data-module-id="1" >
                                        <div class="flex-row-between flex-align-center flex-nowrap course-btn-element course-menu-module p-0 mb-2" data-content-type="module" data-content-id="1">
                                            <p class="font-20 font-weight-bold noSelect mb-0 hover-underline cursor-pointer" data-mimic-id="module_1">Module 1</p>
                                        </div>

                                        <div class="flex-col-start flex-align-start lesson-container">
                                            <div class="flex-row-between flex-align-center flex-wrap course-menu-submodule course-btn-element" data-content-type="lesson"  data-content-id="1-1">
                                                <div class="flex-row-start flex-align-center">
                                                    <i class="fa fa-book-open font-18"></i>
                                                    <p class="font-18 mb-0 ml-2 noSelect" data-mimic-id="lesson_1-1">Lesson 1</p>
                                                </div>
                                            </div>
                                        </div>

                                        <button class="mt-2 dark-btn pt-1 pb-1 pl-3 pr-3 flex-row-start flex-align-center course-module-add-more" data-add-item="lesson">
                                            <i class="bi bi-plus-circle font-18"></i>
                                            <span class="ml-2 mb-0">Add lesson</span>
                                        </button>
                                    </div>
                                @else
                                    @foreach($modules as $moduleId => $module)
                                        <div class="item-container-box p-3 w-100 color-gray mt-3" data-module-id="{{$moduleId}}" >
                                            <div class="flex-row-between flex-align-center flex-nowrap course-btn-element course-menu-module p-0 mb-2" data-content-type="module" data-content-id="1">
                                                <p class="font-20 font-weight-bold noSelect mb-0 hover-underline cursor-pointer" data-mimic-id="module_{{$moduleId}}">{{$module["module_title"]}}</p>
                                                @if((string)$moduleId !== "1")
                                                    <i class="font-20 bi bi-x-circle cursor-pointer hover-cta-color ml-2 remove-course-item"></i>
                                                @endif
                                            </div>
                                            <div class="flex-col-start flex-align-start lesson-container">
                                                @foreach($module["lessons"] as $lesson)
                                                    <div class="flex-row-between flex-align-center flex-wrap course-menu-submodule course-btn-element" data-content-type="lesson"  data-content-id="{{$lesson->current_id}}">
                                                        <div class="flex-row-start flex-align-center">
                                                            <i class="fa fa-book-open font-18"></i>
                                                            <p class="font-18 mb-0 ml-2 noSelect" data-mimic-id="lesson_{{$lesson->current_id}}">{{$lesson->lesson_title}}</p>
                                                        </div>
                                                        @if($lesson->current_id !== "1-1")
                                                            <i class="ml-2 font-18 bi bi-x-circle cursor-pointer hover-cta-color remove-course-item"></i>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            <button class="mt-2 dark-btn pt-1 pb-1 pl-3 pr-3 flex-row-start flex-align-center course-module-add-more" data-add-item="lesson">
                                                <i class="bi bi-plus-circle font-18"></i>
                                                <span class="ml-2 mb-0">Add lesson</span>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-12 col-md-6 mt-2">
                                <p class="font-22 font-weight-bold mb-0" id="currentStageText">Course Settings</p>
                            </div>

                            <div class="col-12 col-md-6 mt-2 desktopOnlyFlex">
                                <form id="publish_course" action="{{url("create/course")}}" method="post" class="w-100">
                                    @csrf
                                    <button class="dark-btn w-100 course-next-btn" style="border-radius: 10px !important;" data-current-view="" data-error="{{trans('general.error')}}" data-msg-error="{{trans('general.error_internet_disconnected')}}">Next</button>
                                </form>
                            </div>


                            <div class="col-12">
                                <div class="flex-col-start">
                                    <div class="progress-wrapper px-3 px-lg-0 display-none mb-3" id="progress">
                                        <div class="progress-info">
                                            <div class="progress-percentage">
                                                <span class="percent">0%</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <!-- Alert -->
                                    <div class="bg-dark color-light border-radius-5px display-none mb-0 my-3 py-2 px-3 display-none" id="errorUdpate">
                                        <ul class="list-unstyled m-0" id="showErrorsUdpate"></ul>
                                    </div><!-- Alert -->
                                </div>
                            </div>
                        </div>



                        <div class="dataParentContainer mt-3" id="courseSettings">
                            <div class="row">
                                <div class="col-12 second mt-2">
                                    <div class="flex-col-start">
                                        <input class="w-100 font-18 bg-med-light-gray border-0 color-gray p-4" style="border-radius: 10px !important;" autocomplete="off" name="course_title"
                                               placeholder="Your Course Title Goes Here" type="text"  value="{{$edit ? $update->title : ''}}"/>

                                        <p class="mt-4 mb-0 font-weight-bold">Course description</p>
                                        <textarea name="courseDescription" id="courseDescription" data-post-length="{{$settings->update_length}}" rows="4" cols="40"
                                                  placeholder="{{trans('general.write_something')}}"
                                                  class="form-control textareaAutoSize mt-1 emojiArea border-radius-10px">{{$edit ? $update->description : ''}}</textarea>



                                        <p class="mt-4 mb-0 font-weight-bold">Who has access to this course</p>
                                        <select name="course_access" class="mt-1 form-control border-radius-10px">
                                            <option value="subscribers" {{$edit && $update->access === 'subscribers' ? 'selected' : ''}}>Free for paying subscribers</option>
                                            <option value="paid" {{$edit && $update->access === 'paid' ? 'selected' : ''}}>Available to everyone (Paid)</option>
                                            <option value="public" {{$edit && $update->access === 'public' ? 'selected' : ''}}>Free for everyone</option>
                                        </select>

                                        <div class="w-100 price-element mt-4 {{$edit && $update->access === 'paid' ? '' : 'd-none'}}">
                                            <p class="mb-0 font-weight-bold">Course price</p>
                                            <input class="form-control mt-1 isNumber" style="border-radius: 10px !important;" autocomplete="off" name="course_price"
                                                   placeholder="{{trans('general.price')}}"  type="text"  value="{{$edit ? $update->price : ''}}"/>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6 mt-4">
                                                <div class="w-100 p-4 border border-med-light-gray border-radius-5px flex-row-around flex-align-center">
                                                    <div class="flex-row-start flex-align-center">
                                                        <p class="mb-0 font-weight-bold">Users can download media</p>
                                                        <label class="form-switch mb-0 ml-3 ">
                                                            <input type="checkbox" name="media_downloadable" {{$edit && $update->media_downloadable === "yes" ? 'checked': ''}}>
                                                            <i></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mt-4">
                                                <div class="w-100 p-4 border border-med-light-gray border-radius-5px flex-row-around flex-align-center">
                                                    <div class="flex-row-start flex-align-center">
                                                        <div class="flex-row-start flex-align-center">
                                                            <p class="mb-0 font-weight-bold">Strict module flow</p>
                                                            <i class="bi bi-info-circle-fill ml-2 " data-toggle="tooltip" data-placement="top"
                                                               title="{{trans("general.what_is_strict_flow")}}"></i>
                                                        </div>
                                                        <label class="form-switch mb-0 ml-3">
                                                            <input type="checkbox" name="strict_flow" {{$edit && $update->strict_flow === "yes" ? 'checked': ''}}>
                                                            <i></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="file-upload-parent w-100 mt-4" data-thumb-type="photo" data-file-element-id="course-thumb">
                                            @if($edit)
                                                <input type="hidden" name="fileuploader-list-photo" class="preload-file" value='[{"file":"{{$update->image}}"}]'>
                                            @endif
                                            <div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">
                                                <img src="{{$edit ? Helper::getFile(config('path.images').$update->image) : ''}}" class="{{$edit ? '' : 'd-none'}} w-100 noSelect" id="course-thumb-image"/>
                                                <div class="flex-col-start flex-align-center {{$edit ? 'd-none' : ''}}">
                                                    <p class="mb-0 color-light-gray">Add image</p>
                                                    <i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>
                                                </div>
                                            </div>

                                            <input type="file" name="photo" id="courseCoverPhoto" accept="image/*" class="filepond w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div class="dataParentContainer mt-3 d-none" id="courseContent">

                            @if($edit)
                                @foreach($modules as $moduleId => $module)
                                    <script>
                                        courseItemList.push('{{$moduleId}}')
                                    </script>
                                    {{--                            Module--}}
                                    <div class="flex-col-start content-parent dataParentContainer" data-content-type="module" data-content-id="{{$moduleId}}">
                                        <p class="mb-0 font-weight-bold">Module title</p>
                                        <input class="form-control mt-1 mimic-text-input mb-3" style="border-radius: 10px !important;" value="{{$module["module_title"]}}" autocomplete="off" name="module_title"
                                               placeholder="{{trans('general.title')}}" type="text" data-mimic-search="module_{{$moduleId}}"/>
                                        <p class="mb-0 font-weight-bold">Availability after weeks</p>
                                        <select name="duration" id="duration" class="form-control mt-1 mimic-text-input">
                                            <?php for($i=0;$i<=12;$i++) {?>
                                                <option value="{{$i}}"  @if($module["duration"] == $i) Selected @endif>{{$i}}</option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    @foreach($module["lessons"] as $lesson)
                                        <script>
                                            courseItemList.push('{{$lesson->current_id}}')
                                        </script>
                                        {{--                            Lesson--}}
                                        <div class="flex-col-start content-parent dataParentContainer d-none mt-4" data-content-type="lesson" data-content-id="{{$lesson->current_id}}">
                                            <div class="file-upload-parent w-100" data-thumb-type="video" data-file-element-id="{{$lesson->current_id}}">
                                                @if(!$lesson->is_embed && $lesson->media != "")
                                                <?php
                                                $lesson_media = explode("updates/videos/", $lesson->media);
                                                if(isset($lesson_media[1]))
                                                    $file = $lesson_media[1];
                                                else
                                                    $file = '';
                                                ?>
                                                    <input type="hidden" name="fileuploader-list-photo" data-filetype="video" class="preload-file" value='[{"file":"{{$file}}"}]'>
                                                @endif
                                                <div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">
                                                    @if($lesson->media != "")
                                                    <video class="w-100 noSelect {{$lesson->is_embed ? 'd-none' : ''}}" controls preload="none">
                                                        <source src="{{$lesson->is_embed ? '' : $lesson->media}}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    @endif
                                                    <div class="flex-col-start flex-align-center {{$lesson->is_embed || $lesson->media == '' ? '' : 'd-none'}}">
                                                        <p class="mb-0 color-light-gray">Add video</p>
                                                        <i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>
                                                    </div>
                                                </div>

                                                <input type="file" name="photo" id="courseCoverPhoto" accept="video/*" class="filepond w-100">
                                            </div>

                                            <p class="mt-4 mb-0 font-weight-bold">Or lesson video link</p>
                                            <input class="form-control mt-1 mimic-text-input" style="border-radius: 10px !important;" autocomplete="off" name="lesson_video_url"
                                                   placeholder="https://www.youtube.com/watch?v=BvWt" type="url" value="{{$lesson->is_embed ? $lesson->media : ''}}"/>

                                            <input class="mt-4 w-100 font-18 bg-med-light-gray border-0 color-gray p-4 mimic-text-input" style="border-radius: 10px !important;"  autocomplete="off" name="lesson_title"
                                                   placeholder="Your Lesson Title Goes Here" type="text" data-mimic-search="lesson_{{$lesson->current_id}}" value="{{$lesson->lesson_title}}"/>

                                            <p class="mt-4 mb-0 font-weight-bold">Lesson context</p>
                                            <textarea name="lesson_context" style="border-radius: 10px !important;" data-post-length="{{$settings->update_length}}" rows="4" cols="40"
                                                      placeholder="{{trans('general.write_something')}}"
                                                      class="form-control textareaAutoSize mt-1 emojiArea">{{$lesson->lesson_description}}</textarea>
                                            <!-- Lesson file -->
                                            <p class="mt-4 mb-0 font-weight-bold">Lesson File</p>
                                            <div class="file-upload-parent w-100 mt-4" data-thumb-type="file"  data-file-element-id="{{$lesson->current_id}}">
                                                @if($edit)
                                                    <input type="hidden" name="fileuploader-list-file" data-filetype="file" class="preload-file" value='[{"file":"{{$lesson->lesson_file}}"}]'>
                                                @endif
                                                <span class="content-holder">{{$lesson->lesson_file}}</span>
                                                <div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">
                                                    <div class="flex-col-start flex-align-center {{$edit && $lesson->lesson_file!='' ? 'd-none' : ''}}">
                                                        <p class="mb-0 color-light-gray">Add File</p>
                                                        <i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>
                                                    </div>
                                                </div>

                                                <input type="file" name="file" id="courseCoverPhoto" class="filepond w-100">
                                            </div>
                                            <!-- Lesson file -->
                                        </div>
                                    @endforeach
                                @endforeach








                            @else
                                {{--                            Module--}}
                                <div class="flex-col-start content-parent dataParentContainer" data-content-type="module" data-content-id="1">
                                    <p class="mb-0 font-weight-bold">Module title</p>
                                    <input class="form-control mt-1 mimic-text-input" style="border-radius: 10px !important;" value="Module 1" autocomplete="off" name="module_title"
                                           placeholder="{{trans('general.title')}}" type="text" data-mimic-search="module_1"/>
                                    <p class="mb-0 font-weight-bold">Availability after weeks</p>
                                    <select name="duration" id="duration" class="form-control mt-1 mimic-text-input">
                                        <?php for($i=0;$i<=12;$i++) {?>
                                            <option value="{{$i}}">{{$i}}</option>
                                        <?php } ?>
                                    </select>
                                </div>


                                {{--                            Lesson--}}
                                <div class="flex-col-start content-parent dataParentContainer d-none mt-4" data-content-type="lesson" data-content-id="1-1">
                                    <div class="file-upload-parent w-100" data-thumb-type="video" data-file-element-id="1-1">
                                        <div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">
                                            <video class="d-none w-100 noSelect" controls>
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="flex-col-start flex-align-center">
                                                <p class="mb-0 color-light-gray">Add video</p>
                                                <i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>
                                            </div>
                                        </div>
                                        <input type="file" name="photo" id="courseCoverPhoto" accept="video/*" class="filepond w-100">
                                    </div>

                                    <p class="mt-4 mb-0 font-weight-bold">Or lesson video link</p>
                                    <input class="form-control mt-1 mimic-text-input" style="border-radius: 10px !important;" value="" autocomplete="off" name="lesson_video_url"
                                           placeholder="https://www.youtube.com/watch?v=BvWt" type="url" />

                                    <input class="mt-4 w-100 font-18 bg-med-light-gray border-0 color-gray p-4 mimic-text-input" style="border-radius: 10px !important;" value="" autocomplete="off" name="lesson_title"
                                           placeholder="Your Lesson Title Goes Here" type="text" data-mimic-search="lesson_1-1"/>

                                    <p class="mt-4 mb-0 font-weight-bold">Lesson context</p>
                                    <textarea name="lesson_context" style="border-radius: 10px !important;" data-post-length="{{$settings->update_length}}" rows="4" cols="40"
                                              placeholder="{{trans('general.write_something')}}"
                                              class="form-control textareaAutoSize mt-1 emojiArea"></textarea>
                                    
                                    <!-- Lesson file -->
                                    <p class="mt-4 mb-0 font-weight-bold">Lesson File</p>
                                    <div class="file-upload-parent w-100 mt-4" data-thumb-type="file"  data-file-element-id="1-1">
                                        <span class="content-holder"></span>
                                        <div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">
                                            <div class="flex-col-start flex-align-center">
                                                <p class="mb-0 color-light-gray">Add File</p>
                                                <i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>
                                            </div>
                                        </div>

                                        <input type="file" name="file" id="courseCoverPhoto" class="filepond w-100">
                                    </div>
                                    <!-- Lesson file -->
                                </div>
                            @endif
                        </div>

                        <div class="row mt-3 hideOnDesktopFlex">
                            <div class="col-5 col-lg-3 w-100 mt-2">
                                <button class="dark-btn w-100 h-50px flex-row-around flex-align-center course-module-add-more border-radius-10px" data-add-item="module">
                                    <div>
                                        <i class="bi bi-plus-circle font-18"></i>
                                        <span class="ml-2 mb-0">Add module</span>
                                    </div>
                                </button>
                            </div>
                            <div class="col-7 col-lg-9 w-100 mt-2">
                                <form id="publish_course" action="{{url("create/course")}}" method="post" class="w-100">
                                    @csrf
                                    <button class="dark-btn w-100 border-radius-10px course-next-btn" data-current-view="" data-error="{{trans('general.error')}}" data-msg-error="{{trans('general.error_internet_disconnected')}}">Next</button>
                                </form>
                            </div>
                        </div>
                    </div>




                </div>
            </div>



        </div>

    </div>

    </div>
    
    
@endsection
 
@section('javascript')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#token_item').val()
            }
        });
    
        $(document).ready(function(){
            $(".sortable").sortable({
                change: function( event, ui ) {
                    $this = $(this);
                    let lessons = [];
                    setTimeout(function(){
                        
                        let $module = $this.closest('.item-container-box');
                        let module_id = $module.data('module-id');
                        $module.find('.sortable .course-menu-submodule').each(function(){
                            $lesson = $(this);
                            lessons.push($lesson.attr('data-lesson-id'));
                        });
                        console.log(lessons);
                        
                        $.ajax({
                            url: '{{url("change-lessons-order-numbers")}}',
                            type: 'post',
                            data: {ids: lessons},
                            success: function(result){
                                
                            }
                        });
                         
                    }, 2000);
                    
                }
            });
        });
    </script>

@endsection
