@extends('frontend.layouts.app')

@section('title','Profile')

@section('content')

<div class="update-password">


    <div class="card mb-3">
        <div class="card-body">
            <div class="text-center">
                <img src="{{asset('img/Password_Flatline.png')}}" alt="" >
            </div>
            <form action="{{route('update-password.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="">Old Password</label>
                    <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" value="{{old('old_password')}}">
                    @error('old_password')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="">New Password</label>
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" value="{{old('new_password')}}">
                    @error('new_password')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button Type="submit" class="btn btn-theme btn-block mt-4">Update Password</button>
            </form>
        </div>
    </div>
</div>


@endsection

@section('script')
 <script>
     $(document).ready(function() {

    } );
 </script>
@endsection
