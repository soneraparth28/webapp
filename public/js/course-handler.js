
function courseContentHtml(itemType, nextId, titleId) {
    let  html = ''
    if(itemType === "module") {
        html += '<div class="flex-col-start content-parent dataParentContainer d-none" data-content-type="module" data-content-id="' + nextId + '">';
        html += '<p class="mb-0 font-weight-bold">Module title</p>';
        html += '<input class="form-control mt-1 mimic-text-input mb-3" value="Module ' + titleId + '" autocomplete="off" name="module_title" ';
            html += 'placeholder="" type="text" data-mimic-search="module_' + nextId + '" style="border-radius: 10px !important;"/>';
            html += '<p class="mb-0 font-weight-bold">Availability after weeks</p>';
            html += '<select name="duration" id="duration" class="form-control mt-1 mimic-text-input">';
                for(var i=0; i<=12; i++)
                {
                    html += '<option value="'+i+'">'+i+'</option>';
                }
            html += '</select>';
        html += '</div>';
    }
    else if(itemType === "lesson") {
        html += '<div class="flex-col-start content-parent dataParentContainer d-none mt-4" data-content-type="lesson" data-content-id="' + nextId + '">';
            html += '<div class="file-upload-parent w-100" data-thumb-type="video" data-file-element-id="' + nextId + '">';
               html += '<div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">';
                    html += '<video class="d-none w-100 noSelect" controls>';
                        html += '<source src="" type="video/mp4">';
                        html += 'Your browser does not support the video tag.';
                    html += '</video>';
                    html += '<div class="flex-col-start flex-align-center">';
                        html += '<p class="mb-0 color-light-gray">Add video</p>';
                        html += '<i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>';
                    html += '</div>';
                html += '</div>';
                html += '<input type="file" name="photo" id="courseCoverPhoto" accept="video/*" class="filepond w-100">';
            html += '</div>';
            html += '<p class="mt-4 mb-0 font-weight-bold">Or lesson video link</p>';
            html += '<input class="form-control mt-1 mimic-text-input" style="border-radius: 10px !important;" value="" autocomplete="off" name="lesson_video_url"';
                html += ' placeholder="https://www.youtube.com/watch?v=BvWt" type="url" />';
            html += '<input class="mt-4 w-100 font-18 bg-med-light-gray border-0 color-gray p-4 mimic-text-input" style="border-radius: 10px !important;" autocomplete="off" name="lesson_title"';
                html += ' placeholder="Your Lesson Title Goes Here" type="text" data-mimic-search="lesson_' + nextId + '" value="Lesson ' + titleId + '"/>';
            html += '<p class="mt-4 mb-0 font-weight-bold">Lesson context</p>';
            html += '<textarea name="lesson_context" style="border-radius: 10px !important;" data-post-length="{{$settings->update_length}}" rows="4" cols="40"';
                html += ' placeholder="Write something..." class="form-control textareaAutoSize mt-1 emojiArea"></textarea>';
                html += '<p class="mt-4 mb-0 font-weight-bold">Lesson File</p>';
                html += '<div class="file-upload-parent w-100 mt-4" data-thumb-type="file"  data-file-element-id="1-1">';
                html += '<span class="content-holder"></span>';
                html += '<div class="courses-cover-thumb flex-row-around flex-align-center mt-2 border-radius-10px">';
                html += '<div class="flex-col-start flex-align-center">';
                html += '<p class="mb-0 color-light-gray">Add File</p>';
                html += '<i class="fa fa-plus-circle font-20 mt-2 color-light-gray"></i>';
                html += '</div></div>';
                html += '<input type="file" name="file" id="courseCoverPhoto" class="filepond w-100"></div>';
        html += '</div>';
    }

    return $(html);
}


function courseItemHtml(itemType, nextId, titleId, childId) {
    let  html = ''
    if(itemType === "lesson") {
        html += '<div class="flex-row-between flex-align-start flex-nowrap course-menu-submodule course-btn-element mt-3" data-content-type="lesson" data-content-id="' + nextId + '">';
            html += '<div class="flex-row-start flex-align-center">';
                html += '<i class="fa fa-book-open font-18"></i>';
                html += '<p class="font-18 mb-0 ml-2 noSelect wrap" data-mimic-id="lesson_' + nextId + '">Lesson ' + titleId + '</p>';
            html += '</div>';
            html += '<i class="ml-2 font-18 bi bi-x-circle cursor-pointer hover-cta-color remove-course-item"></i>';
        html += '</div>';
    }
    else if(itemType === "module") {
        html += '<div class="item-container-box p-3 w-100 color-gray mt-4" data-module-id="' + nextId + '">';
            html += '<div class="flex-row-between flex-align-center flex-nowrap mb-2 course-menu-module course-btn-element" data-content-type="module" data-content-id="' + nextId + '">';
                html += '<p class="font-20 font-weight-bold noSelect mb-0 hover-underline cursor-pointer" data-mimic-id="module_' + nextId + '">Module ' + titleId + '</p>';
                html += '<i class="font-20 bi bi-x-circle cursor-pointer hover-cta-color ml-2 remove-course-item"></i>';
            html += '</div>';
            html += '<div class="flex-col-start flex-align-start lesson-container mt-2">';
                html += courseItemHtml("lesson", childId, 1);
            html += '</div>';
            html += '<button class="mt-2 dark-btn pt-1 pb-1 pl-3 pr-3 flex-row-start flex-align-center course-module-add-more" data-add-item="lesson">';
                html += '<i class="bi bi-plus-circle font-18"></i>';
                html += '<span class="ml-2 mb-0">Add lesson</span>';
            html += '</button>';
        html += '</div>';
    }

    return html;
}

const mobileModuleNavHtml = (openId, title) => {
    return $(
        '<p class="mb-0 font-16 px-2 pb-3 border-bottom openModuleMobileView cursor-pointer hover-opacity-8 font-weight-bold course-btn-active border-dark" ' +
            'data-open-id="' + openId + '">' +
        "Module " + title +
        '</p>'
    );
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
    let menuParent = $(document).find("#course-content-menu").first(),
        courseContentContainer = $(document).find("#courseContent"), childId,
        maxElements = {
            lesson: 50,
            module: 50
        }

    if(!courseContentContainer.length) return;
    courseContentContainer = courseContentContainer.first();


    if(itemType === "lesson") {
        let moduleParent = btn.parents(".item-container-box[data-module-id]").first();
        let lessonParent = moduleParent.find(".lesson-container").first();
        if(!lessonParent.length) return;

        let moduleId = moduleParent.attr("data-module-id");
        let lessonElements = lessonParent.find(".course-menu-submodule[data-content-id]");
        titleId = lessonElements.length + 1;
        if(titleId > maxElements[itemType]) return;

        nextItemId = moduleId + "-" + getRandomUnqId();

        newCourseContentLesson = courseContentHtml(itemType, nextItemId, titleId);
        courseItemList.push(nextItemId.toString());

        //--------------------
        insertNewElements(moduleId, itemType, nextItemId, titleId);
    }
    else if(itemType === "module") {
        let moduleElements = menuParent.find(".course-menu-module[data-content-id]");
        titleId = moduleElements.length + 1;
        if(titleId > maxElements[itemType]) return;


        nextItemId = getRandomUnqId();
        childId = nextItemId + "-" + getRandomUnqId();

        newCourseContentModule = courseContentHtml(itemType, nextItemId, titleId);
        newCourseContentLesson = courseContentHtml("lesson", childId, 1);

        courseItemList.push(nextItemId.toString());
        courseItemList.push(childId.toString());

        //--------------------
        insertNewElements(null, itemType, nextItemId, titleId, childId);
    }

    courseContentContainer.append(newCourseContentModule)
    courseContentContainer.append(newCourseContentLesson)

    let targetBtn = menuParent.find(".course-btn-element[data-content-type=" + itemType + "][data-content-id=" + nextItemId + "]");
    if(targetBtn.length) targetBtn.first().trigger("click");
    if(newCourseContentLesson.find(".courses-cover-thumb").length)
    {
        initFilepondElement(newCourseContentLesson.find(".courses-cover-thumb").first());
        initFilepondElement(newCourseContentLesson.find(".courses-cover-thumb").last());
    }
}

function insertNewElements(currentModuleId, itemType, nextItemId, titleId, childId) {
    let menuParent = $(document).find("#course-content-menu").first();
    let mobileMenuParent = $(document).find("#mobile-lesson-menu").first();
    let mobileModuleNavParent = $(document).find("#mobileModuleNav").first();


    if(itemType === "lesson") {
        $(document).find(".item-container-box[data-module-id=" + currentModuleId + "]").each(function () {
            $(this).find(".lesson-container").first().append($(courseItemHtml(itemType, nextItemId, titleId, childId)));
        })
    }
    else {
        $(courseItemHtml(itemType, nextItemId, titleId, childId)).insertBefore(
            menuParent.find(".course-module-add-more[data-add-item=\"module\"]").first().parents(".item-container-box").first()
        );
        mobileMenuParent.append($(courseItemHtml(itemType, nextItemId, titleId, childId)));
        mobileModuleNavParent.append(mobileModuleNavHtml(nextItemId, titleId));
    }
}



function switchCourseContentView(btn) {
    let courseContentContainer = $(document).find("#courseContent"), moduleId;
    if(!courseContentContainer.length) return;
    courseContentContainer = courseContentContainer.first();
    let menuContainer = $(document).find("#course-content-menu");
    if(!menuContainer.length) return;
    menuContainer = menuContainer.first();


    let itemType = btn.attr("data-content-type"), itemId = btn.attr("data-content-id"), mediaId = btn.attr("data-video-id"), isEmbed = btn.attr("data-is-embed") === "yes"
    if(!["module", "lesson"].includes(itemType)) return;

    if(itemType === "module") moduleId = itemId;
    else moduleId = btn.parents("[data-module-id]").first().attr("data-module-id");

    let selector = ".content-parent[data-content-type=" + itemType + "][data-content-id=" + itemId + "]"
    if(!courseContentContainer.find(selector).length) return;
    let targetContent = courseContentContainer.find(selector).first();


    let btnElements = $(document).find(".course-btn-element");
    btnElements.each(function () {
        if($(this).hasClass("course-btn-active")) $(this).removeClass("course-btn-active");
    })

    let contentParents = courseContentContainer.find(".content-parent");
    contentParents.each(function () {
        if(!$(this).hasClass("d-none")) $(this).addClass("d-none");
    })


    let containers = [menuContainer];
    if($(document).find("#mobile-lesson-menu").length) containers.push($(document).find("#mobile-lesson-menu").first());
    for(let container of containers) {
        let menuBtn = container.find(".course-btn-element[data-content-type=" + itemType + "][data-content-id=" + itemId + "]").first();
        if(menuBtn.length && !menuBtn.hasClass("course-btn-active")) menuBtn.addClass("course-btn-active");
    }

    if(targetContent.hasClass("d-none")) targetContent.removeClass("d-none");

    contentViewChange("content")
    nextBtnSetValues(itemId,isViewingLastCreationItem(itemId))
    paginateThroughMobileView(null, moduleId);
    mobileMenuToggleModuleView(moduleId);

    if(mediaId !== undefined && isEmbed) mediaViewGetCompletion(mediaId);

    let collapseParent = btn.parents(".navbar-collapse").first();
    if(!collapseParent.length) return true;
    if(collapseParent.hasClass("show")) return true;

    let collapseToggleId = collapseParent.attr("id");
    let collapseToggler = $(document).find("[data-toggle=collapse][data-toggle-id=" + collapseToggleId + "]");
    if(collapseToggler.length) collapseToggler.first().trigger("click");
}

function mobileMenuToggleModuleView(activeModuleId) {
    let menuParent = $(document).find("#mobile-lesson-menu").first(), activeModule = menuParent.find(".item-container-box[data-module-id=" + activeModuleId + "]").first();
    if(!menuParent.length || !activeModule.length) return;

    menuParent.find(".item-container-box[data-module-id]").each(function () {
        let el = $(this), id = el.attr("data-module-id");
        if(id !== activeModuleId && !el.hasClass("d-none")) el.addClass("d-none");
    })
    activeModule.removeClass("d-none");
}

function nextBtnSetValues(data, viewingLast = false) {
    let btns = $(".course-next-btn");
    if(!btns.length) return;
    btns.each(function () {
        let btn = $(this);
        btn.attr("data-current-view", data);
        if(viewingLast) btn.text(editCourse ? "Update course" : "Publish Course");
        else btn.text("Next");
    })
}

function isViewingLastCreationItem(currentId = null) {
    if(currentId === null) currentId = $(".course-next-btn").first().attr("data-current-view");
    let currentKey = getKeyByValue(courseItemList, currentId);
    if(currentKey === undefined || currentId === "") return false;

    let nextKey = (parseInt(currentKey) +1).toString();
    return !Object.keys(courseItemList).includes(nextKey);
}

function createCourseNextItem(btn) {
    if(isViewingLastCreationItem()) {
        createCourse(btn);
        return true;
    }
    let currentContentId = btn.attr("data-current-view"), nextId, nextKey = "";
    if(currentContentId === "") {
        nextId = "1";
        nextKey = 0;
    }
    else {
        let key = getKeyByValue(courseItemList, currentContentId);
        if(key === undefined) return false;

        // let nextKey = key +1;
        let nextKey = (parseInt(key) +1).toString();
        if(!Object.keys(courseItemList).includes(nextKey)) return false;
        nextId = courseItemList[nextKey];
    }


    let btnTargetElement = $(document).find("#course-content-menu").first().find(".course-btn-element[data-content-id=" + nextId + "]").first()
    if(btnTargetElement.length) btnTargetElement.trigger("click");
    nextBtnSetValues(nextId, isViewingLastCreationItem(nextId));
}


function contentViewChange(type) {
    if(!["settings", "content"].includes(type)) return false;
    let colorElements = {
            border: {
                settings: $(document).find(".line.stageSettings"),
                content: $(document).find(".line.stageContent")
            },
            text: {
                settings: $(document).find("td.stageSettings"),
                content: $(document).find("td.stageContent")
            },
            background: {
                settings: $(document).find("th .square.stageSettings"),
                content: $(document).find("th .square.stageContent")
            },
            icon: {
                settings: $(document).find("th i.stageSettings"),
                content: $(document).find("th i.stageContent")
            }
        },
        stageTextElement = $(document).find("#currentStageText").first(),
        settingsContent =  $(document).find("#courseSettings").first(),
        courseContent =  $(document).find("#courseContent").first(),
        mobileMenu =  $(document).find("#mobile-lesson-menu").first();

    stageTextElement.text(type === "settings" ? "Course Settings" : "Course Content");
    if(type === "settings") {
        if(settingsContent.length && settingsContent.hasClass("d-none")) settingsContent.removeClass("d-none");
        if(!courseContent.hasClass("d-none")) courseContent.addClass("d-none");
        if(!mobileMenu.hasClass("d-none")) mobileMenu.addClass("d-none");
    }
    else {
        if(mobileMenu.hasClass("d-none")) mobileMenu.removeClass("d-none");
        if(courseContent.hasClass("d-none")) courseContent.removeClass("d-none");
        if(!settingsContent.hasClass("d-none")) settingsContent.addClass("d-none");
    }

    let progressiveList = type === "content" ? ["content", "settings"] : (type === "settings" ? ["settings"]: []);

    for(let colorType in colorElements) {
        for(let keyType in colorElements[colorType]) {
            let elements = colorElements[colorType][keyType];
            if(!elements.length) continue;

            let inActiveClass = colorType === "border" ? "border-med-light-gray" : (colorType === "text" ? "color-light-gray" : (colorType === "icon" ? "color-light-gray" : "bg-med-light-gray"));
            let activeClass = colorType === "border" ? "border-dark" : (colorType === "text" ? "color-dark" : (colorType === "icon" ? "color-light" : "bg-dark"));

            elements.each(function () {
                if(progressiveList.includes(keyType)) $(this).removeClass(inActiveClass).addClass(activeClass);
                else $(this).removeClass(activeClass).addClass(inActiveClass);
            })
        }
    }

    if(type === "settings") {
        nextBtnSetValues("")
        let activeBtn = $("#course-content-menu").find(".course-btn-active");
        if(activeBtn.length) activeBtn.first().removeClass("course-btn-active");
    }
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
    let itemsToRemove = [contentItem.first()];
    
    let idRemovalList = [contentId];
    let mobileMenu = $(document).find("#mobile-lesson-menu").first().find(".item-container-box[data-module-id=" + contentId + "]").first();
    let mobileModuleNavMenu = $(document).find("#mobileModuleNav").first();
    
    if(contentType === "lesson") {
        $(document).find(".course-menu-submodule[data-content-type=lesson][data-content-id=" + contentId + "]").each(function () {
            itemsToRemove.push($(this));
        })
    }
    else if(contentType === "module") {
        let moduleContainer = menuBtnElement.parents(".item-container-box[data-module-id=" + contentId + "]").first();
        let lessonContainer = moduleContainer.find(".lesson-container").first();
        if(!moduleContainer.length) return false;
        

        itemsToRemove.push(moduleContainer.first());
        let containers = [];
        if(lessonContainer.length) containers.push(lessonContainer);
        if(mobileMenu.length) containers.push(mobileMenu);
        
        for(let container of containers) {
            container.find(".course-menu-submodule").each(function () {
                let lessonId = $(this).attr("data-content-id");

                let lessonItem = contentContainer.find(".content-parent[data-content-type=lesson][data-content-id=" + lessonId + "]");
                if(!lessonItem.length) return;
                idRemovalList.push(lessonId);
                itemsToRemove.push(lessonItem);
            })
        }
        let moduleNavItem = mobileModuleNavMenu.find(".openModuleMobileView[data-open-id=" + contentId + "]").first();
        if(moduleNavItem.length) itemsToRemove.push(moduleNavItem);
    }
    

    if(empty(itemsToRemove)) return false;
    for(let item of itemsToRemove) item.remove();

    for(let id of idRemovalList) {
        let key = getKeyByValue(courseItemList, id.toString());
        if(typeof key !== undefined) delete courseItemList[key];
    }
    courseItemList = Object.values(courseItemList);
    $(document).find("#course-content-menu").first().find(".course-btn-element").first().trigger("click");
}






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
                duration: "select[name=duration]",
            }
        },
        lesson: {
            parent: ".content-parent[data-content-type=lesson]",
            children: {
                video: ["input[name=lesson_video_url]", "input[type=hidden][name=fileuploader-list-photo]"],
                title: "input[name=lesson_title]",
                description: "textarea[name=lesson_context]",
                lesson_file: "input[type=hidden][name=fileuploader-list-file]",
            }
        },
    };


    for(let key in settingsSelectors) {
        let selector = settingsSelectors[key], value;
        let element = settingsParent.find(selector)
        if(!element.length) {
            console.log("not find " + selector);
            continue;
        }

        if(key === "cover_image") {
            let fileValues = {preload: '', load: ''};
            element.each(function () {
                let el = $(this);
                value = JSON.parse(el.val());

                if(empty(value)) {
                    // console.log("empty files cont " + selector);
                    return;
                }
                value = getObjLastElement(value).file;
                fileValues[(el.hasClass("preload-file") ? 'preload' : 'load')] = value;
            })
            collector.settings[key] = !empty(fileValues.load) ? fileValues.load : fileValues.preload;
        }
        else {
            element = element.first();
            if(["strict_flow", "media_downloadable"].includes(key)) value = element.is(":checked");
            else {
                value = element.val();
                if(empty(value) && key !== "price") {
                    // console.log("empty val " + selector);
                    continue;
                }
            }
            collector.settings[key] = value;
        }
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
                        let element = typeParent.find(selector)
                        if(!element.length) {
                            console.log("wheres el?  " + selector);
                            continue;
                        }


                        let fileValues = {preload: '', load: '', embed: ''};
                        element.each(function () {
                            let el = $(this);
                            value = el.val();

                            if(el.attr("name") === "lesson_video_url") {
                                if(validURL(value)) fileValues.embed = value;
                            }
                            else if(el.hasClass("preload-file")) {
                                value = JSON.parse(value);
                                if(!empty(value)) fileValues.preload = getObjLastElement(value).file;
                            }
                            else {
                                value = JSON.parse(value);
                                if(!empty(value)) fileValues.load = getObjLastElement(value).file;
                            }
                        });

                        let fileKey = !empty(fileValues.embed) ? 'embed' : (!empty(fileValues.load) ? 'load' : 'preload');
                        value = fileValues[fileKey];

                        valueFetched = !empty(value);
                        isError = empty(value);
                        if(valueFetched) break;
                    }
                    else if(key === "lesson_file") {
                        
                        let element = typeParent.find(selector)
                        if(!element.length) {
                            console.log("wheres el?  " + selector);
                            continue;
                        }


                        let fileValues = {preload: '', load: '', embed: ''};
                        element.each(function () {
                            let el = $(this);
                            value = el.val();

                            if(el.hasClass("preload-file")) {
                                value = JSON.parse(value);
                                if(!empty(value)) fileValues.preload = getObjLastElement(value).file;
                            }
                            else {
                                value = JSON.parse(value);
                                if(!empty(value)) fileValues.load = getObjLastElement(value).file;
                            }
                        });

                        let fileKey = !empty(fileValues.embed) ? 'embed' : (!empty(fileValues.load) ? 'load' : 'preload');
                        value = fileValues[fileKey];

                        valueFetched = !empty(value);
                        isError = empty(value);
                        if(valueFetched) break;
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
    collector.edit = editCourse === 1;
    collector.courseId = courseId;

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
                console.log("error:", responseText);
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
                            //percent.addClass('text-primary-cta');
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

                    let swalTitle = editCourse ? "Changes saved" : "Course created";
                    let swalText = editCourse ? "The changes are applied instantly and now viewable" : "You can now view the course through your home page along with your other posts";

                    setTimeout(function () {
                        swal({
                            type: 'success',
                            title: swalTitle,
                            text: swalText,
                            confirmButtonColor: "#000",
                            customClass: {
                                confirmButton: 'dark-btn'
                            },
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
                        error += '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' + result.errors[$key] + '</li>';
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
    // let targetId = btn.attr("data-toggle-id");
    // let targetBtn = $(document).find(".course-btn-element[data-content-id=" + targetId + "]");
    // if(targetId !== null && targetBtn.length) targetBtn.first().trigger("click");
    
    let eq = 0;
    $("#course-content-menu .course-btn-element").each(function(index){
        if($(this).hasClass("course-btn-active")){
            eq = index;
        }
    });
    let target_eq = Number(eq) + Number(1); 
    if(btn.hasClass('light-btn')){
        target_eq = Number(eq) - Number(1); 
    }
    $("#course-content-menu .course-btn-element").eq(target_eq).click();
    
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
    var trivialityBorder = .8

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
        pageParent = $(document).find("#course-page").first(),
        duration = calculateMediaDurationInSeconds(mediaId);

    if(!courseBtn.length || !pageParent.length) return false;
    let updateId = pageParent.attr("data-update-id");

    requestServer("/course/ajax/media-view", {update_id: updateId, media_id: mediaId, duration: duration}, "setMediaCompletionIconOnCompletion", {media_id: mediaId})
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
        if(completionBtnMarker.hasClass("bi-circle-half")) completionBtnMarker.removeClass("bi-circle-half").addClass("bi-check-circle-fill");

        console.log([newCompletionCount, completionTotal, completionTracker.parents(".completion-tracker-parent").first().length]);
        if(completionTotal === newCompletionCount) {
            completionTracker.parents(".completion-tracker-parent").first();
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

function paginateThroughMobileView(btn = null, moduleId = null) {
    if(empty(moduleId)) moduleId = btn.attr("data-open-id");
    let parentElement, leftMenu = $("#course-content-menu").first();

    if(leftMenu.find("#module_" + moduleId + "_toggler").length) parentElement = leftMenu.find("#module_" + moduleId + "_toggler").first();
    else if(leftMenu.find(".item-container-box[data-module-id=" + moduleId + "]").length)
        parentElement = leftMenu.find(".item-container-box[data-module-id=" + moduleId + "]").first();
    else return false;
    parentElement = leftMenu.find(".item-container-box[data-module-id=" + moduleId + "]").first();
    if(!empty(btn)) parentElement.find(".course-btn-element").first().trigger("click"); //Only click if navigate was triggered directly



    $(document).find(".openModuleMobileView").each(function () {
        if($(this).hasClass("course-btn-active")) $(this).removeClass("course-btn-active").removeClass("border-dark").addClass("border-med-light-gray");
    })
    if(empty(btn)) btn = $(document).find(".openModuleMobileView[data-open-id=" + moduleId + "]").first();
    btn.addClass("course-btn-active").removeClass("border-med-light-gray").addClass("border-dark");

}



$(document).on("click", "#course-completion-btn", function (){ markCourseComplete() })
$(document).on("click", ".lesson-paginator", function (){ paginateLessons($(this)) })
$(document).on("click", ".openModuleMobileView", function (){ paginateThroughMobileView($(this)) })
$(document).on("click", "button[name=publish_course]", function (e){ e.preventDefault(); createCourse($(this)) })
$(document).on("click", ".course-module-add-more", function (){ addCourseItems($(this)) })
$(document).on("click", ".course-next-btn", function (e){e.preventDefault(); createCourseNextItem($(this)) })


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



$(document).on("click", "form#email_flow_form button", function (e) {
    e.preventDefault();
});


function toggleFlowForm (clear = true) {
    let form = $(document).find("form#email_flow_form").first();
    if(!form.length) return;

    if(clear) {
        form.find("#text-input").first().html("");
        form.find("input[name=template_name]").first().val("");
        form.find("input[name=flow_id]").first().val(0);
    }

    if(form.hasClass("open")) {
        form.slideUp("slow");
        form.removeClass("open")
        return;
    }
    form.slideDown("slow");
    form.addClass("open");
}

$(document).on("click", "button[name=toggle_flow_form]", function (e) {
    toggleFlowForm();
})




$(document).on("click", "button[name=save_email_flow]", function (e) {
    e.preventDefault();
    let form = $(document).find("form#email_flow_form").first(),
        nameField = form.find("input[name=template_name]").first(),
        flowIdField = form.find("input[name=flow_id]").first(),
        courseIdField = form.find("input[name=course_id]").first(),
        richTextContainer = form.find("#text-input").first(),
        errorField = form.find("#error-field").first();
    if(!nameField.length || !flowIdField.length || !richTextContainer.length || !courseIdField.length || !errorField.length) return;

    errorField.text("");
    errorField.addClass("d-none");
    let data = {
        title: nameField.val(),
        flow_id: flowIdField.val(),
        course_id: courseIdField.val(),
        html_content: richTextContainer.html(),
        _token: getCsrfToken()
    }
    console.log(data)

    $.ajax({
        method: form.attr("method"),
        url: form.attr("action"),
        data,
        success: (res) => {
            console.log(res)

            if(typeof res !== "object") res = JSON.parse(res);

            if(res.status !== "success") {
                errorField.text(res.message);
                errorField.removeClass("d-none");
                return;
            }

            window.location = window.location.href;
        },
        error: (res) => {
            console.log("result error: " + res.responseText);
            errorField.text("Something went wrong. Please try again later");
            errorField.removeClass("d-none");
        }
    })
})



$(document).on("click", ".edit-flow-content", function () {
    let parent = $(this).parents(".dataParentContainer").first(),
        container = parent.find(".flow-html-content").first(),
        form = $(document).find("form#email_flow_form").first(),
        editor = form.find("#text-input").first(),
        titleField = form.find("input[name=template_name]").first(),
        flowIdField = form.find("input[name=flow_id]").first();

    if(!container.length || !editor.length || !titleField.length || !flowIdField.length) return;
    let flowId = container.attr("data-flow-id"), flowTitle = container.attr("data-flow-title"), htmlContent = container.html();
    titleField.val(flowTitle);
    flowIdField.val(flowId);
    editor.html(htmlContent);

    if(!form.hasClass("open")) toggleFlowForm(false)
});


$(document).on("change", "input[name=days_after_unlock]", function () {
    let el = $(this);
    let val = el.val();
    if(typeof val !== "number") val = parseInt(val);

    let flowId = el.attr("data-flow-id"), courseId = el.attr("data-course-id");
    let data = {
        _token: getCsrfToken(),
        flow_id: flowId,
        course_id: courseId,
        new_value: val
    }
    console.log(data)

    $.ajax({
        method: "post",
        url: el.attr("data-dest-url"),
        data,
        success: (res) => {

        },
        error: (res) => {
            console.log("result error: " + res.responseText);
        }
    })

})

function calculateMediaDurationInSeconds(videoId){
    console.log(videoId);

    const video = document.querySelector(`video[data-video-id="${videoId}"]`);

    if (video) {
        // Get the duration of the video element
        console.log(`Video ID: ${videoId}, Duration: ${video.duration.toFixed(2)} seconds`);
        return video.duration.toFixed(2);

        // Log the video ID and duration
    } else {
       return 0;
    }

}