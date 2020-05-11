@extends('spark::layouts.app')

@section('content')
    <v-app-bar
            app
            color="#95ca97"
            height="100"
    >
        <v-avatar
                class="mr-3"
                color="#95ca97"
                style="height: 66px;
                            min-width: 100px;
                            width: 216px;"
        >
            <v-img
                    src="img/premiumlogo/jemmson-logo.png"
            ></v-img>
        </v-avatar>
    </v-app-bar>

    <v-container style="margin-top: 14rem;">
        <v-card>
            <v-card-title>Reset Password</v-card-title>
            <v-card-text>
                <input type="hidden" name="token" value="{{ $token }}">
            </v-card-text>
            <v-card-text>
                <v-text-field
                        id="username"
                        label="Email"
                        :value="{{ $email or old('email')  }}"
                        v-model="form.username"
                        prepend-icon="mdi-account-circle"
                />
            </v-card-text>
            <v-card-text>

                    <v-text-field
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            label="Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                            @change="setToken({{ $token }})"
                    />

            </v-card-text>

            <v-card-text>
                <v-text-field
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        label="Password"
                        prepend-icon="mdi-lock"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPassword = !showPassword"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn
                    id="reset"
                    ref="reset"
                    @click="passwordReset()"
                    color="primary"
                    text
                    >Reset Password</v-btn>
            </v-card-actions>
        </v-card>


    </v-container>

{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-8 col-md-offset-2">--}}
{{--                <div class="panel panel-default">--}}
{{--                    <div class="panel-heading">Reset Passwordsddssd</div>--}}

{{--                    <div class="panel-body">--}}
{{--                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">--}}
{{--                            {!! csrf_field() !!}--}}

{{--                            <input type="hidden" name="token" value="{{ $token }}">--}}

{{--                            <!-- E-Mail Address -->--}}
{{--                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
{{--                                <label class="col-md-4 control-label">E-Mail Address</label>--}}

{{--                                <div class="col-md-6">--}}
{{--                                    <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}" autofocus>--}}

{{--                                    @if ($errors->has('email'))--}}
{{--                                        <span class="help-block">--}}
{{--                                        {{ $errors->first('email') }}--}}
{{--                                    </span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- Password -->--}}
{{--                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
{{--                                <label class="col-md-4 control-label">Password</label>--}}

{{--                                <div class="col-md-6">--}}
{{--                                    <input type="password" class="form-control" name="password">--}}

{{--                                    @if ($errors->has('password'))--}}
{{--                                        <span class="help-block">--}}
{{--                                        {{ $errors->first('password') }}--}}
{{--                                    </span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- Password Confirmation -->--}}
{{--                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">--}}
{{--                                <label class="col-md-4 control-label">Confirm Password</label>--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <input type="password" class="form-control" name="password_confirmation">--}}

{{--                                    @if ($errors->has('password_confirmation'))--}}
{{--                                        <span class="help-block">--}}
{{--                                        {{ $errors->first('password_confirmation') }}--}}
{{--                                    </span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- Reset Button -->--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="col-md-6 col-md-offset-4">--}}
{{--                                    <button type="submit" class="btn btn-primary">--}}
{{--                                        <i class="fa fa-btn fa-refresh"></i>Reset Password--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
