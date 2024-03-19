@extends('layouts.app')

@section('title') {{trans('auth.sign_up')}} -@endsection

<style>

    video{
        min-width: 100%;
        min-height: 100%;
        object-fit: fill;
    }

    .vertical-middle{
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }

</style>

@section('content')

    <div class="page-wrapper">

        <div class="row mnh-100vh mr-0">
            <div class="col-12 col-lg-6 xol-xl-5 mnh-100">
                <div class="flex-col-start flex-align-center vertical-middle p-4">
                    <div class="flex-col-start flex-align-center w-60">
                        <img src="{{asset('public/img/bm-logo.png')}}" class="noSelect w-250px" />
                    </div>

                    <form method="POST" id="formLoginRegister" class="mt-3 mxw-100 mnw-75">
                        @csrf

                        <style>
                            .month_col{
                                background:black;
                            }
                            .month_title{
                                color:white !important;
                            }
                            .month_subs{
                                color:white !important;
                            }.month_det{
                                color:lightgray !important;
                            }
                            .month_price{
                                /*background:black;*/
                                color:white !important;
                            }

                        </style>
                        <br>

                        <h3 style="font-weight:bold;">Choose plan</h3>
                        <div class="row month_col" style="border: 1px solid #ECE8E8;padding-top: 20px;padding-bottom: 10px;padding-left: 20px;border-radius: 2%;">
                            <div class="col-md-8 ">
                                <h4 class="month_title">Monthly</h4>
                                <p class="month_subs">Monthly Subscription</p>
                                <p class="month_det">Billed 69 euro every month</p>

                            </div>

                            <div class="col-md-4 " style="font-size: 70px;font-weight: bold;"><div class="month_price">69€</div></div>
                        </div>
                        <br>
                        <div class="row yearly_col" style="border: 1px solid #ECE8E8;padding-top: 20px;padding-bottom: 10px;padding-left: 20px;border-radius: 2%;">
                            <div class="col-md-8 ">
                                <h4 class="yearly_title">Yearly</h4>
                                <p class="yearly_subs">Monthly Subscription</p>
                                <p class="yearly_det">Billed 399 euro every year</p>

                            </div>

                            <div class="col-md-4" style="font-size: 70px;font-weight: bold;"><div class="yearly_price">399€</div></div>
                        </div>
                        <br>                        <br>                        <br>

                        <h3 style="font-weight:bold;">Payment Details</h3>
                        <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                            <input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
                                    </div>
                                    <div id="card-field"></div>
                                </div>
                            </div>
                            <button type="button" class="dark-btn w-100 mt-2 go-to-payment" id="go-to-payment">Pay <span id="price" data-price='69'> 69€</span></button>




                    </form>

                    <!--<a href="{{url("login")}}" class="mt-4 color-dark font-16 text-center">{{trans("auth.already_have_an_account")}} <span class="font-weight-bold">{{trans("auth.login")}}</span></a>-->

                    <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin">
                        <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"></ul>
                    </div>
                    @include('errors.errors-forms')

                </div>
            </div>


            <div class="d-none d-lg-flex col-lg-6 xol-xl-7 p-0 mnh-100 position-relative">
                <div id="max-size-image-container-plans">
  <!--                  <img src="{{asset('public/img/advert/creator_1.jpg')}}" style="max-width: 100%;-->
  <!--height: auto;" class="noSelect" />-->
                    <!--<video autoplay muted loop id="myVideo">-->
                    <!--    <source src="{{asset('public/videos/login-video.mp4')}}" type="video/mp4">-->
                    <!--    Your browser does not support HTML5 video.-->
                    <!--</video>-->
                </div>
            </div>
        </div>







    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

    $(".yearly_col").click(function(){


        $(".month_col").css('background-color','white');

        $(".month_title").attr('style', 'color:black !important');

        $(".month_subs").attr('style', 'color:black !important');

        $(".month_det").attr('style', 'color:lightgray !important');

        $(".month_price").attr('style', 'color:black !important');


        $(".yearly_col").css('background-color','black');

        $(".yearly_title").attr('style', 'color:white !important');

        $(".yearly_subs").attr('style', 'color:white !important');

        $(".yearly_det").attr('style', 'color:lightgray !important');

        $(".yearly_price").attr('style', 'color:white !important');



    })

    $(".month_col").click(function(){


        $(".yearly_col").css('background-color','white');

        $(".yearly_title").attr('style', 'color:black !important');

        $(".yearly_subs").attr('style', 'color:black !important');

        $(".yearly_det").attr('style', 'color:lightgray !important');

        $(".yearly_price").attr('style', 'color:black !important');


        $(".month_col").css('background-color','black');

        $(".month_title").attr('style', 'color:white !important');

        $(".month_subs").attr('style', 'color:white !important');

        $(".month_det").attr('style', 'color:lightgray !important');

        $(".month_price").attr('style', 'color:white !important');



    })

</script>
<script>




        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#token_item').val()
            }
        });

        $(document).ready(function(){
            $('#step1 .steps-navigation .step .bar .progress-line').addClass('fill');

            $('#logo').css("left", $("#formLoginRegister").position().left);






        });

	    $(document).on('click','#go-to-step2',function(s) {

 		 s.preventDefault();
		 sendFormLoginRegisterModified();

 		 });

        	function sendFormLoginRegisterModified()
        	{
        		var element = $(this);
        		$('#go-to-step2').attr({'disabled' : 'true'});
        		$('#go-to-step2').find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

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

                                $('#step1').hide();
                                $('#step2').show();

                                $('#errorLogin').hide();

                                $('#step2 .steps-navigation .step .bar .progress-line').addClass('fill');


        				 }  else {

        					 if (result.errors) {

        						 var error = '';
        						 var $key = '';

        					for ($key in result.errors) {
        							 error += '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' + result.errors[$key] + '</li>';
        						 }

        						 $('#showErrorsLogin').html(error);
        						 $('#errorLogin').fadeIn(500);
        						 $('#go-to-step2').removeAttr('disabled');
        						 $('#go-to-step2').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
        					 }
        				 }

        				},
        				error: function(responseText, statusText, xhr, $form) {
        						// error
        						$('#go-to-step2').removeAttr('disabled');
        						$('#go-to-step2').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
        						swal({
        								type: 'error',
        								title: error_oops,
        								text: error_occurred+' ('+xhr+')',
        							});
        				}
        			}).submit();
        		})(); //<--- FUNCTION %
        	}

        	$(document).on('click','#go-to-step3',function() {
        	    $.ajax({
        	        url: '{{url("course-invitation-step2")}}',
        	        type: 'post',
        	        data: {address: $('#address').val(), city: $('#city').val(), zip: $('#zip').val(), countries_id: $('#countries_id').val(), },
        	        success: function(result){

        	            if(result.success){
        	                $("#cardholder-name-PPV").val(result.name);
        	                $("#cardholder-email-PPV").val(result.email);

                            $('#cardholder_name').val(result.name);

        	                $.ajax({
                    	        url: '{{url("course-invitation-get-taxes")}}',
                    	        type: 'post',
                    	        data: {},
                    	        success: function(result){
                    	            $("#price").html(calculateTotalAmountAfterTax(result.taxes));

                    	            $('#step2').hide();
                                    $('#step3').show();
                                    $('#step3 .steps-navigation .step .bar .progress-line').addClass('fill');

                                    $("#formSendPPV").ajaxForm({
                                    dataType : 'json',
                                    success:  function(result) {
                                        if(result.success){
                                            //window.location.replace(result.url)
                                            RevolutCheckout(result.public_id, result.mode).then(function (RC) {

                                                var card = RC.createCardField({
                                                    target: document.getElementById('card-field'),
                                                    name: result.name,  // (mandatory!) name of the cardholder
                                                    onSuccess() {  // Callback called when payment finished successfully
                                                    ///window.alert("Thank you!");
                                                    location.href = "{{ route ('home') }}";
                                                    },
                                                    onError(message) {  // Callback in case some error happened
                                                    window.alert("Payment failed! Reason: "+message);
                                                    },
                                                    onCancel() {  // (optional) Callback in case user cancelled a transaction
                                                    //window.alert("Payment cancelled!");
                                                    },
                                                });
                                                $("#card-field").addClass('form-group');
                                                $("#card-field").find('iframe input').css('height','25px');

                                                document.getElementById("go-to-payment").addEventListener("click", function () {
                                                    card.submit({
                                                        name: $('#cardholder_name').val()
                                                    });
                                                });

                                                var paymentRequest = RC.paymentRequest({
                                                    target: document.getElementById('payment-request'),
                                                    name: result.name,  // (mandatory!) name of the cardholder
                                                    onSuccess() {  // Callback called when payment finished successfully
                                                    ///window.alert("Thank you!");
                                                    location.href = "{{ route ('home') }}";
                                                    },
                                                    onError(message) {  // Callback in case some error happened
                                                    window.alert("Payment failed! Reason: "+message);
                                                    },
                                                    onCancel() {  // (optional) Callback in case user cancelled a transaction
                                                    //window.alert("Payment cancelled!");
                                                    },
                                                    buttonStyle: { size: 'small' }
                                                });

                                                paymentRequest.canMakePayment().then((method) => {
                                                    if (method) {
                                                    paymentRequest.render()
                                                    } else {
                                                    setResult('Not supported')
                                                    paymentRequest.destroy()
                                                    }
                                                })

                                            });

                                                }
                                            }
                                        }).submit();

                    	        }
        	                });

        	            }
        	        }
        	    })
        	})

        	$(document).on('click','.go-to-payment',function() {

                $.ajax({
        	        url: '{{ route("update_transaction") }}',
        	        type: 'post',
        	        data: {"user_id" : "328",
        	               'price' :"20",
        	            "_token": "{{ csrf_token() }}",


        	        },
        	        headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },

        	        success: function(result){

        	           if(result){
        	               window.location.replace("https://app.eduvo.io");
        	           }
        	        }
                });
        	});

        	$(document).on('click', '#step3 .payment-option', function(){
        	    $(this).find('input[type=radio]').prop("checked", true);
        	    $('#step3 .payment-option').removeClass('selected');
        	    $(this).addClass('selected');
        	});


    	function onlyNumberKey(evt) {

            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        function calculateTotalAmountAfterTax(taxes){
            let price = total = $("#price").data("price");
            for(i=0; i<taxes.length; i++){
                tax_percantage = taxes[i].percentage;
                tax_price = (tax_percantage*price)/100;
                total = parseFloat(total) + parseFloat(tax_price);
            }
            total = Number(total).toFixed(2);
            return total;
        }


    </script>

@endsection
