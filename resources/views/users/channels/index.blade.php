@extends('layouts.app')

@section('title') Community @endsection

@section('content')

    <?php $pageTitle = "Community"; ?>

    <style>
        .action-icons > div a{
            background-color: #f3f3f3;
            padding: 10px 14px;
            border-radius: 5px;
        }
        
        .action-icons > div a img{
            width: 20px;
        }
        
        .custom-url-field{
            background-color: #efefef;
            border: 0px !important;
            padding-right: 95px !important;
        }
        
        .custom-url-copy-button{
            position: absolute;
            right: 9px;
            top: 7px;
            background-color: white;
            height: 30px;
            border-radius: 5px;
            box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
            -webkit-box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
            -moz-box-shadow: 1px 1px 3px -1px rgba(0,0,0,0.75);
        }
        
        .no-courses{
            width: 100%;
            margin-top: 36vh;
        }
        
    </style>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")
            <div class="session-main-content-wrapper">
                <div class="row">
                    
                    @if(count($channels) > 0)
                        <div class="col-6 mt-3 text-left">
                            <p class="font-25 font-weight-bold">Channels</p>
                        </div>
                        <div class="col-6 mt-3 text-right">
                            <a href="{{url('my/channel/add')}}">
                                <button class="dark-btn">Create Channel</button>
                            </a>
                        </div>
                        @foreach($channels as $channel)
                            <div class="col-12 mt-3">
                                <div class="card border-radius-5px">
                                    <div class="card-body">
                                        <div class="flex-row-between flex-align-center flex-wrap">
                                            <div class="flex-row-start flex-align-center mr-5">
                                                <div class="flex-col-start">
                                                    <p class="mb-0 font-18 font-weight-bold">{{$channel->channel}}</p>
                                                </div>
                                            </div>
                                            <div class="flex-row-end flex-align-center action-icons">                                                
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center mr-3">
                                                        <a class="mb-0 font-16" href="{{url("my/channel/" . $channel->id . "/edit")}}">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="flex-col-start">
                                                    <div class="flex-row-start flex-align-center mr-3">
                                                        <a class="mb-0 font-16" href="{{url("my/channel/" . $channel->id . "/delete")}}">
                                                        <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach    
                    @else
                        <div class="no-courses">
                            <div class="col-12 text-center">
                                <h5 class="text-muted">You have no Channel created</h5>
                            </div>
                            <div class="col-12 mt-3 text-center">
                                <a href="{{url('my/channel/add')}}">
                                    <button class="dark-btn">Create Channel</button>
                                </a>
                            </div>
                        </div>

                    @endif
                </div>

            </div>



        </div>
    </div>

@endsection
@section('javascript')
    <script>
        function copyCourseInvitationLink(id){
            var copyText = document.getElementById("course-invitation-link-"+id);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value);
        }
    </script>
@endsection