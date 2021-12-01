@extends('frontend.layouts.app_plain')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-6">
            <div class="card auth-form">

                <div class="card-body">
                    <h3 class="text-center">Admin Login</h3>

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="">Phone</label>
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" name="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-5 mb-4 btn-theme">Login</button>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
