//<--------- Messages -------//>
(function($) {
	"use strict";

	//<-------- * TRIM Space * ----------->
	function trim(string) {
		return string.replace(/^\s+/g,'').replace(/\s+$/g,'');
	}

		// Scroll to paginator chat
		var scr = $('#contentDIV')[0].scrollHeight;
		$('#contentDIV').animate({scrollTop: scr},100);

	$(document).on('click','#button-reply-msg',function(s) {

	 s.preventDefault();

	 var element = $(this);

});//<<<-------- * END FUNCTION CLICK * ---->>>>

	//<----- Chat Live
	var request = false;

	function Chat() {

		var param   = /^[0-9]+$/i;
		var lastID  = $('li.chatlist:last').attr('data');
		var liveID  = $('.live-data').attr('data');
		var creator = $('.live-data').attr('data-creator');

		if (! liveOnline) {
			return false;
		}

		if (! request) {
			request = true;

			//****** COUNT DATA
			request = $.ajax({
			  method: "GET",
			  url: URL_BASE+"/get/data/live",
			  data: {
					last_id:lastID ? lastID : 0,
					live_id: liveID,
					creator: creator
				},
				complete: function() { request = false; }
			}).done(function(response) {

			if (response) {

				// Live end
				if (response.status == 'offline') {
					window.location.reload();
					return false;
				}

				// Session Null
				if (response.session_null) {
					window.location.reload();
				}

				// Comments
				if (response.total !== 0) {

					// Scroll to paginator chat
					var scr = $('#contentDIV')[0].scrollHeight;
					$('#contentDIV').animate({scrollTop: scr},100);

				var total_data = response.comments.length;

				for (var i = 0; i < total_data; ++i) {
					$(response.comments[i]).hide().appendTo('#allComments').fadeIn(250);
					}
				} // response.total !== 0

				// Online users
				$('#liveViews').html(response.onlineUsers);

				// Likes
				if (response.likes !== 0) {
					$('#counterLiveLikes').html(response.likes);
				} else {
					$('#counterLiveLikes').html('');
				}

				if (response.time) {
					$('.limitLiveStreaming > span').html(response.time);
				}

			}//<-- response

			},'json');

			}// End Request

	}//End Function TimeLine

	setInterval(Chat, 1000);

	// End Live Stream
	$(document).on('click','#endLive', function(e){

	   e.preventDefault();

	   var element = $(this);
	   element.blur();

	 swal(
	   {
			 title: delete_confirm,
		   text: confirm_end_live,
		   type: "error",
		   showLoaderOnConfirm: true,
		   showCancelButton: true,
		   confirmButtonColor: "#000",
		   confirmButtonText: yes_confirm_end_live,
		   cancelButtonText: cancel_confirm,
           customClass: {
               confirmButton: 'dark-btn',
               cancelButton: 'light-btn'
           },
	     closeOnConfirm: false,
	       },
	       function(isConfirm){

					 if (isConfirm) {
						 (function() {
				        $('#formEndLive').ajaxForm({
				        dataType : 'json',
				        success:  function(response) {
				          // Exit
				        },
				        error: function(responseText, statusText, xhr, $form) {
				             // error
				             swal({
				                 type: 'error',
				                 title: error_oops,
				                 text: ''+error_occurred+' ('+xhr+')',
				               });
				         }
				       }).submit();
				     })(); //<--- FUNCTION %
					 } // isConfirm
	        });
	    });// End live

			// Exit Live
			$(document).on('click','#exitLive', function(e) {
				e.preventDefault();
		 	  var element = $(this);
		 	  element.blur();

	 	 swal(
	 	   {
	 			 title: delete_confirm,
	 		   text: confirm_exit_live,
	 		   type: "error",
	 		   showLoaderOnConfirm: true,
	 		   showCancelButton: true,
	 		   confirmButtonColor: "#000",
               customClass: {
                   confirmButton: 'dark-btn',
                   cancelButton: 'light-btn'
               },
	 		   confirmButtonText: yes_confirm_exit_live,
	 		   cancelButtonText: cancel_confirm,
	 	     closeOnConfirm: false,
	 	       },
	 	       function(isConfirm){

	 					 if (isConfirm) {
	 						 (function() {
	 				        window.location.href = URL_BASE;
	 				     })(); //<--- FUNCTION %
	 					 } // isConfirm
	 	        });
    });// Exit live

        //============= Comments
        $(document).on('keypress','#commentLive',function(e) {
            let element = $(this);
            if(empty(element.val())) return;
            if (e.which == 13) {
                e.preventDefault();
                element.blur();
                $('.blocked').show();

                 $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   type: "POST",
                   url: URL_BASE+"/comment/live",
                   dataType: 'json',
                   data: $("#formSendCommentLive").serialize(),
                   success: function(result){
                       element.val('');
                 }//<-- RESULT
               }).fail(function(jqXHR, ajaxOptions, thrownError)
                 {
                     $('.popout').removeClass('popout-success').addClass('popout-error').html(error_occurred).slideDown('500').delay('5000').slideUp('500');
                     $('.blocked').hide();
                 });//<--- AJAX

             }//e.which == 13
        });//<----- CLICK

        // Hide Top Menu y Chat
        $(document).on('click', '#full-screen-video', function(e) {

            let belowSizeLimit = $(window).width() <= 991;
            if (belowSizeLimit) {
                $('.liveContainerFullScreen').toggleClass('controls-hidden');

                if ($('.liveContainerFullScreen').hasClass('controls-hidden')) {
                    $(".live-top-menu").animate({"top": "-80px" }, "fast");
                    $("#live-chat-container").css("position", "relative").animate({"bottom": "-52vh" }, "slow")

                } else {
                    $(".live-top-menu" ).animate({"top": "0" }, "slow");
                    $("#live-chat-container").css("position", "fixed").animate({"bottom": "15px" }, "slow")
                }
            }

            $(window).on("resize", function () {
                let currentSize = $(window).width();
                if(belowSizeLimit && currentSize > 991 && $("#live-chat-container").css("position") === "relative") {
                    $("#live-chat-container").attr("style", "");
                    $(".live-top-menu" ).attr("style", "left: 15px; right: 15px; top: 0;");
                    belowSizeLimit = false;
                }
                else if(!belowSizeLimit && currentSize <= 991) belowSizeLimit = true;
            })
        });

        /*========= Like ==============*/
        $(document).on('click','.button-like-live',function(e) {
            var element     = $(this);
            var id          = $('.liveContainerFullScreen').attr("data-id");
            var data        = 'id=' + id;

            e.preventDefault();

            element.blur();

            if (! id) {
                return false;
            }

                 $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   type: 'POST',
                   url: URL_BASE+"/live/like",
                     dataType: 'json',
                   data: data,
                   success: function(result) {

                    if (result.success) {

                            if (result.likes !== 0) {
                                $('#counterLiveLikes').html(result.likes);
                            } else {
                                $('#counterLiveLikes').html('');
                            }

                            if (element.hasClass('active')) {
                                    element.removeClass('active');
                                    element.removeClass('bi-heart-fill').addClass('bi-heart');

                                    if (result.likes !== 0) {
                                        $('#counterLiveLikes').html(result.likes);
                                    } else {
                                        $('#counterLiveLikes').html('');
                                    }

                                } else {
                                    element.addClass('active');
                                    element.removeClass('bi-heart').addClass('bi-heart-fill');
                                }

                    } else {
                            window.location.reload();
                            element.removeClass('button-like-live');
                            element.removeClass('active');
                    }
                    }//<-- RESULT
               }).fail(function(jqXHR, ajaxOptions, thrownError)
                 {
                     $('.popout').removeClass('popout-success').addClass('popout-error').html(error_occurred).slideDown('500').delay('5000').slideUp('500');
                 });//<--- AJAX

        });//<----- LIKE



    $(document).on("click", ".live-options", function () {
        let id = $(this).attr("id");
        switch (id) {
            default: return;
            case "muteAudio": return toggleMuteAudio($(this));
            case "muteVideo": return toggleMuteVideo($(this));
        };
    })


})(jQuery);
