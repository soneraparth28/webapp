

<div class="flex-col-start flex-align-start w-100" id="course-content-menu" style=" unicode-bidi:bidi-override;
         direction: ltr;
      overflow: scroll;
      overflow-x: hidden!important;">
    <div class="flex-row-start flex-align-center">
        <span class="font-14 color-gray">Courses</span>
        <i class="font-12 ml-3 bi bi-chevron-right"></i>
        <span class="font-14 ml-3">{{$edit ? "Edit" : "Create"}}</span>
    </div>

    @if(!$edit)
        <div class="item-container-box p-3 w-100 color-gray mt-3" data-module-id="1" >
            <div class="flex-row-between flex-align-center flex-nowrap mb-2 course-btn-element course-menu-module" data-content-type="module" data-content-id="1">
                <p class="font-20 font-weight-bold noSelect mb-0 hover-underline cursor-pointer" data-mimic-id="module_1">Module 1</p>
            </div>

            <div class="flex-col-start flex-align-start lesson-container mt-2">
                <div class="flex-row-between flex-align-center flex-nowrap course-menu-submodule course-btn-element" data-content-type="lesson"  data-content-id="1-1">
                    <div class="flex-row-start flex-align-center">
                        <i class="fa fa-book-open font-18"></i>
                        <p class="font-18 mb-0 ml-2 noSelect wrap" data-mimic-id="lesson_1-1">Lesson 1</p>
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
                <div class="flex-row-between flex-align-center flex-nowrap mb-2 course-btn-element course-menu-module px-0" data-content-type="module" data-content-id="{{$moduleId}}">
                    <p class="font-20 font-weight-bold noSelect mb-0 hover-underline cursor-pointer" data-mimic-id="module_{{$moduleId}}">{{$module["module_title"]}}</p>
                    @if((string)$moduleId !== "1")
                        <i class="font-20 bi bi-x-circle cursor-pointer hover-cta-color ml-2 remove-course-item"></i>
                    @endif
                </div>

                <div class="flex-col-start flex-align-start lesson-container mt-2 sortable">
                    @foreach($module["lessons"] as $lesson)
                        <div class="flex-row-between flex-align-center flex-nowrap course-menu-submodule course-btn-element ui-state-default" data-content-type="lesson"  data-content-id="{{$lesson->current_id}}" data-lesson-id="{{$lesson['id']}}">
                            <div class="flex-row-start flex-align-center">
                                <i class="fa fa-book-open font-18"></i>
                                <p class="font-18 mb-0 ml-2 noSelect wrap" data-mimic-id="lesson_{{$lesson->current_id}}">{{$lesson->lesson_title}}</p>
                            </div>
                            @if((string)$lesson->current_id !== "1-1")
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



    <div class="item-container-box p-3 w-100 color-gray mt-4">
        <p class="font-20 font-weight-bold noSelect">Modules</p>
        <button class="mt-2 dark-btn pt-1 pb-1 pl-3 pr-3 flex-row-start flex-align-center course-module-add-more" data-add-item="module">
            <i class="bi bi-plus-circle font-18"></i>
            <span class="ml-2 mb-0">Add module</span>
        </button>
    </div>


</div>



