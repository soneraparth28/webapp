/**
 * custom.js - fileuploader
 * Copyright (c) 2021 Innostudio.de
 * Website: https://innostudio.de/fileuploader/
 * Version: 2.2 (27 Nov 2020)
 * License: https://innostudio.de/fileuploader/documentation/#license
 */


function initFilepondElement(thumbParent) {
    // maximum_files_post = 1;
    let thumbFileElement = thumbParent.parents(".dataParentContainer").first().find("input[type=file]").first();
    thumbParent.on("click", function () {
        // $(document).on("click", ".courses-cover-thumb", function () {
        thumbFileElement.trigger("click");
    })


    thumbFileElement.fileuploader({
        fileMaxSize: maxSizeInMb,
        limit: maximum_files_post,
        extensions: [
            'png',
            'jpeg',
            'jpg',
            'gif',
            'video/mp4',
            'video/quicktime',
            'video/3gpp',
            'video/mpeg',
            'video/x-matroska',
            'video/x-ms-wmv',
            'video/vnd.avi',
            'video/avi',
            'video/x-flv',
        ],

        captions: lang,

        dialogs: {
            // alert dialog
            alert: function(text) {
                return swal({
                    title: error_oops,
                    text: text,
                    type: "error",
                    confirmButtonText: ok
                });
            },

            // confirm dialog
            confirm: function(text, callback) {
                confirm(text) ? callback() : null;
            }
        },

        changeInput: ' ',
        enableApi: true,
        addMore: true,


        dragDrop: {
            container: '.courses-cover-thumb'
        },
        afterRender: function(listEl, parentEl, newInputEl, inputEl) {
            console.log("after render");
            var plusInput = listEl.find('.courses-cover-thumb'),
                api = $.fileuploader.getInstance(inputEl.get(0));

            plusInput.on('click', function() {
                api.open();
            });

            api.getOptions().dragDrop.container = plusInput;
        },

        // while using upload option, please set
        // startImageRenderer: false
        // for a better effect
        upload: {
            url: URL_BASE+'/upload/media',
            data: null,
            type: 'POST',
            enctype: 'multipart/form-data',
            start: true,
            synchron: true,
            chunk: 50,
            beforeSend: function(item, listEl, parentEl, newInputEl, inputEl) {

                console.log("before send weuhg    ");
                console.log(item);
                $('.btn-blocked').show();

                // here you can create upload headers
                item.upload.headers = {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                };
                return true;
            },


            onSuccess: function(result, item) {
                console.log("onsuccess");
                console.log([result, item]);
                var data = {};

                if (result && result.files) {
                    data = result;
                } else {
                    data.hasWarnings = true;
                }

                // if success
                if (data.isSuccess && data.files.length) {
                    item.name = data.files[0].name;
                    item.html.find('.content-holder > h5').text(item.name).attr('title', item.name);
                }

                // if warnings
                if (data.hasWarnings) {
                    var error = '';

                    for (var warning in data.warnings) {
                        error += '<li><i class="fa fa-times-circle"></i> ' + data.warnings[warning];
                    }

                    $('#showErrorsUdpate').html(error);
                    $('#errorUdpate').fadeIn(500);

                    item.remove();

                    // item.html.removeClass('upload-successful').addClass('upload-failed');
                    return this.onError ? this.onError(item) : null;
                }

                item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');

                setTimeout(function() {
                    item.html.find('.progress-holder').hide();
                    item.renderThumbnail();

                    item.html.find('.fileuploader-action-popup, .fileuploader-item-image').show();
                }, 400);

                $('.btn-blocked').hide();
            },
            onError: function(item) {
                console.log("error :o");
                console.log(item);
                item.html.find('.progress-holder, .fileuploader-action-popup, .fileuploader-item-image').hide();

                $('.btn-blocked').hide();

            },
            onProgress: function(data, item) {
                console.log("progress");
                console.log([data, item]);
                var progressBar = item.html.find('.progress-holder');

                if(progressBar.length > 0) {
                    progressBar.show();
                    progressBar.find('.fileuploader-progressbar .bar').width(data.percentage + "%");
                }

                item.html.find('.fileuploader-action-popup, .fileuploader-item-image').hide();
            }
        },
        onRemove: function(item) {
            console.log("on remove");
            console.log(item);
            $.post(URL_BASE+'/delete/media', {
                file: item.name,
                _token: $('meta[name="csrf-token"]').attr('content')
            });
        }

    }); // End fileuploader()
}



$(document).ready(function() {

    let prevCoverFileSrc = {};
    window.setInterval(function (){
        let fileSourceElement = $(document).find("input[type=hidden][name=fileuploader-list-photo]");
        if(!fileSourceElement.length) return;

        fileSourceElement.each(function () {
            let parent = $(this).parents(".file-upload-parent").first();
            let fileElementId = parent.attr("data-file-element-id");
            if(!(fileElementId in prevCoverFileSrc)) prevCoverFileSrc[fileElementId] = "";

            let hiddenValues = $(this).val();
            let obj = JSON.parse(hiddenValues);
            if(!Object.keys(obj).length) return;

            let newSrc = getObjLastElement(obj).file;
            if(newSrc === prevCoverFileSrc[fileElementId]) return;

            let mediaType = parent.attr("data-thumb-type"), coverMedia, coverSrc;
            if(mediaType === "photo") {
                coverMedia = parent.find("img");
                if(!coverMedia.length) return;
                coverSrc = URL_BASE + "/public/uploads/updates/images/" + newSrc;
            }
            else if(mediaType === "video") {
                coverMedia = parent.find("video").first().find("source");
                if(!coverMedia.length) return;
                coverSrc = URL_BASE + "/public/uploads/updates/videos/" + newSrc;
            }
            else return;

            prevCoverFileSrc[fileElementId] = obj[0].file;
            coverMedia.each(function () {
                let coverItem = $(this);
                coverItem.attr("src", coverSrc);
                let hiddenItem = mediaType === "photo" ? coverItem : coverItem.parents("video").first();
                if(hiddenItem.hasClass("d-none")) {
                    hiddenItem.removeClass("d-none");
                    hiddenItem.parents().first().find("div").addClass("d-none");
                }
                if(mediaType === "video") {
                    hiddenItem.get(0).load();
                    // hiddenItem.get(0).play();
                }
            })


        })

    }, 500);


    if($(document).find(".courses-cover-thumb").length) {
        $(document).find(".courses-cover-thumb").each(function () {
                initFilepondElement($(this));
        })
    }

});
