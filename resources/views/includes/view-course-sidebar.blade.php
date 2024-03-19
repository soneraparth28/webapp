

<div class="flex-col-start flex-align-start w-100" id="course-content-menu">
@foreach($modules as $moduleId => $module)

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
    @if($weeks >= intval($module["duration"]) || auth()->user()->id == $module["user_id"])
        <div class="item-container-box p-3 w-100 color-gray {{$moduleId === 1 ? "": "mt-4"}}" data-module-id="{{$moduleId}}" >

            <div class="flex-row-between flex-align-center flex-nowrap course-heading-container">
                <p class="font-20 cursor-pointer mb-0 font-weight-bold {{$moduleId === 1 ? "": "collapsed"}}"
                data-toggle="collapse" data-target="#module_{{$moduleId}}_toggler" aria-controls="navbarCollapse" aria-expanded="{{$moduleId === 1 ? true: false}}"
                data-toggle-id="module_{{$moduleId}}_toggler">
                    {{$module["module_title"]}}
                </p>
                <div class="flex-row-end flex-align-start flex-nowrap @if($module["is_completed"]) text-primary-cta @endif mnw-75px completion-tracker-parent">
                    <div class="ml-1 mb-0">
                        <span id="module_{{$moduleId}}_completion_tracker" data-total-count="{{count($module["lessons"])}}">{{$module["lessons_completed"]}}</span>
                        <span id=""> / {{count($module["lessons"])}}</span>
                    </div>
                    <i class="bi bi-chevron-down ml-2"></i>
                </div>
            </div>

            <div class="navbar-collapse collapse mt-2 dataParentContainer {{$moduleId === 1 ? "show": ""}}" id="module_{{$moduleId}}_toggler">
            <!--<div class="navbar-collapse collapse mt-2 dataParentContainer {{$moduleId === 1 ? "show": ""}}">-->
                <div class="flex-col-start flex-align-start">

                    @foreach($module["lessons"] as $lesson)
                        <div class="flex-col-start mt-4 course-post-link-bar course-btn-element cursor-pointer @if($lesson->previous === null) course-btn-active  @endif  @if($lesson->is_completed) lessonIsComplete @endif"  data-content-type="lesson"  data-content-id="{{$lesson->current_id}}" data-video-id="{{$lesson->media_id}}" data-is-embed="{{$lesson->is_embed ? "yes" : "no"}}">

                            <div class="flex-row-start flex-align-start flex-nowrap">
                                <i class="bi @if($lesson->is_completed) bi-check-circle-fill @else bi-circle-half  @endif font-18 completion-icon"></i>
                                <div class="flex-col-start">
                                    <p class="font-18 mb-0 ml-2 noSelect">{{$lesson->lesson_title}}</p>

                                    <div class="flex-row-start flex-align-center menu-display-lesson">
                                        {{--                            <i class="bi bi-check-circle font-14 dummy-icon " ></i>--}}
                                        <div class="flex-row-start flex-align-center">
                                            <i class="bi bi-camera-video-fill media-type-icon ml-0"></i>
                                            <p class="font-14 mb-0 ml-2">Video</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    @endif
    @endforeach
</div>
