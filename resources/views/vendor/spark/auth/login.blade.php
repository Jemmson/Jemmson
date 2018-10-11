@extends('spark::layouts.app')

@section('content')

    @if(Auth::user() == null)
        <div class="flex flex-col further-info-main text-center">
            <div class="main-header p-4 uppercase">
                Please login
            </div>
            <div class="box border flex flex-col section">
                <div class="content">
                    <form class="form-horizontal" role="form" method="POST" action="/login">
                    {{ csrf_field() }}

                    <!-- E-Mail Address -->
                        <div class="input-section">
                            <label class="j-label">E-Mail Address/Phone</label>

                            <div>
                                <input type="text" class="border input" name="username" value="{{ old('email') }}"
                                       autofocus>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="input-section">
                            <label class="j-label">Password</label>

                            <div>
                                <input type="password" class="border input" name="password">
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="input-section">
                            <div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="input-section">
                            <div>
                                <button name="login" type="submit" class="register text-center border shadow uppercase" style="margin-bottom: .5rem;">
                                    <i class="fas m-r-xs fa-sign-in-alt" style="margin-right: .5rem;"></i>Login
                                </button>

                                <a class="text-center uppercase" style="color: mediumslateblue; text-decoration: underline;" href="{{ url('/password/reset') }}">Forgot Your
                                    Password?</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>


        </div>
    @else
        <div class="flex flex-col further-info-main text-center">
            <div class="main-header p-4 uppercase">
                You are already logged in
            </div>
            <div class="box border flex flex-col section" style="height: 70vh">

            </div>
        </div>
    @endif
    <jem-footer></jem-footer>
@endsection
