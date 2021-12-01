@extends('frontend.layouts.app_plain')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="height:100vh" >
        <div class="col-md-6">
            <div class="card auth-form">
                <div class="card-body">
                    <h3 class="text-center">Register</h3>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"  autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"  value="{{ old('email') }}"  autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror"  value="{{ old('phone') }}"  autofocus>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}"  autofocus>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"   autofocus>
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-5 mb-4 btn-theme">Register</button>

                        <div class="d-flex">
                            <a href="{{route('login')}}">Already have an account?</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
