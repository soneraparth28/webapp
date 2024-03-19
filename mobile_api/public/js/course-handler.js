
function courseContentHtml(itemType, nextId, titleId) {
    let  html = ''
    if(itemType === "module") {
        html += '<div class="flex-col-start content-parent dataParentContainer d-none" data-content-type="module" data-content-id="' + nextId + '">';
        html += '<p class="mb-0">Module title</p>';
        html += '<input class="form-control mt-1 mimic-text-input" value="Module ' + titleId + '" autocomplete="off" name="module_title" ';
            html += 'placeholder="" type="text" data-mimic-search="module_' + nextId + '"/>';
        html += '</div>';
    }
    else if(itemType === "lesson") {
        html += '<div class="flex-col-start content-parent dataParentContainer d-none" data-content-type="lesson" data-content-id="' + nextId + '">';
        html += '<div class="file-upload-parent w-100" data-thumb-type="video" data-file-element-id="' + nextId + '">';
        html += '<div class="courses-cover-thumb flex-row-around flex-align-center mt-2">';
        html += '<video class="d-none w-100 noSelect" controls>';
        html += '<source src="" type="video/mp4">';
        html += 'Your browser does not support the video tag.';
        html += '</video>';
        html += '<div class="flex-col-start flex-align-center">';
        html += '<i style="color: #90a0bc !important; font-style: normal !important;">+</i>';
        html += '</div>';
        html += '</div>';
        html += '<input type="file" name="photo" id="courseCoverPhoto" accept="video/*" class="filepond w-100">';
        html += '</div>';
        html += '<p class="mt-4 mb-0">Or lesson video link</p>';
        html += '<input class="form-control mt-1 mimic-text-input" value="" autocomplete="off" name="lesson_video_url"';
            html += 'placeholder="https://www.youtube.com/watch?v=BvWt" type="url" />';
        html += '<p class="mt-4 mb-0">Lesson title</p>';
        html += '<input class="form-control mt-1 mimic-text-input" value="Lesson ' + titleId + '" autocomplete="off" name="lesson_title"';
            html += 'placeholder="" type="text" data-mimic-search="lesson_' + nextId + '"/>';
        html += '<p class="mt-4 mb-0">Lesson context</p>';
        html += '<textarea name="lesson_context" data-post-length="1000" rows="4" cols="40" placeholder="" ';
            html += 'class="form-control textareaAutoSize mt-1 emojiArea"></textarea>';
        html += '</div>';
    }

    return $(html);
}


function courseItemHtml(itemType, nextId, titleId, childId) {
    let  html = ''
    if(itemType === "lesson") {
        html += '<div class="flex-row-between flex-align-start flex-nowrap course-menu-submodule course-btn-element" data-content-type="lesson" data-content-id="' + nextId + '">';
        html += '<p class="font-18 noSelect" data-mimic-id="lesson_' + nextId + '">Lesson ' + titleId + '</p>';
        html += '<i class="bi bi-trash font-20 text-white cursor-pointer hover-cta-color remove-course-item"></i>';
        html += '</div>';
        html += '';
    }
    else if(itemType === "module") {
        html += '<div class="flex-row-between flex-align-start flex-nowrap course-menu-module course-btn-element" data-content-type="module" data-content-id="' + nextId + '">';
        html += '<p class="font-18 font-weight-bold noSelect" data-mimic-id="module_' + nextId + '">Module ' + titleId + '</p>';
        html += '<i class="bi bi-trash font-20 text-white cursor-pointer hover-cta-color remove-course-item"></i>';
        html += '</div>';
        html += '<div class="flex-col-start flex-align-start lesson-container" data-module-id="' + nextId + '">';
        html += courseItemHtml("lesson", childId, 1);
        html += '<div class="flex-row-between flex-align-center flex-wrap course-menu-submodule course-module-add-more" data-add-item="lesson">';
        html += '<p class="font-18">Add lesson</p>';
        html += '<i class="bi bi-plus-circle font-18"></i>';
        html += '</div>';
        html += '</div>';
    }

    return html;
}

function getRandomUnqId() {
    let id;
    while (true) {
        id = parseInt((Math.random()) * 10000000);
        if(!$(document).find("[data-content-id=" + id + "]").length && !$(document).find("[data-module-id=" + id + "]").length) return id;
    }
}


function addCourseItems(btn) {
    let itemType = btn.attr("data-add-item"), nextItemId = 0, titleId = 0, newCourseContentModule = "", newCourseContentLesson = "";
    let menuParent = btn.parents("#course-content-menu").first();
    let maxElements = {
        lesson: 10,
        module: 10
    }

    let courseContentContainer = $(document).find("#courseContent"), childId;
    if(!courseContentContainer.length) return;
    courseContentContainer = courseContentContainer.first();


    if(itemType === "lesson") {
        let lessonParent = btn.parents(".lesson-container").first();
        if(!lessonParent.length) return;

        let moduleId = lessonParent.attr("data-module-id");
        let lessonElements = lessonParent.find(".course-menu-submodule[data-content-id]");
        titleId = lessonElements.length + 1;
        nextItemId = moduleId + "-" + getRandomUnqId();

        newCourseContentLesson = courseContentHtml(itemType, nextItemId, titleId);
    }
    else if(itemType === "module") {
        let moduleElements = menuParent.find(".course-menu-module[data-content-id]");
        titleId = moduleElements.length + 1;
        nextItemId = getRandomUnqId();
        childId = nextItemId + "-" + getRandomUnqId();

        newCourseContentModule = courseContentHtml(itemType, nextItemId, titleId);
        newCourseContentLesson = courseContentHtml("lesson", childId, 1);
    }

    if(titleId > maxElements[itemType]) return;

    $(courseItemHtml(itemType, nextItemId, titleId, childId)).insertBefore(btn);
    courseContentContainer.append(newCourseContentModule)
    courseContentContainer.append(newCourseContentLesson)

    let targetBtn = menuParent.find(".course-btn-element[data-content-type=" + itemType + "][data-content-id=" + nextItemId + "]");
    if(targetBtn.length) targetBtn.first().trigger("click");
    if(newCourseContentLesson.find(".courses-cover-thumb").length)
        initFilepondElement(newCourseContentLesson.find(".courses-cover-thumb").first());
}

function switchCourseContentView(btn) {
    let courseContentContainer = $(document).find("#courseContent");
    let courseContentCollapse = $(document).find("#courseContentCollapse");
    let courseSettingsCollapse = $(document).find("#courseSettings");
    if(!courseContentContainer.length) return;
    courseContentContainer = courseContentContainer.first();
    let menuContainer = $(document).find("#course-content-menu");
    if(!menuContainer.length) return;
    menuContainer = menuContainer.first();


    let itemType = btn.attr("data-content-type"), itemId = btn.attr("data-content-id"), mediaId = btn.attr("data-video-id"), isEmbed = btn.attr("data-is-embed") === "yes"
    if(!["module", "lesson"].includes(itemType)) return;


    let selector = ".content-parent[data-content-type=" + itemType + "][data-content-id=" + itemId + "]"
    if(!courseContentContainer.find(selector).length) return;
    let targetContent = courseContentContainer.find(selector).first();


    let btnElements = menuContainer.find(".course-btn-element");
    btnElements.each(function () {
        if($(this).hasClass("course-btn-active")) $(this).removeClass("course-btn-active");
    })

    let contentParents = courseContentContainer.find(".content-parent");
    contentParents.each(function () {
        if(!$(this).hasClass("d-none")) $(this).addClass("d-none");
    })



    if(!btn.hasClass("course-btn-active")) btn.addClass("course-btn-active");
    if(targetContent.hasClass("d-none")) targetContent.removeClass("d-none");

    if(courseContentCollapse.length && !courseContentCollapse.hasClass("show")) $(document).find("#courseContentCollapseToggler").trigger("click");
    if(courseSettingsCollapse.length && courseSettingsCollapse.hasClass("show")) $(document).find("#courseSettingsToggler").trigger("click");

    let currentHiddenElement = $(document).find("#current-lesson");
    if(currentHiddenElement.length) currentHiddenElement.first().val(itemId);


    if(mediaId !== undefined && isEmbed) mediaViewGetCompletion(mediaId);


    let collapseParent = btn.parents(".navbar-collapse").first();
    if(!collapseParent.length) return true;
    if(collapseParent.hasClass("show")) return true;

    let collapseToggleId = collapseParent.attr("id");
    console.log($(document).find("[data-toggle=collapse][data-toggle-id=" + collapseToggleId + "]").length);
    let collapseToggler = $(document).find("[data-toggle=collapse][data-toggle-id=" + collapseToggleId + "]");
    if(collapseToggler.length) collapseToggler.first().trigger("click");

}


function removeCourseItem(btn) {
    let menuBtnElement = btn.parents(".course-btn-element").first();
    if(!menuBtnElement.length) return false;

    let contentId = menuBtnElement.attr("data-content-id"),
    contentType = menuBtnElement.attr("data-content-type");

    if(contentType === "module" && contentId === "1") return false;
    if(contentType === "lesson" && contentId === "1-1") return false;

    let contentContainer = $(document).find("#courseContent").first();
    let contentItem = contentContainer.find(".content-parent[data-content-type=" + contentType + "][data-content-id=" + contentId + "]");
    if(!contentItem.length) return false;
    let itemsToRemove = [menuBtnElement, contentItem];

    if(contentType === "module") {
        let leftMenu = menuBtnElement.parents("#course-content-menu").first();
        let lessonContainer = leftMenu.find(".lesson-container[data-module-id=" + contentId + "]");
        if(!lessonContainer.length) return false;

        itemsToRemove.push(lessonContainer.first());

        for(let i = 1; i < 10; i++) {
            let lessonItem = contentContainer.find(".content-parent[data-content-type=lesson][data-content-id=" + contentId + "-" + i + "]");
            if(!lessonItem.length) break;
            itemsToRemove.push(lessonItem);
        }
    }

    if(empty(itemsToRemove)) return false;
    for(let item of itemsToRemove) item.remove();

    let updatedMenuLeft = $(document).find("#course-content-menu").first();



    updatedMenuLeft.find(".course-btn-element").first().trigger("click");
}





$(document).on("click", ".remove-course-item", function(e) {
    e.stopPropagation();
    removeCourseItem($(this));
});
$(document).on("click", ".course-btn-element", function() {
    switchCourseContentView($(this))
})

$(document).on("change", "select[name=course_access]", function () {
    let select = $(this);
    let priceElement = select.siblings(".price-element");
    if(!priceElement.length) return
    priceElement = priceElement.first();

    let currentValue = select.val();
    if(currentValue === "paid" && priceElement.hasClass("d-none")) priceElement.removeClass("d-none");
    if(currentValue !== "paid" && !priceElement.hasClass("d-none")) priceElement.addClass("d-none");
})



function assembleCourseDetails() {
    let settingsParent = $(document).find("#courseSettings").first(),
        contentParent = $(document).find("#courseContent").first(),
        menuContainer = $(document).find("#course-content-menu").first();

    if(!settingsParent.length || !contentParent.length || !menuContainer.length) {};
    let collector = {settings: {module: 0, lesson: 0}, content: []};

    let settingsSelectors = {
        title: "input[name=course_title]",
        description: "textarea[name=courseDescription]",
        access: "select[name=course_access]",
        price: "input[name=course_price]",
        cover_image: "input[type=hidden][name=fileuploader-list-photo]",
        media_downloadable: "input[type=checkbox][name=media_downloadable]",
        strict_flow: "input[type=checkbox][name=strict_flow]",
    };

    let contentSelectors = {
        module: {
            parent: ".content-parent[data-content-type=module]",
            children: {
                title: "input[name=module_title]",
            }
        },
        lesson: {
            parent: ".content-parent[data-content-type=lesson]",
            children: {
                video: ["input[name=lesson_video_url]", "input[type=hidden][name=fileuploader-list-photo]"],
                title: "input[name=lesson_title]",
                description: "textarea[name=lesson_context]",
            }
        },
    };


    for(let key in settingsSelectors) {
        let selector = settingsSelectors[key], value;
        let element = settingsParent.find(selector).first();
        if(!element.length) {
            console.log("not find " + selector);
            continue;
        }

        if(["strict_flow", "media_downloadable"].includes(key)) value = element.is(":checked");
        else if(key === "cover_image") {
            value = JSON.parse(element.val());
            if(empty(value)) {
                console.log("empty files cont " + selector);
                continue;
            }
            value = getObjLastElement(value).file;
        }
        else {
            value = element.val();
            if(empty(value) && key !== "price") {
                console.log("empty val " + selector);
                continue;
            }
        }


        collector.settings[key] = value;
    }

    let lessons = [], modules = [];

    for(let type in contentSelectors) {
        let typeDetails = contentSelectors[type];
        let typeSelector = typeDetails.parent;
        let typeChildren = typeDetails.children;
        let isError = true;


        let typeParentElements = contentParent.find(typeSelector);
        if(!typeParentElements.length) {
            console.log("not parent el " + typeSelector);
            continue;
        }


        typeParentElements.each(function () {
            let typeCollector = {};
            let typeParent = $(this), value;
            collector.settings[type] += 1;
            let contentType = typeParent.attr("data-content-type"),
                contentId = typeParent.attr("data-content-id");
            let menuElement = menuContainer.find(".course-btn-element[data-content-type=" + contentType + "][data-content-id=" + contentId + "]").first();
            if(!menuElement.length) {
                console.log("not find menu el length " + typeSelector);
                return;
            }


            for(let key in typeChildren) {
                let selectors = typeChildren[key];
                if(typeof selectors !== "object") selectors = [selectors];

                let valueFetched = false;

                for(let selector of selectors) {
                    if(key === "video") {
                        let element = typeParent.find(selector).first();
                        if(!element.length) {
                            console.log("wheres el?  " + selector);
                            continue;
                        }

                        value = element.val();
                        if(element.attr("name") === "lesson_video_url") {
                            if(!validURL(value)) {
                                console.log("invalid url " + selector);
                                continue;
                            }
                        }
                        else {
                            value = JSON.parse(value);
                            if(empty(value)) {
                                console.log("empty files " + selector);
                                continue;
                            }
                            value = getObjLastElement(value).file;
                        }


                        valueFetched = true;
                        isError = false;
                        break;
                    }
                    else {
                        let element = typeParent.find(selector).first();
                        if(!element.length) {
                            console.log("not find content el " + selector);
                            continue;
                        }

                        value = element.val();
                        if(empty(value)) {
                            console.log("empty val of content el " + selector);
                            continue;
                        }
                        valueFetched = true;
                        isError = false;
                    }
                }
                if(valueFetched) typeCollector[key] = value;
            }

            typeCollector.content_id = contentId;
            if(contentType === "module") modules.push(typeCollector);
            else lessons.push(typeCollector);
        })
        // if(isError) return {};
    }


    if(!empty(modules)) {
        for(let module of modules) {
            let moduleId = module.content_id, moduleLessons;

            if(empty(lessons)) moduleLessons = [];
            else {
                moduleLessons = lessons.filter(function (lesson) {
                    return moduleId === (((lesson.content_id).split("-"))[0]);
                })

                moduleLessons = sortByKey(moduleLessons, "content_id", true);
            }

            collector.content.push({
                ...module,
                ...{lessons: moduleLessons}
            });
        }
    }
    return collector;
}



function createCourse(element) {
    let form = $(document).find("#publish_course").first();
    if(!form.length) return false;
    let collector = assembleCourseDetails();
    console.log(collector);

    if(!form.find("input[type=hidden][name=course_content]").length) form.append($('<input type="hidden" name="course_content" />'));
    form.find("input[type=hidden][name=course_content]").first().attr("value", JSON.stringify(collector))


    element.attr({'disabled' : 'true'});
    element.find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');



    $('#progress').show();

    var $error = element.attr('data-error');
    var $errorMsg = element.attr('data-msg-error');

    (function() {

        var bar = $('.progress-bar');
        var percent = $('.percent');
        var percentVal = '0%';

        form.ajaxForm({
            dataType : 'json',
            error: function(responseText, statusText, xhr, $form) {
                console.log("error:  %s", responseText);
                element.removeAttr('disabled');

                if(!xhr) {
                    xhr = '- ' + $errorMsg;
                } else {
                    xhr = '- ' + xhr;
                }

                $('.popout').removeClass('popout-success').addClass('popout-error').html($error+' '+xhr+'').fadeIn('500').delay('5000').fadeOut('500');
                $('#progress').hide();
                bar.width(percentVal).removeClass('bg-success').addClass('bg-primary');
                percent.html(percentVal).removeClass('text-primary-cta');
                element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
            },
            beforeSend: function() {
                bar.width(percentVal);
                percent.html(percentVal);
            },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        bar.width(percentComplete + '%');
                        percent.html(percentComplete+'%');

                        if(percentComplete === 100) {
                            bar.removeClass('bg-primary').addClass('bg-success');
                            percent.addClass('text-primary-cta');
                        }
                    }
                }, false);
                return xhr;
            },
            // uploadProgress: function(event, position, total, percentComplete) {
            //     console.log(["uploadprogress..", {event, position, total, percentComplete}]);
            //     var percentVal = percentComplete + '%';
            //     bar.width(percentVal);
            //     percent.html(percentVal);
            //
            //     if(parseInt(percentComplete) === 100) {
            //         bar.removeClass('bg-primary').addClass('bg-success');
            //         percent.addClass('text-primary-cta');
            //     }
            // },
            success: function(result) {
                //===== SUCCESS =====//
                if (result.success !== false && ! result.pending) {

                    $('#progress').hide();
                    bar.width(percentVal).removeClass('bg-success').addClass('bg-primary');
                    percent.html(percentVal).removeClass('text-primary-cta');

                    $('#errorUdpate').fadeOut(500);
                    $('#showErrorsUdpate').html('');
                    element.addClass('e-none');
                    element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

                    setTimeout(function () {
                        swal({
                            type: 'success',
                            title: "Course created",
                            text: "You can now view the course through your home page along with your other posts",
                            confirmButtonText: "ok"
                        }, function() {
                            window.location = (window.location.href).replace(window.location.pathname, "");
                        });
                    }, 1000)


                } else if (result.success && result.pending) {

                    if (! result.encode) {
                        // Success post pending
                        $('#alertPostPending').fadeIn();
                    } else {
                        swal({
                            type: 'info',
                            title: video_on_way,
                            text: video_processed_info,
                            confirmButtonText: ok
                        });
                    }


                    $('#progress').hide();
                    bar.width(percentVal).removeClass('bg-success').addClass('bg-primary');
                    percent.html(percentVal)

                    $('#errorUdpate').fadeOut(500);
                    $('#showErrorsUdpate').html('');
                    element.addClass('e-none');
                    element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

                } else {
                    $('#progress').hide();
                    bar.width(percentVal).removeClass('bg-success').addClass('bg-primary');
                    percent.html(percentVal).removeClass('text-primary-cta');

                    var error = '';
                    var $key = '';

                    for( $key in result.errors ) {
                        error += '<li><i class="fa fa-times-circle"></i> ' + result.errors[$key] + '</li>';
                    }

                    $('#showErrorsUdpate').html(error);
                    $('#errorUdpate').fadeIn(500);

                    element.removeAttr('disabled');
                    element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
                }
            }//<----- SUCCESS
        }).submit();
    })(); //<--- FUNCTION %


}





function paginateLessons(btn) {
    let targetId = btn.attr("data-toggle-id");
    let targetBtn = $(document).find(".course-btn-element[data-content-id=" + targetId + "]");
    if(targetId !== null && targetBtn.length) targetBtn.first().trigger("click");
}



function timeTracking(el) {
    console.log("initialized tracking");
    var videoElement = el.get(0), videoId = el.attr("data-video-id");

    // handler to let me resize the array once we know the length
    Array.prototype.resize = function(newSize, defaultValue) {
        while(newSize > this.length)
            this.push(defaultValue);
        this.length = newSize;
    }

    var duration = 0; // will hold length of the video in seconds
    var watched = new Array(0);
    var finishedWatching = false;
    var trivialityBorder = .8;

    let  roundUp = function (num, precision) {
        return Math.ceil(num * precision) / precision
    }

    let timeupdate = function () {
        let currentTime = parseInt(videoElement.currentTime);
        watched[currentTime] = 1;

        var sum = watched.reduce(function(acc, val) {return acc + val;}, 0);
        console.log([duration * trivialityBorder - sum, videoId]);

        if ((sum >= (duration * trivialityBorder)) && !finishedWatching) {

            finishedWatching = true;
            mediaViewGetCompletion(videoId)

            // trackEvent("passive", videoId);
        }
    }

    let getDuration = function() {
        duration = parseInt(roundUp(videoElement.duration,1));
        watched.resize(duration,0)
    }

    videoElement.addEventListener('loadedmetadata', getDuration, false);
    videoElement.addEventListener('timeupdate',timeupdate,false)
}


const getCsrfToken = () => { return document.querySelector("meta[name=csrf-token]").getAttribute("content"); }


function requestServer(endpoint, params, callbackMethod = null, callbackArguments = {}) {
    if(!("_token" in params)) params._token = getCsrfToken();
    $.ajax({
        method: "post",
        url: endpoint,
        data: params,
        success: (res) => {
            if(callbackMethod !== null && (callbackMethod in window)) {
                if(Object.keys(callbackArguments).length) {
                    for(let key in callbackArguments) res[key] = callbackArguments[key];
                }
                window[callbackMethod](res);
            }
        },
        error: (res) => {
            console.log("result error: " + res.responseText);
        }
    })
}

function mediaViewGetCompletion(mediaId) {
    let menuParent = $(document).find("#course-content-menu").first(),
        courseBtn = menuParent.find(".course-btn-element[data-video-id=" + mediaId + "]"),
        pageParent = $(document).find("#course-page").first();

    if(!courseBtn.length || !pageParent.length) return false;
    let updateId = pageParent.attr("data-update-id");

    requestServer("/course/ajax/media-view", {update_id: updateId, media_id: mediaId}, "setMediaCompletionIconOnCompletion", {media_id: mediaId})
}

function markCourseComplete() {
    let updateId = $(document).find("#course-page").first().attr("data-update-id");
    console.log(updateId);
    if(empty(updateId)) return false;

    requestServer("/course/ajax/complete", {update_id: updateId}, "handleCourseCompletionResponse")
}
function handleCourseCompletionResponse(res) {
    if(res.status === "error") {
        swal({
            type: 'error',
            title: "Could not mark course as complete",
            text: res.message,
            confirmButtonText: "ok"
        });
        return false;
    }
    window.location = res.redirect_url;
}

function setMediaCompletionIconOnCompletion(res) {
    console.log(res);
    if(!res.is_completed) return false;
    let menuParent = $(document).find("#course-content-menu").first(),
        courseBtn = menuParent.find(".course-btn-element[data-video-id=" + res.media_id + "]");

    if(!courseBtn.length) return false;
    courseBtn.each(function () {
        let btn = $(this);
        if(btn.hasClass("lessonIsComplete")) return;
        let lessonParent = btn.parents(".dataParentContainer").first();
        let moduleId = (lessonParent.attr("id")).replace("_toggler", "");

        let completionTracker = menuParent.find("#" + moduleId + "_completion_tracker").first();
        let completionTotal = parseInt(completionTracker.attr("data-total-count"));
        let newCompletionCount = (parseInt(completionTracker.text()) + 1);

        completionTracker.text( newCompletionCount );
        let completionBtnMarker = btn.find(".completion-icon").first();
        if(completionBtnMarker.hasClass("bi-circle")) completionBtnMarker.removeClass("bi-circle").removeClass("bi-circle").addClass("bi-check-circle").addClass("text-primary-cta");

        console.log([newCompletionCount, completionTotal, completionTracker.parents(".completion-tracker-parent").first().length]);
        if(completionTotal === newCompletionCount) {
            completionTracker.parents(".completion-tracker-parent").first().addClass("text-primary-cta");
            btn.addClass("lessonIsComplete");
        }
    })

    if(res.course_is_completed) {
        let completionBtn = $(document).find("#course-completion-btn");
        if(!completionBtn.length) return false;
        completionBtn = completionBtn.first();

        if(completionBtn.hasClass("d-none")) completionBtn.removeClass("d-none");
    }
}



if($(document).find(".course-video-track").length) {
    if(typeof timeTracking == "function") {
        $(document).find(".course-video-track").each(function () {
            timeTracking($(this));
        });
    }
}

$(document).ready(function (){
    (function(){
        let menuParent = $(document).find("#course-content-menu").first(),
            courseFirstLesson = menuParent.find(".course-btn-element[data-content-type=lesson][data-content-id=1-1]");
        if(!courseFirstLesson.length) return;

        mediaViewGetCompletion(courseFirstLesson.first().attr("data-video-id"));
    })( jQuery );

})

function htmlDownload(targetId) {
    let div = document.getElementById(targetId);

    html2canvas(div, {
        scrollX: 0,
        scrollY: -window.scrollY
    }).
        then(function (canvas) {
            canvas.toBlob((blob) => {
                let  a = document.createElement('a');
                let url = URL.createObjectURL(blob);
                console.log(blob);
                a.style.display = 'none';
                a.href = url;
                a.download = 'diploma.png';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove()
            });
        })
}


if($(document).find("#downloadCanvas").length) htmlDownload($(document).find("#downloadCanvas").attr("data-target-id"))




$(document).on("click", "#course-completion-btn", function (){ markCourseComplete() })
$(document).on("click", ".previous-lesson-button, .next-lesson-button", function (){ paginateLessons($(this)) })
$(document).on("click", "button[name=publish_course]", function (e){ e.preventDefault(); createCourse($(this)) })
$(document).on("click", ".course-module-add-more", function (){ addCourseItems($(this)) })
