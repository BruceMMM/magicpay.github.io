@extends('frontend.layouts.app')

@section('title','Notification')

@section('content')
<div class="">

    <div class="infinite-scroll">
        @foreach($notifications as $notification)

        <a href="{{url('notification/'.$notification->id)}}">
            <div class="card mb-2">
                <div class="card-body p-3">
                    <h6><i class="fas fa-bell @if(is_null($notification->read_at)) text-danger @endif"></i> {{Illuminate\Support\Str::limit($notification->data['title'], 40)}}</h6>
                    <p class="mb-1">{{Illuminate\Support\Str::limit($notification->data['message'], 100)}}</p>
                    <small class="text-muted mb-1">{{Carbon\Carbon::parse($notification->create_at)->format('Y-m-d h:i:s A')}}</small>
                </div>
            </div>
        </a>

        @endforeach
        {{$notifications->links() }}
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
    });
</script>

@endsection
