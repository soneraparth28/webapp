@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('public/plugins/datepicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/plugins/select2/select2.min.css') }}?v={{$settings->version}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: var(--theme-dark-card) !important;
        }
    </style>

    <?php $pageTitle = "Addons"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")



        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row">
                    <div class="col-lg-4 col-xl-3 d-none d-lg-flex">
                        @include("includes.cards-settings")
                    </div>


                    <div class="col-12 col-lg-8 col-xl-9">
                        <div class="row">
                            <div class="col-12 d-lg-none">
                                @include("includes.card-settings-mobile")
                            </div>
                            <div class="col-12 mt-3 mt-lg-0">
                                <div class="flex-row-start flex-align-center">
                                    
                                    <i class="bi bi-gift font-22" style="padding-left:10px;"></i>
                                    <p class="font-22 font-weight-bold mb-0 ml-3">App</p>
                                </div>
                                <p class="mb-0 color-gray font-18">&nbsp;&nbsp;&nbsp;Create your own app and publish it on app store within 7 days.</p>
                                
                                 <div class="row mnh-100vh mr-0">
                                    <div class="col-12 col-lg-12 xol-xl-5 mnh-100">
                                        <div class=" flex-align-center vertical-middle p-4">
                        
                                            <form class="mt-3 mxw-100 mnw-75">
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
                                                        <p class="month_det">Billed 1 euro every month</p>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4 " style="font-size: 70px;font-weight: bold;"><div class="month_price" price="1">1€</div></div>
                                                </div>
                                                <br>
                                                <div class="row yearly_col" style="border: 1px solid #ECE8E8;padding-top: 20px;padding-bottom: 10px;padding-left: 20px;border-radius: 2%;">
                                                    <div class="col-md-8 ">
                                                        <h4 class="yearly_title">Yearly</h4>
                                                        <p class="yearly_subs">Monthly Subscription</p>
                                                        <p class="yearly_det">Billed 10 euro every year</p>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4" style="font-size: 70px;font-weight: bold;"><div class="yearly_price" price="10">10€</div></div>
                                                </div>
                                                <br>                        <br>                        <br>
                                            
                                                
                                                <h4 class="mb-3">Payment Details</h4>
                                            
                                                {{--<div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group payment-option">
                                                            <input type="radio" name="pay-time" class="theme"  aria-required="true" required>
                                                            <label class="font-16 font-weight-bold mb-0"><i class="fa fa-credit-card-alt"></i> Pay now</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group payment-option">
                                                            <input type="radio" name="pay-time"  class="theme"  aria-required="true" required>
                                                            <label class="font-16 font-weight-bold mb-0"><i class="fa fa-credit-card-alt"></i> Pay later</label>
                                                        </div>
                                                    </div>
                                                </div>--}}
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                                <input id="cardholder_name"  name="cardholder_name" class="theme form-control" tabindex="1" aria-required="true" required type="text" placeholder="Cardholder Name">
                                                        </div>
                                                        <div id="card-field"></div>   
                                                    </div>
                                                </div>
                                                 <button type="button" class="dark-btn w-100 mt-2" id="go-to-payment">Pay €<span id="price" data-price='1'>1</span></button>
                        
                        
                                                
                                            </form>
                        
                        
                                            <form method="post" action="{{route('ppv_demo')}}" id="formSendPPV">
                            
                            <!--<input type="hidden" name="id" value="50000">-->
                        	<input type="hidden" id="amount" name="amount" value="">
                            <input type="hidden" id="flag" name="flag" value="app">
                         
                        	<input type="hidden" id="cardholder-name-PPV" value="">
                        	<input type="hidden" id="cardholder-email-PPV" value="">
                        	@csrf
                            
                        	<div class="text-center">
                                <div class="bg-dark color-light border-radius-5px display-none mb-0 my-3 py-2 px-3" id="errorPPV">
                                    <ul class="list-unstyled m-0" id="showErrorsPPV"></ul>
                                </div>
                                
                                <!--<div class="text-cenwter">-->
                                <!--    <button type="submit" id="ppvBtn" class="dark-btn px-3 py-2 mt-4 ppvBtn">Pay €20$ <small>EUR</small></button>-->
                                <!--</div>-->
                                
                        	</div>
                        
                        </form>
                                            <!--<a href="{{url("login")}}" class="mt-4 color-dark font-16 text-center">{{trans("auth.already_have_an_account")}} <span class="font-weight-bold">{{trans("auth.login")}}</span></a>-->
                        
                                            <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin">
                                                <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"></ul>
                                            </div>
                                            @include('errors.errors-forms')
                        
                                        </div>
                                    </div>
                        
                        
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

    
    <script>

    var monthprice = $(".month_price").attr("price");
    
    $("#amount").val(monthprice);
    
    $(".yearly_col").click(function(){
        
        var yearlyprice = $(".yearly_price").attr("price");
        
        $(".month_col").css('background-color','white');
        
        $(".month_title").attr('style', 'color:black !important');
        
        $(".month_subs").attr('style', 'color:black !important');
    
        $(".month_det").attr('style', 'color:lightgray !important');
        
        $(".month_price").attr('style', 'color:black !important');
        
        $("#amount").val(yearlyprice);
        
        $("#price").attr('data-price', yearlyprice);
        
        $("#price").text(yearlyprice);
        
        $(".yearly_col").css('background-color','black');
        
        $(".yearly_title").attr('style', 'color:white !important');
        
        $(".yearly_subs").attr('style', 'color:white !important');
    
        $(".yearly_det").attr('style', 'color:lightgray !important');
        
        $(".yearly_price").attr('style', 'color:white !important');
        
        
        
    })
    
    $(".month_col").click(function(){
        
        var monthprice = $(".month_price").attr("price");
        
        $("#amount").val(monthprice);
        
        
        $(".yearly_col").css('background-color','white');
        
        $(".yearly_title").attr('style', 'color:black !important');
        
        $(".yearly_subs").attr('style', 'color:black !important');
    
        $(".yearly_det").attr('style', 'color:lightgray !important');
        
        $(".yearly_price").attr('style', 'color:black !important');
        
         $("#price").attr('data-price', monthprice);
         
         $("#price").text(monthprice);
        
        $(".month_col").css('background-color','black');
        
        $(".month_title").attr('style', 'color:white !important');
        
        $(".month_subs").attr('style', 'color:white !important');
    
        $(".month_det").attr('style', 'color:lightgray !important');
        
        $(".month_price").attr('style', 'color:white !important');
        
        
        
    })
    
     $("#formSendPPV").ajaxForm({
        dataType : 'json',
        success:  function(result) {
            console.log(result);
            if(result.success){
                // window.location.replace(result.url)
                RevolutCheckout(result.public_id, result.mode).then(function (RC) {
    
                    var card = RC.createCardField({
                        target: document.getElementById('card-field'),
                        name: result.name,  // (mandatory!) name of the cardholder
                        onSuccess() {  // Callback called when payment finished successfully
                        window.alert("Thank you!");
                        // location.href = "{{ route ('home') }}";
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
            
    
     $(document).on('click','#go-to-payment',function() {
        
        var price = $("#price").attr('data-price');
                    
         var flag = $("#flag").val();           
                    
                    $.ajax({
        	        url: '{{ route("update_plans") }}',
        	        type: 'post',
        	        data: {
        	            'flag' : flag,
        	               'price' : price,
        	        },
        	        headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },

        	        success: function(result){
        	           
        	         window.location.reload();
        	        }
                });
                
        
         
     });
    

</script>
@endsection