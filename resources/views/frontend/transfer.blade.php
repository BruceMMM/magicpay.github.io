@extends('frontend.layouts.app')

@section('title','Transfer')

@section('content')
<div class="transfer">
    <div class="card">
        <div class="card-body">
            <form action="{{route('transfer/confirm')}}"  method="GET" autocomplete="off" id="transfer-form">

                <input type="hidden" name="hash_value" class="hash_value" value="">

                <div class="form-group">
                    <label for="">From</label>
                    <p class="mb-1 text-muted">{{Auth::User()->name}}</p>
                    <p class="text-muted">{{Auth::User()->phone}}</p>
                </div>
                <div class="form-group">
                    <label for="">To <span class="text-success to_account_info"></span></label>
                    <div class="input-group">
                        <input type="number" name="to_phone" class="form-control to_phone @error('to_phone') is-invalid @enderror" value="{{old('to_phone')}}" placeholder="Enter Phone Number" >
                        <span class="input-group-text btn verify-btn" id="basic-addon2"><i class="fas fa-check-circle"></i></span>
                    </div>
                    @error('to_phone')
                        <span class="text-danger mb-0"><small>{{ $message }}</small></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">Account (MMK)</label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}" placeholder="Enter Phone amount">
                    @error('amount')
                        <span class="text-danger mb-0"><small class=" mb-0">{{ $message }}</small></span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control" placeholder="Message...">{{old('description')}}</textarea>
                </div>

                <button type="submit" class="btn btn-block  btn-theme mt-4 submit-btn">Continue</button>
            </form>
        </div>
    </div>
</div>

@endsection


@section('script')
<script>
    $(document).ready(function(){
        $('.verify-btn').on('click', function(){
            var phone =$('.to_phone').val();
            $.ajax({
                url : '/to-account-verify?phone=' + phone,
                type : 'GET',
                success: function(res){
                    if(res.status == 'success'){
                        $('.to_account_info').text('('+res.data['name']+')');
                    }else{
                        $('.to_account_info').text('('+res.message+')')
                    }
                }
            });
        });

        $('.submit-btn').on('click',function(e){
            e.preventDefault();

            var to_phone = $('.to_phone').val();
            var amount = $('.amount').val();
            var description = $('.description').val();
            $.ajax({
                url : `/transfer-hash?to_phone=${to_phone}&amount=${amount}&description=${description}`,
                type : 'GET',
                success: function(res){
                    if(res.status == 'success'){
                        $('.hash_value').val(res.data);
                        $('#transfer-form').submit();
                    }
                }
            });
        })
    });
</script>
@endsection
