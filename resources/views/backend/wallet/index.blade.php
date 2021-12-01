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
            <div>Wallet</div>
        </div>
    </div>
</div>



<div class="content py-3">
    <div class="container">

        <div class="pb-3">
            <a href="{{url('admin/wallet/add/amount')}}" class="btn btn-success"><i class="fas fa-plus-circle"> Add Amount</i></a>
            <a href="{{url('admin/wallet/reduce/amount')}}" class="btn btn-danger"><i class="fas fa-minus-circle"> Reduce Amount</i></a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered datatable">
                    <thead class="bg-light">
                        <th>Account Number</th>
                        <th>Account Person</th>
                        <th>Amount(MMK)</th>
                        <th>Create Time</th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
 <script>
     $(document).ready(function() {
        var table =$('.datatable').DataTable( {
            processing: true,
            serverSide: true,
            ajax: "/admin/wallet/datatable/ssd",
            columns: [
                {
                    data: "account_number",
                    name: "account_number",
                },
                {
                    data: "account_person",
                    name: "account_person",
                },
                {
                    data: "amount",
                    name: "amount",
                },

                {
                    data: "created_at",
                    name: "created_at"
                },

            ],
            order: [[3,"desc"]]
        });


    } );
 </script>
@endsection


