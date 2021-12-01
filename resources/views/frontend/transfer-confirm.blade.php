@extends('frontend.layouts.app')

@section('title','Transfer Confirm')

@section('transfer-active','mm-active')

@section('content')
<div class="transfer">
    <div class="card">
        <div class="card-body">
            <form action="{{route('transfer/complete')}}"  method="POST" id="form">
                @csrf
                <input type="hidden"name="to_phone" value="{{$to_account->phone}}">
                <input type="hidden"name="amount" value="{{$amount}}">
                <input type="hidden"name="description" value="{{$description}}">

                <div class="form-group">
                    <label for="" class="mb-0"><strong>From</strong></label>
                    <p class="mb-1 text-muted">{{Auth::User()->name}}</p>
                    <p class="mb-1 text-muted">{{Auth::User()->phone}}</p>
                </div>
                <div class="form-group">
                    <label for="" class="mb-0"><strong>To</strong></label>
                    <p class="mb-1 text-muted">{{$to_account->name}}</p>
                    <p class="mb-1 text-muted">{{$to_account->phone}}</p>
                </div>
                <div class="form-group">
                    <label for="" class="mb-0"><strong>Account (MMK)</strong></label>
                    <p class="mb-1 text-muted">{{$amount}}</p>
                </div>
                <div class="form-group">
                    <label for="" class="mb-0"><strong>Description</strong></label>
                    <p class="mb-1 text-muted">{{$description}}</p>
                </div>

                <button type="submit" class="btn btn-block  btn-theme mt-5 confirm-btn">Comfirm</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('.confirm-btn').on('click', function(e){
            e.preventDefault();

            Swal.fire({
                title: 'Please fill your password!',
                icon: 'info',
                html:
                    '<input type="password" class="form-control text-center password"/>',
                showCancelButton: true,
                confirmButtonText:'Confirm',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
            }).then((result)=>{
                if (result.isConfirmed) {
                    var password = $('.password').val();

                    $.ajax({
                        url : '/password-check?password=' + password,
                        type : 'GET',
                        success: function(res){
                            if(res.status == 'success'){
                                $('#form').submit();
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: res.message,
                                });
                            }
                        }
                    });
                }
            });
        });
    })
</script>
@endsection
