@extends('frontend.layouts.app')

@section('title','Transaction')

@section('transaction-active','mm-active')

@section('content')
<div class="transaction">
    <div class="card mb-2">
        <div class="card-body p-2">
            <h6><i class="fas fa-filter"></i> Filter</h6>
           <div class="row">
               <div class="col-6">
                    <div class="input-group my-2">
                        <div class="input-group-prepend">
                            <label for="" class="input-group-text p-1">Date</label>
                        </div>
                        <input type="text" class="date form-control" value="{{request()->date }}" placeholder="All">
                    </div>
                </div>

               <div class="col-6">
                    <div class="input-group my-2">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Types</label>
                        </div>
                        <select class="custom-select type">
                            <option value="">All</option>
                            <option value="1" @if(request()->type == 1) selected @endif>Income</option>
                            <option value="2" @if(request()->type == 2) selected @endif>Expense</option>
                        </select>
                    </div>
               </div>
           </div>
        </div>
    </div>
    <hr>

    <h6>Transaction</h6>
    <div class="infinite-scroll">
        @foreach($transactions as $transaction)
        <a href="{{url('transaction/'.$transaction->trx_id)}}">
            <div class="card mb-2 ">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">{{ $transaction->trx_id }}</h6>
                        <p class="mb-1 @if($transaction->type == 1) text-success @elseif($transaction->type == 2) text-danger @endif">{{ $transaction->amount }}<small>MMK</small>
                        </p>
                    </div>

                    <p class="text-muted mb-1">
                        @if($transaction->type == 1)
                            From
                        @elseif($transaction->type == 2)
                            To
                        @endif
                        {{ $transaction->source ? $transaction->source->name:'' }}
                    </p>
                    <p class="text-muted mb-1">{{ $transaction->created_at }}</p>
                </div>
            </div>
        </a>

        @endforeach
        {{$transactions->links() }}
    </div>
</div>
@endsection

@section('script')

<script type="text/javascript">
    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });


        $('.date').daterangepicker({
            "singleDatePicker": true,
            "autoApply": false,
            "autoUpdateInput": false,
            "locale": {
               "format": "YYYY-MM-DD",
            },
        });

        $('.date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));

            var date = $('.date').val();
            var type = $('.type').val();

            history.pushState(null, '', `?date=${date}&type=${type}`);
            window.location.reload();
            });

        $('.date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');

            var date = $('.date').val();
            var type = $('.type').val();

            history.pushState(null, '', `?date=${date}&type=${type}`);
            window.location.reload();
        })

        $('.type').change(function(){
            var date = $('.date').val();
            var type = $('.type').val();

            history.pushState(null, '', `?date=${date}&type=${type}`);
            window.location.reload();
        });
    });
</script>

@endsection
