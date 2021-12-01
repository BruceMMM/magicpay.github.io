@extends('frontend.layouts.app')

@section('title','Magic Pay')

@section('home-active','mm-active')

@section('content')
<div class="home">
    <div class="row">
        <div class="col-12">
            <div class="profile mb-3">
                <img src="https://ui-avatars.com/api/?background=008888&color=fff&name={{Auth::user()->name}}" alt="">
                <h6>{{$user->name}}</h6>
                <p class="text-muted">{{ $user->wallet ? number_format($user->wallet->amount,2) : 0 }} MMK</p>
            </div>
        </div>
        <div class="col-6">
            <a href="{{url('scan-and-pay')}}">
                <div class="card shortcut-box mb-3">
                    <div class="card-body p-3">
                        <img src="{{asset('img/qr-code-scan.png')}}" alt="">
                        <span>Scan & Pay</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
           <a href="{{url('receive-qr')}}">
            <div class="card shortcut-box  mb-3">
                <div class="card-body p-3">
                    <img src="{{asset('img/qr.png')}}" alt="">
                    <span>Recive QR</span>
                </div>
            </div>
           </a>
        </div>
        <div class="col-12">
            <div class="card mb-3 function-box">
                <div class="card-body">
                    <a href="{{route('transfer')}}" class="d-flex justify-content-between update">
                        <span><img src="{{asset('img/currency-exchange.png')}}" alt=""> Transfer</span>
                        <span><i class="fas fa-angle-right"></i></span>
                    </a>
                    <hr>
                    <a href="{{route('wallet')}}" class="d-flex justify-content-between logout">
                        <span><img src="{{asset('img/wallet.png')}}" alt=""> Wallet</span>
                        <span><i class="fas fa-angle-right"></i></span>
                    </a>
                    <hr>
                    <a href="{{route('transaction')}}" class="d-flex justify-content-between logout">
                        <span><img src="{{asset('img/transaction.png')}}" alt=""> Transcation</span>
                        <span><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>
        </div>


    </div>
</div>

@endsection
