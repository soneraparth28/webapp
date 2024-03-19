@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection


@section('content')
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: var(--theme-dark-card) !important;
        }
    </style>

    <?php $pageTitle = "Messages"; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")



        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">

                <div class="row mt-2 ml-0">
{{--                    <div class="col-lg-4 col-xl-3 d-none d-lg-flex" id="messagesContainer">--}}
{{--                        @include("includes.cards-settings")--}}
{{--                    </div>--}}

                    <div class="@if(request()->is("messages")) col-12  @else d-none d-md-flex @endif col-md-4 p-4 p-md-0">
                        <div class="flex-col-start w-100 d-scrollbars" style="max-height: 90vh !important;">
                            @include('includes.sidebar-messages-inbox')
                        </div>
                    </div>


                    <div class="@if(request()->is("messages")) d-none d-md-flex @else col-12  @endif col-md-8 ">
                        <div class="w-100 bg-lightest-gray border border-med-light-gray container-msg content">
                            @if(!isset($messages))
                            <div class="flex-row-around p-4">
                                <button class="dark-btn" data-toggle="modal" data-target="#newMessageForm">
                                    <i class="bi bi-plus-lg mr-1"></i> {{trans('general.new_message')}}
                                </button>
                            </div>
                            @else
                                @include("includes.inbox-conversation")
                            @endif
                        </div>





                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.modal-new-message')
@endsection



@section('javascript')
    <script src="{{ asset('public/js/messages.js') }}?v={{$settings->version}}"></script>
    <script src="{{ asset('public/js/fileuploader/fileuploader-msg.js') }}?v={{$settings->version}}"></script>
    <script src="{{ asset('public/js/paginator-messages.js') }}"></script>
@endsection
