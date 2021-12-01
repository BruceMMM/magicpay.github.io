@extends('frontend.layouts.app')

@section('title','Notification')

@section('content')
<div>
    <div class="card">
        <div class="card-body text-center">
            <div >
                <img src="{{asset('img/Notifications.png')}}" width="200px"alt="">
            </div>
            <h6>{{$notification->data['title']}}</h6>
            <p class="text-muted mb-1">{{$notification->data['message']}}</p>
            <p><small class="mb-3">{{Carbon\Carbon::parse($notification->created_at)->format('Y-m-d H:i:s A')}}</small></p>
            <a href="{{$notification->data['web_link']}}" class="btn btn-sm btn-theme">Containue</a>
        </div>
    </div>

</div>
@endsection
