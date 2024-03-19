@extends('layouts.app')

@section('title')
    {{ trans('auth.sign_up') }} -
@endsection

@section('content')
    <style>
        /*#max-size-image-container-video{*/
        /*        position: absolute;*/
        /*        top: 0;*/
        /*        right: 0;*/
        /*        bottom: 0;*/
        /*        left: 0;*/
        /*        overflow: hidden;*/
        /*        padding: 0;*/
        /*}*/

        #logo {
            position: absolute;
            top: 15px;
        }

        .text-box {
            position: absolute;
            top: calc(100vh - 190px);
            left: 50px;
        }

        .text-box p {
            color: white !important;
            margin: 0px;Billed 399 euro every year


            font-size: 13px;
            width: 90%;
        }

        .text-box p.heading {
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .vertical-middle {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .steps-navigation {
            display: flex;
            width: 250px;
            justify-content: center;
            font-size: 12px;
            margin: auto;
            margin-top: 100px;
        }

        .steps-navigation .step p {
            margin-bottom: 2px;
        }

        .steps-navigation .step {
            width: 80px;
        }

        .steps-navigation .step .bar {
            height: 4px;
            background-color: #dbdbdb;
            border-radius: 5px;
        }

        .steps-navigation .step.filled .bar {
            background-color: black;
        }

        .steps-navigation .step .bar .progress-line {
            height: 4px;
            background-color: #000000;
            border-radius: 5px;
            width: 0px;
            transition: 0.5s linear all;
        }

        .steps-navigation .step .bar .progress-line.fill {
            width: 100%;
        }

        #step3 .form-group {
            border: 1px solid #f3f3f3;
            border-radius: 5px;
            padding: 14px 14px;
        }

        #step3 .form-group label {
            font-size: 14px !important;
            margin-left: 5px;
        }

        #step3 .form-group label i {
            font-size: 20px;
            vertical-align: bottom;
            margin-right: 5px;
        }

        #step3 .payment-option {}

        #step3 .payment-option.selected {}
    </style>

    <div class="page-wrapper">

        <div class="row mnh-100vh mr-0">
            <div class="col-12 col-lg-6 xol-xl-6 mnh-100">
                <div class="flex-col-start flex-align-center vertical-middle p-4">
                    <div class="flex-col-start flex-align-center w-60">
                        <img src="{{ asset('public/img/bm-logo.png') }}" class="noSelect w-250px" />
                        <p class="font-25 font-weight-bold mt-2 mb-0">Create A Creator Account</p>
                        <p class="font-20">Let’s get started</p>
                    </div>

                    <form method="POST" action="" id="formLoginRegister" class="mt-3 mxw-100 mnw-75">
                        @csrf
                        <input type="hidden" name="return"
                            value="{{ count($errors) > 0 ? old('return') : url()->previous() }}">

                        @if ($settings->captcha == 'on')
                            @captcha
                        @endif

                        <style>
                            .month_col {
                                background: black;
                            }

                            .month_title {
                                color: white !important;
                            }

                            .month_subs {
                                color: white !important;
                            }

                            .month_det {
                                color: lightgray !important;
                            }

                            .month_price {
                                /*background:black;*/
                                color: white !important;
                            }
                        </style>


                        <div id="step1">

                            <h4 class="mb-3">User Details</h4>

                            <div class="form-group">
                                <label class="font-16 font-weight-bold mb-0">{{ trans('auth.full_name') }}</label>
                                <div class="input-group input-theme mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input id="name" value="{{ old('name') }}" name="name"
                                        class="theme form-control" tabindex="1" aria-required="true" required
                                        type="text" placeholder="Enter here">
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="font-16 font-weight-bold mb-0">{{ trans('auth.email') }}</label>
                                <div class="input-group input-theme mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input id="email" name="email" class="theme form-control" tabindex="2"
                                        aria-required="true" required type="text" value="{{ old('email') }}"
                                        placeholder="Enter here">


                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-16 font-weight-bold mb-0">{{ trans('auth.password') }}</label>
                                <div class="input-group input-theme mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input id="pass" name="password" class="theme form-control" style="border-top-right-radius: 0px !important; border-bottom-right-radius: 0px !important" tabindex="3"
                                        value="" aria-required="true" type="password"
                                        placeholder="Enter password here" required="">
                                        <div class="input-group-append toggle-password">
                                            <span class="c-pointer input-group-text"><i class="feather icon-eye"></i></span>
                                        </div>
                                </div>
                            </div>


                            <div class="flex-row-between flex-align-center flex-wrap">
                                <div class="flex-row-start flex-align-center mt-1">
                                    <input type="checkbox" name="agree_gdpr" class="square-20" style="border: 2px"
                                        id="customCheckRegister" />
                                    <span class="mb-0 ml-2 font-16">
                                        {{ trans('admin.i_agree_gdpr') }}
                                        <a href="https://mator.io/privacy-policy/" target="_blank" class="font-14">
                                            {{ trans('admin.privacy_policy') }}</a>
                                    </span>
                                </div>
                            </div>


                            <button type="submit" class="dark-btn w-100 mt-2" id="go-to-step2"><i></i>Next</button>
                            <div class="alert alert-success mt-3 py-2 px-3 border-radius-5px display-none"
                                id="checkAccount"></div>

                            @if ($settings->facebook_login == 'on' || $settings->google_login == 'on' || $settings->apple_login == 'on')
                                <div class="flex-col-start flex-align-center mt-3">
                                    <p class="text-center text-uppercase mb-0 font-16">Or</p>

                                    <div class="flex-col-start flex-align-center mt-3 w-100">
                                        @if ($settings->google_login == 'on')
                                            <a href="{{ url('oauth/google') }}" class="light-btn w-100"
                                                style="border-radius: 10px !important;">
                                                <i class="fa-brands fa-google mr-2"></i>
                                                Login with Google
                                            </a>
                                        @endif

                                        @if ($settings->facebook_login == 'on')
                                            <a href="{{ url('oauth/facebook') }}" class="light-btn w-100 mt-3"
                                                style="border-radius: 10px !important;">
                                                <i class="fa-brands fa-facebook mr-2"></i>
                                                Login with Facebook
                                            </a>
                                        @endif

                                        @if ($settings->apple_login == 'on')
                                            <a href="{{ url('oauth/apple') }}" class="light-btn w-100 mt-3"
                                                style="border-radius: 10px !important;">
                                                <i class="fa-brands fa-apple mr-2"></i>
                                                Login with Apple
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <br><br>
                            <a href="{{ url('login') }}" class="mt-4 color-dark font-16 text-center"
                                style="margin-left:30%;margin-right:20%;">{{ trans('auth.already_have_an_account') }} <span
                                    class="font-weight-bold">{{ trans('auth.login') }}</span></a>


                            {{-- <div class="steps-navigation">
                                <div class="step step1">
                                    <p class="title">Account</p>
                                    <div class="bar">
                                        <div class="progress-line"></div>
                                    </div>
                                </div>
                                <div class="step step2 ">99€
                                    <p class="title">Payment</p>
                                    <div class="bar"></div>
                                </div>

                            </div> --}}

                        </div>

                        <div id="step2" style="display: none">

                            <h3 style="font-weight:bold;">Choose plan</h3>
                            <div class="row month_col"
                                style="border: 1px solid #ECE8E8;padding-top: 20px;padding-bottom: 10px;padding-left: 20px;border-radius: 2%;">
                                <div class="col-md-8 ">
                                    <h4 class="month_title">Monthly</h4>
                                    <p class="month_subs">Monthly Subscription</p>
                                    <p class="month_det">Billed 69 euro every month</p>

                                </div>

                                <div class="col-md-4 " style="font-size: 50px;font-weight: bold;">
                                    <div class="month_price" price="69">69€</div>
                                </div>
                            </div>
                            <br>
                            <div class="row yearly_col"
                                style="border: 1px solid #ECE8E8;padding-top: 20px;padding-bottom: 10px;padding-left: 20px;border-radius: 2%;">
                                <div class="col-md-8 ">
                                    <h4 class="yearly_title">Yearly</h4>
                                    <p class="yearly_subs">Yearly Subscription</p>
                                    <p class="yearly_det">Billed 399 euro every year</p>

                                </div>

                                <div class="col-md-4" style="font-size: 50px;font-weight: bold;">
                                    <div class="yearly_price" price="399">399€</div>
                                </div>
                            </div>
                            <br> <br> <br>


                            <h4 class="mb-3">Payment Details</h4>

                            {{-- <div class="row">
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
                            </div> --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <input id="cardholder_name" name="cardholder_name" class="theme form-control"
                                            tabindex="1" aria-required="true" required type="text"
                                            placeholder="Cardholder Name">
                                    </div>
                                    <div id="card-field"></div>
                                </div>
                            </div>
                            <button type="button" class="dark-btn w-100 mt-2" id="go-to-payment">Pay Now <span
                                    id="price" data-price='69'> 69€ </span></button>

                            <div id="payment-request" class="mt-3"></div>
                            {{-- <div class="steps-navigation">
                                <div class="step step1 filled">
                                    <p class="title">Account</p>
                                    <div class="bar"></div>
                                </div>
                                <div class="step step2 filled">
                                    <p class="title">Payment</p>
                                    <div class="bar">
                                        <div class="progress-line"></div>
                                    </div>
                                </div>

                            </div> --}}

                        </div>

                        <div id="step3">



                        </div>

                    </form>

                    <div style="display: none">
                        <form method="post" action="{{ route('buy_throgh_revolut') }}" id="formSendPPV">

                            <!--<input type="hidden" name="id" value="50000">-->
                            <input type="hidden" id="amount" name="amount" value="">


                            <input type="hidden" id="cardholder-name-PPV" value="">
                            <input type="hidden" id="cardholder-email-PPV" value="">
                            <input type="hidden" id="interval" name="interval" value="monthly">
                            @csrf

                            <div class="text-center">
                                <div class="bg-dark color-light border-radius-5px display-none mb-0 my-3 py-2 px-3"
                                    id="errorPPV">
                                    <ul class="list-unstyled m-0" id="showErrorsPPV"></ul>
                                </div>

                                <div class="text-cenwter">
                                    <button type="submit" id="ppvBtn" class="dark-btn px-3 py-2 mt-4 ppvBtn">Pay €99$
                                        <small>EUR</small></button>
                                </div>

                            </div>

                        </form>
                    </div>

                    <div class="display-none mb-0 mt-3 py-2 px-3 border-radius-5px bg-dark color-light" id="errorLogin">
                        <ul class="list-unstyled m-0 color-light" id="showErrorsLogin"></ul>
                    </div>
                    @include('errors.errors-forms')

                </div>
            </div>


            <div class="d-none d-lg-flex col-lg-6 xol-xl-6 p-0 mnh-100 position-relative">
                <div id="max-size-image-container-video"
                    style="display: flex;justify-content: center;align-items: center;width: 100%;overflow: hidden;display: flex;">

                    <video autoplay muted loop id="myVideo"
                        style="width: 100%;height: 100%;object-fit: cover;object-fit: cover;">
                        <source src="{{ asset('public/videos/video1.mp4') }}" type="video/mp4">
                        Your browser does not support HTML5 video.
                    </video>

                </div>


            </div>
        </div>







    </div>
@endsection

@section('javascript')
    <script>
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        // Get values from URL
        var paidValue = getParameterByName('paid');
        var pacidValue = getParameterByName('pacid');
        console.log('pacidValue: ', pacidValue);

        // Set cookies
        document.cookie = 'paid=' + paidValue + '; expires=' + new Date(new Date().getTime() + 40 * 24 * 60 * 60 * 1000)
            .toUTCString() + '; path=/';
        document.cookie = 'pacid=' + pacidValue + '; expires=' + new Date(new Date().getTime() + 40 * 24 * 60 * 60 * 1000)
            .toUTCString() + '; path=/';

        function callToPartnerAds() {
            // Check if cookies exist
            if (document.cookie.indexOf('paid=') !== -1 && document.cookie.indexOf('pacid=') !== -1) {
                // Set other parameters
                var programId = 10720; // Your program ID
                var orderNumber = '12345'; // Replace with the actual order number
                var revenuePrSale = '799'; // Replace with the actual order total

                // Get values from cookies
                var paidValue = document.cookie.replace(/(?:(?:^|.*;\s*)paid\s*=\s*([^;]*).*$)|^.*$/, '$1');
                var pacidValue = document.cookie.replace(/(?:(?:^|.*;\s*)pacid\s*=\s*([^;]*).*$)|^.*$/, '$1');

                // Build URL
                // var partnerAdsUrl = `https://www.partner-ads.com/dk/leadtracks2s.php?programid=${programId}&type=lead&partnerid=${paidValue}&pacid=${pacidValue}&ordreid=${orderNumber}&varenummer=x&antal=1&omprsalg=${revenuePrSale}`;
                var partnerAdsUrl =
                    `https://www.partner-ads.com/dk/leadtracks2s.php?programid=${programId}&type=salg&partnerid=${paidValue}&pacid=${pacidValue}&ordreid=${orderNumber}&varenummer=x&antal=1&omprsalg=${revenuePrSale}`;
                console.log('partnerAdsUrl: ', partnerAdsUrl);

                // Make the call to Partner-Ads URL
                fetch(partnerAdsUrl, {
                    method: 'GET',
                    mode: 'no-cors' // Use 'no-cors' mode for cross-origin requests that don't need a response
                });

                // Optional: Clear cookies after the purchase
                document.cookie = 'paid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = 'pacid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }
        }
    </script>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>-->

    <script> (function(w, d, s, p, t) { w.gr = w.gr || function() { w.gr.q = w.gr.q || []; w.gr.q.push(arguments); }; p = d.getElementsByTagName(s)[0]; t = d.createElement(s); t.async = true; t.src = "https://app.getreditus.com/gr.js?_ce=60"; p.parentNode.insertBefore(t, p); })(window, document, "script"); gr("track", "pageview"); </script>


    <script>
        var monthprice = $(".month_price").attr("price");

    $("#amount").val(monthprice);

    $("body").on('click', '.toggle-password', function() {
        $('.toggle-password > .input-group-text > i').toggleClass("icon-eye-off");
        var input = $("#pass");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(".yearly_col").click(function(){

        var yearlyprice = $(".yearly_price").attr("price");

        $(".month_col").css('background-color','white');

        $(".month_title").attr('style', 'color:black !important');

        $(".month_subs").attr('style', 'color:black !important');

        $(".month_det").attr('style', 'color:lightgray !important');

        $(".month_price").attr('style', 'color:black !important');

        $("#amount").val(yearlyprice);

        $("#price").attr('data-price', yearlyprice);

        $("#price").text(yearlyprice+'€');

        $("#interval").val("yearly");

        $(".yearly_col").css('background-color','black');

        $(".yearly_title").attr('style', 'color:white !important');

        $(".yearly_subs").attr('style', 'color:white !important');

        $(".yearly_det").attr('style', 'color:lightgray !important');

        $(".yearly_price").attr('style', 'color:white !important');

        $("#formSendPPV").ajaxForm({
        dataType : 'json',
        success:  function(result) {
           // alert('here it is');
            console.log(result);
            if(result.success){
                // window.location.replace(result.url)
                public_id = result.public_id;
                mode = result.mode;
                customer_name = result.name;
                $("#payment-request").empty();
                RevolutCheckout(public_id, mode).then(function (RC) {

                    var card = RC.createCardField({
                        target: document.getElementById('card-field'),
                        name: customer_name,  // (mandatory!) name of the cardholder
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
                        redirectToPreviousUrlWithMessage('error', 'Your plan has been subscribed successfully.');
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

         $("#price").text(monthprice+'€');

         $("#interval").val("monthly");

        $(".month_col").css('background-color','black');

        $(".month_title").attr('style', 'color:white !important');

        $(".month_subs").attr('style', 'color:white !important');

        $(".month_det").attr('style', 'color:lightgray !important');

        $(".month_price").attr('style', 'color:white !important');

        $("#formSendPPV").ajaxForm({
        dataType : 'json',
        success:  function(result) {
           // alert('here it is');
            console.log(result);
            if(result.success){
                // window.location.replace(result.url)
                public_id = result.public_id;
                mode = result.mode;
                customer_name = result.name;
                $("#payment-request").empty();
                RevolutCheckout(public_id, mode).then(function (RC) {

                    var card = RC.createCardField({
                        target: document.getElementById('card-field'),
                        name: customer_name,  // (mandatory!) name of the cardholder
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
                        redirectToPreviousUrlWithMessage('error', 'Your plan has been subscribed successfully.');
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



    })
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#token_item').val()
            }
        });



        $(document).ready(function() {
            $('#step1 .steps-navigation .step .bar .progress-line').addClass('fill');

            $('#logo').css("left", $("#formLoginRegister").position().left);

        });

        $(document).on('click', '#go-to-step2', function(s) {

            s.preventDefault();
            sendFormLoginRegisterModified();

        });

        function sendFormLoginRegisterModified() {
            var element = $(this);
            $('#go-to-step2').attr({
                'disabled': 'true'
            });
            $('#go-to-step2').find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

            (function() {
                $("#formLoginRegister").ajaxForm({
                    dataType: 'json',
                    success: function(result) {

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

                            $("#cardholder-name-PPV").val();
                            $("#cardholder-email-PPV").val();

                            $('#cardholder_name').val();

                            $.ajax({
                                url: '{{ url('course-invitation-get-taxes') }}',
                                type: 'post',
                                data: {},
                                success: function(result) {
                                    $("#price").html(calculateTotalAmountAfterTax(result
                                        .taxes));

                                    $('#step2').show();
                                    // $('#step3').show();
                                    $('#step2 .steps-navigation .step .bar .progress-line')
                                        .addClass('fill');

                                   /* $("#formSendPPV").ajaxForm({
                                        dataType: 'json',
                                        success: function(result) {
                                            if (result.success) {
                                                // window.location.replace(result.url)
                                                RevolutCheckout(result.public_id,
                                                    result.mode).then(function(
                                                    RC) {

                                                    var card = RC
                                                        .createCardField({
                                                            target: document
                                                                .getElementById(
                                                                    'card-field'
                                                                ),
                                                            name: result
                                                                .name, // (mandatory!) name of the cardholder
                                                            onSuccess() { // Callback called when payment finished successfully
                                                                ///window.alert("Thank you!");
                                                                callToPartnerAds()
                                                                location
                                                                    .href =
                                                                    "{{ route('home') }}";
                                                            },
                                                            onError(
                                                                message
                                                            ) { // Callback in case some error happened
                                                                window
                                                                    .alert(
                                                                        "Payment failed! Reason: " +
                                                                        message
                                                                    );
                                                            },
                                                            onCancel() { // (optional) Callback in case user cancelled a transaction
                                                                //window.alert("Payment cancelled!");
                                                            },
                                                        });
                                                    $("#card-field")
                                                        .addClass(
                                                            'form-group');
                                                    $("#card-field").find(
                                                            'iframe input')
                                                        .css('height',
                                                            '25px');

                                                    document.getElementById(
                                                            "go-to-payment")
                                                        .addEventListener(
                                                            "click",
                                                            function() {
                                                                card.submit({
                                                                    name: $(
                                                                            '#cardholder_name'
                                                                            )
                                                                        .val()
                                                                });
                                                            });

                                                    var paymentRequest = RC
                                                        .paymentRequest({
                                                            target: document
                                                                .getElementById(
                                                                    'payment-request'
                                                                ),
                                                            name: result
                                                                .name, // (mandatory!) name of the cardholder
                                                            onSuccess() { // Callback called when payment finished successfully
                                                                ///window.alert("Thank you!");
                                                                callToPartnerAds()
                                                                location
                                                                    .href =
                                                                    "{{ route('home') }}";
                                                            },
                                                            onError(
                                                                message
                                                            ) { // Callback in case some error happened
                                                                window
                                                                    .alert(
                                                                        "Payment failed! Reason: " +
                                                                        message
                                                                    );
                                                            },
                                                            onCancel() { // (optional) Callback in case user cancelled a transaction
                                                                //window.alert("Payment cancelled!");
                                                            },
                                                            buttonStyle: {
                                                                size: 'small'
                                                            }
                                                        });

                                                    paymentRequest
                                                        .canMakePayment()
                                                        .then((method) => {
                                                            if (
                                                                method
                                                                ) {
                                                                paymentRequest
                                                                    .render()
                                                            } else {
                                                                setResult
                                                                    (
                                                                        'Not supported'
                                                                        )
                                                                paymentRequest
                                                                    .destroy()
                                                            }
                                                        })

                                                });

                                            }
                                        }
                                    }).submit();*/
                                     $("#formSendPPV").ajaxForm({
        dataType : 'json',
        success:  function(result) {
           // alert('here it is');
            console.log(result);
            if(result.success){
                // window.location.replace(result.url)
                public_id = result.public_id;
                mode = result.mode;
                customer_name = result.name;
                $("#payment-request").empty();
                RevolutCheckout(public_id, mode).then(function (RC) {

                    var card = RC.createCardField({
                        target: document.getElementById('card-field'),
                        name: customer_name,  // (mandatory!) name of the cardholder
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
                        redirectToPreviousUrlWithMessage('error', 'Your plan has been subscribed successfully.');
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


                        } else {

                            if (result.errors) {

                                var error = '';
                                var $key = '';

                                for ($key in result.errors) {
                                    error +=
                                        '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' +
                                        result.errors[$key] + '</li>';
                                }

                                $('#showErrorsLogin').html(error);
                                $('#errorLogin').fadeIn(500);
                                $('#go-to-step2').removeAttr('disabled');
                                $('#go-to-step2').find('i').removeClass(
                                    'spinner-border spinner-border-sm align-middle mr-1');
                            }
                        }

                    },
                    error: function(responseText, statusText, xhr, $form) {
                        // error
                        $('#go-to-step2').removeAttr('disabled');
                        $('#go-to-step2').find('i').removeClass(
                            'spinner-border spinner-border-sm align-middle mr-1');
                        swal({
                            type: 'error',
                            title: error_oops,
                            text: error_occurred + ' (' + xhr + ')',
                        });
                    }
                }).submit();
            })(); //<--- FUNCTION %
        }

        $(document).on('click', '#go-to-step3', function() {
            $.ajax({
                url: '{{ url('course-invitation-step2') }}',
                type: 'post',
                data: {
                    address: $('#address').val(),
                    city: $('#city').val(),
                    zip: $('#zip').val(),
                    countries_id: $('#countries_id').val(),
                },
                success: function(result) {

                    if (result.success) {


                    }
                }
            })
        })

        function redirectToPreviousUrlWithMessage(type,message) {
        const previousUrl = document.referrer || '/'; // Get the previous URL or use '/' as a fallback
        const redirectUrl = new URL(previousUrl);

        // Add the success message as a query parameter
        redirectUrl.searchParams.set(type, message);

        // Redirect to the modified URL
        window.location.href = redirectUrl.toString();
    }

    /*$(document).on('click','#go-to-payment',function() {

        console.log(public_id);
        RevolutCheckout(public_id, mode).then(function (instance) {
          instance.payWithPopup({
            savePaymentMethodFor: "merchant",
            onSuccess() {
              var price = $("#price").attr('data-price');
              var flag = $("#flag").val();

                 redirectToPreviousUrlWithMessage('error','Your plan has been subscribed successfully.');
            },
            onError(message) {
              window.alert("Oh no :(");
              redirectToPreviousUrlWithMessage('alert','Your payment proccess is failed.');
            },
          });
        });

    });
*/
        $(document).on('click', '#step3 .payment-option', function() {
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

        function calculateTotalAmountAfterTax(taxes) {
            let price = total = $("#price").data("price");
            for (i = 0; i < taxes.length; i++) {
                tax_percantage = taxes[i].percentage;
                tax_price = (tax_percantage * price) / 100;
                total = parseFloat(total) + parseFloat(tax_price);
            }
            total = Number(total).toFixed(2)+'€';
            return total;
        }
    </script>
@endsection
