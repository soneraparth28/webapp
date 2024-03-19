@extends('layouts.app')

@section('title') {{trans('Community')}} -@endsection

@section('content')

<?php $pageTitle = "Community"; ?>

<div class="session-main-wrapper">
    @include("includes.menus.main-creator-menu")
    <div class="session-main-page-wrapper">
        @include("includes.menus.main-creator-navbar")
        <div class="session-main-content-wrapper">
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="row">
                        <div class="col-12 mt-3 mt-lg-0">
                            <div class="flex-row-start flex-align-center">
                                <p class="font-22 font-weight-bold mb-0">Add Channel</p>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    @include('errors.errors-forms')
                                    <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('my/channel/store')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">{{trans('general.channel_name')}} *</label>
                                            <input type="text" id="channel" name="channel" class="form-control" placeholder="Channel Name" autocomplete="off" style="color: #000!important;">
                                        </div>
                                        <button type="submit" class="dark-btn w-10 mt-2">{{trans('general.save_changes')}}</button>
                                    </form>
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