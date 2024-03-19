<script>
window.paceOptions = {
    ajax: false,
    restartOnRequestAfter: false,
};
</script>
<script src="{{ asset('public/js/core.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/js/jqueryTimeago_'.Lang::locale().'.js') }}"></script>
<script src="{{ asset('public/js/lazysizes.min.js') }}" async=""></script>
<script src="{{ asset('public/js/plyr/plyr.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/plyr/plyr.polyfilled.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/app-functions.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/install-app.js') }}?v={{$settings->version}}"></script>
@auth
  <script src="{{ asset('public/js/progress-loader.js') }}"></script>
  <script src="{{ asset('public/js/fileuploader/jquery.fileuploader.min.js') }}"></script>
  <script src="{{ asset('public/js/fileuploader/fileuploader-post.js') }}?v={{$settings->version}}"></script>
  <script src="{{ asset('public/js/fileuploader/fileuploader-courses.js') }}?v={{$settings->version}}"></script>
  <script src="{{asset("public/js/course-handler.js")}}?v=4234235"></script>
{{--  <script src="{{ asset('public/plugins/select2/select2.min.js') }}"></script>--}}
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="{{ asset('public/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
  @if (config('app.locale') != 'en')
      <script src="{{ asset('public/plugins/datepicker/locales/bootstrap-datepicker.'.config('app.locale').'.js') }}"></script>
  @endif

<script src="https://js.stripe.com/v3/"></script>
<script src='https://checkout.razorpay.com/v1/checkout.js'></script>
<script src='https://js.paystack.co/v1/inline.js'></script>
@if (request()->is('my/wallet'))
<script src="{{ asset('public/js/add-funds.js') }}?v={{$settings->version}}"></script>
@else
<script src="{{ asset('public/js/payment.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('public/js/payments-ppv.js') }}?v={{rand()}}"></script>
<script src="{{ asset('public/js/upload-avatar-cover.js')}}?v={{$settings->version}}" type="text/javascript"></script>
@endif
@endauth

@if ($settings->custom_js)
  <script type="text/javascript">
  {!! $settings->custom_js !!}
  </script>
@endif

<script type="text/javascript">
const lightbox = GLightbox({
    touchNavigation: true,
    loop: false,
    closeEffect: 'fade'
});

@if (auth()->check())
$('.btnMultipleUpload').on('click', function() {
  $('.fileuploader').toggleClass('d-block');
});

    // new update's category

    $(document).on('click', '#attach-category', function(){
        $('.category-selection').slideToggle();
    });

    $(document).on('click', '.show-add-new-update-category-section', function(){
        $(this).hide();
        $('.add-new-category-section').show();
    });

    $(document).on('click', '.add-new-category', function(){

        const name = $('.new-category-name').val();
        if(name.length){
            $.ajax({
                url: "{{url('updatecategory/create')}}",
                type: 'post',
                data: {'name': name, '_token': "{{ csrf_token() }}"},
                success: function(res){
                    if(res.length){
                        res = JSON.parse(res);
                        let html = '<option value="'+res.id+'">'+res.name+'</option>';
                        $('.update-category-list').append(html);
                        $('.update-category-list').val(res.id);

                        $('.show-add-new-update-category-section').show();
                        $('.add-new-category-section').hide();

                        $('.new-category-name').val('');

                    }
                }
            })
        }


    });

    $(document).on('change', '#selected-category', function(){
    	localStorage.setItem("update_selected_category", $(this).val());
    	showPostByCategory();
    });

    // if page is profile page then check of category id from browser localstorage for selected category
    if (typeof(Storage) !== "undefined") {
    	let old_stored_category_id = localStorage.getItem("update_selected_category");
		if(old_stored_category_id != ''){
			$('#selected-category').val(old_stored_category_id);
			showPostByCategory();
		}
	}

    function showPostByCategory(){

		$this= $('#selected-category');
		let cat_id = $this.val();

		if(cat_id.length){
			$('.wrap-post .card.card-updates').hide();
			$('.wrap-post .card.card-updates[data-category-id='+cat_id+']').show();
		}
		else{
			$('.wrap-post .card.card-updates').show();
		}

    }

@endif
</script>

@if (auth()->guest()
    && ! request()->is('password/reset')
    && ! request()->is('password/reset/*')
    && ! request()->is('contact')
    )
<script type="text/javascript">

	//<---------------- Login Register ----------->>>>

	_submitEvent = function() {
		  sendFormLoginRegister();
		};

	if (captcha == false) {

	    $(document).on('click','#btnLoginRegister',function(s) {

 		 s.preventDefault();
		 sendFormLoginRegister();

 		 });//<<<-------- * END FUNCTION CLICK * ---->>>>
	}

	function sendFormLoginRegister()
	{
		var element = $(this);
		$('#btnLoginRegister').attr({'disabled' : 'true'});
		$('#btnLoginRegister').find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

		(function(){
			 $("#formLoginRegister").ajaxForm({
			 dataType : 'json',
			 success:  function(result) {

         if (result.actionRequired) {
           $('#modal2fa').modal({
    				    backdrop: 'static',
    				    keyboard: false,
    						show: true
    				});

            $('#loginFormModal').modal('hide');
           return false;
         }

				 // Success
				 if (result.success) {

           if (result.isModal && result.isLoginRegister) {
             window.location.reload();
           }

					 if (result.url_return && ! result.isModal) {
					 	window.location.href = result.url_return;
					 }

					 if (result.check_account) {
					 	$('#checkAccount').html(result.check_account).fadeIn(500);

						$('#btnLoginRegister').removeAttr('disabled');
						$('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						$('#errorLogin').fadeOut(100);
						$("#formLoginRegister").reset();
					 }

				 }  else {

					 if (result.errors) {

						 var error = '';
						 var $key = '';

					for ($key in result.errors) {
							 error += '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' + result.errors[$key] + '</li>';
						 }

						 $('#showErrorsLogin').html(error);
						 $('#errorLogin').fadeIn(500);
						 $('#btnLoginRegister').removeAttr('disabled');
						 $('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
					 }
				 }

				},
				error: function(responseText, statusText, xhr, $form) {
						// error
						$('#btnLoginRegister').removeAttr('disabled');
						$('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						swal({
								type: 'error',
								title: error_oops,
								text: error_occurred+' ('+xhr+')',
							});
				}
			}).submit();
		})(); //<--- FUNCTION %
	}// End function sendFormLoginRegister


</script>
@endif
