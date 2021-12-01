@extends('backend.layouts.app')

@section('title','User')

@section('user-active','mm-active')

@section('content')
<div class="app-page-title" style="padding: 10px 30px; margin:-30px -30px 0px;">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-users icon-gradient bg-mean-fruit">
                </i>
            </div>
            <div>Create User</div>
        </div>
    </div>
</div>



<div class="content pt-3">
    <div class="container">
        <div class="card">
            <div class="card-body">

                @include('backend.layouts.flash')

                <form action="{{ route('admin.user.store') }}" method="POST" id="create">
                    @csrf
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Phone</label>
                        <input type="number" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button class="btn btn-secondary back-btn">Cancle</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
 {!! JsValidator::formRequest('App\Http\Requests\StoreAdminUser', '#create') !!}
 <script>
     $(document).ready(function() {

    } );
 </script>
@endsection
