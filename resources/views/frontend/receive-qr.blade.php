@extends('frontend.layouts.app')

@section('title','QR Code')

@section('content')
<div class="receive-qr">
    <div class="card my-card">
        <div class="card-body text-center">
            <p class="mb-0">QR Scan to pay me</p>
            <div>
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(240)->generate($authUser->phone)) !!} ">
            </div>
            <p class="mb-1">{{$authUser->name}}</p>
            <p class="mb-1">{{$authUser->phone}}</p>
        </div>
    </div>
</div>

@endsection

