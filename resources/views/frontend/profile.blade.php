@extends('frontend.layouts.app')

@section('title','Profile')

@section('profile-active','mm-active')

@section('content')

<div class="account">
    <div class="profile mb-3">
        <img src="https://ui-avatars.com/api/?background=008888&color=fff&name={{Auth::user()->name}}" alt="">
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <span>Name</span>
                <span>{{ Auth::user()->name }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span>Phone</span>
                <span> {{ Auth::user()->phone }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span>Email</span>
                <span>{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <a href="{{route('update-password')}}" class="d-flex justify-content-between update">
                <span>Update Password</span>
                <span><i class="fas fa-angle-right"></i></span>
            </a>
            <hr>
            <a href="" class="d-flex justify-content-between logout">
                <span>Logout</span>
                <span><i class="fas fa-angle-right"></i></span>
            </a>
        </div>
    </div>


</div>


@endsection

@section('script')
 <script>
     $(document).ready(function() {
        $(document).on('click','.logout',function(e){
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure you want to logout?',
                showCancelButton: true,
                confirmButtonText: `Confirm`,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url : "{{route('logout')}}",
                        type : 'POST',
                        success: function(){
                            window.location.replace("{{route('profile')}}");
                        }
                    })
                }
            })
        });
    } );
 </script>
@endsection
