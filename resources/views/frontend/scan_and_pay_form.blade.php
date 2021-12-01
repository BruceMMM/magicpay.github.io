@extends('frontend.layouts.app')

@section('title','Scan & Pay Form')

@section('content')
<div class="transfer">
    <div class="card">
        <div class="card-body">
            <form action="{{url('scan-and-pay/confirm')}}"  method="GET" autocomplete="off" id="transfer-form">

                <input type="hidden" name="hash_value" class="hash_value" value="">
                <input type="hidden" name="to_phone" class="tophone" value="{{$to_account->phone}}">

                <div class="form-group">
                    <label for="">From</label>
                    <p class="mb-1 text-muted">{{Auth::User()->name}}</p>
                    <p class="text-muted">{{Auth::User()->phone}}</p>
                </div>

                <div class="form-group">
                    <label for="">To</label>
                    <p class="mb-1 text-muted">{{$to_account->name}}</p>
                    <p class="text-muted">{{$to_account->phone}}</p>
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
