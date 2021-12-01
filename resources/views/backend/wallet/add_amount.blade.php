@extends('backend.layouts.app')

@section('title','Wallet')

@section('wallet-active','mm-active')

@section('content')
<div class="app-page-title" style="padding: 10px 30px; margin:-30px -30px 0px;">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-wallet icon-gradient bg-mean-fruit">
                </i>
            </div>
            <div>Add Amount</div>
        </div>
    </div>
</div>



<div class="content py-3">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="">User</label>
                        <select name="" id="" class="form-control user_id">
                            <option value="">--- Please Choose ---</option>
                            @foreach($user as $user)
                            <option value="{{$user->id}}">{{$user->name}} ({{$user->phone}})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Amount</label>
                        <input type="number" name="amount" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea name="description"  class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button class="btn btn-secondary back-btn">Cancle</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.user_id').select2({
            placeholder: "--- Please Choose ---",
            allowClear: true,
            theme: 'bootstrap4',
        });

    });
</script>
@endsection
