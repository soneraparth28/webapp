<form name="filterMessages" id="filterMessages" action="" method="get" class="mb-2 position-relative">
    <input id="messageFilterInput" class="form-control" type="text" required name="q" autocomplete="off" minlength="3" placeholder="{{ trans('general.search') }}">
    <button class="btn btn-outline-success my-sm-0 button-search e-none color-light-gray"><i class="bi bi-search"></i></button>

    <div class="mt-1 dropdown">
        <a href="javascript:;" class="font-12 color-light-gray dropdown-toggle cursor-pointer" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false" id="sortMessagesBy">
            Sort by: <span class="color-dark">Newest</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="sortMessagesBy">
            <a class="dropdown-item active" href="javascript:;">Newest</a>
            <a class="dropdown-item" href="javascript:;">Oldest</a>
        </div>
    </div>
</form>
@include('includes.messages-inbox')
