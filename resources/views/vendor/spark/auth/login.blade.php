@extends('spark::layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            @if(Auth::user() == null)
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default card card-1">
                        <div class="panel-heading">Login</div>

                        <div class="panel-body">
                            @include('spark::shared.errors')

                            <form class="form-horizontal" role="form" method="POST" action="/login">
                            {{ csrf_field() }}

                            <!-- E-Mail Address -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">E-Mail Address/Phone</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="username" value="{{ old('email') }}"
                                               autofocus>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember"> Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Login Button -->
                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button name="login" type="submit" class="btn btn-primary">
                                            <i class="fas m-r-xs fa-sign-in-alt"></i>Login
                                        </button>

                                        <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your
                                            Password?</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <h1 class="text-center">You are already logged in</h1>
            @endif


        </div>
    </div>
@endsection
