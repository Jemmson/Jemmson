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
                    src="/img/premiumlogo/jemmson-logo.png"
            ></v-img>
        </v-avatar>
    </v-app-bar>

    <v-container style="margin-top: 14rem;">

        <v-card v-if="showHome">
            <v-card-title>Congratulations!</v-card-title>
            <v-card-subtitle>You Have Successfully Reset Your Password. Please Press THe Button Below to Go to the Home
                screen
            </v-card-subtitle>
            <v-card-text>

            </v-card-text>
            <v-card-actions>
                <v-btn
                        id="home"
                        ref="home"
                        style="color: #1976d2 !important; caret-color: #1976d2 !important;"
                        @click="goHome()"
                        color=""
                        text="">Home
                </v-btn>
            </v-card-actions>
        </v-card>


        <v-card>
            <v-card-title>Reset Password</v-card-title>
            <v-card-subtitle>{{ $email }}</v-card-subtitle>
            <v-card-text>
                <input type="hidden" name="token" value="{{ $token }}">
            </v-card-text>
            <v-card-text>
                <v-text-field
                        id="username"
                        label="Email"
                        @change="setEmail('{{ $email }}')"
                        v-model="form.email"
                        prepend-icon="mdi-account-circle"
                />
            </v-card-text>
            <v-card-text>
                {{--TODO: make the error fields red. cant seem to do this.--}}
                <v-text-field
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        label="Password"
                        :color="passwordsMatchRule() === 'Passwords Must Match'  ? '#ff5252': ''"
                        prepend-icon="mdi-lock"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPassword = !showPassword"
                        :class="passwordsMatchRule() === 'Passwords Must Match'  ? 'error--text-blade': ''"
                        :rules="[
                            passwordsMatchRule()
                        ]"
                        @change="setToken('{{ $token }}')"
                />

            </v-card-text>

            <v-card-text>

                {{--TODO: make the error fields red. cant seem to do this.--}}
                <v-text-field
                        id="password"
                        :type="showConfirmationPassword ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        label="Confirm Password"
                        prepend-icon="mdi-lock"
                        :class="passwordsMatchRule() === 'Passwords Must Match'  ? 'error--text-blade': ''"
                        :rules="[
                            passwordsMatchRule()
                        ]"
                        :append-icon="showConfirmationPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showConfirmationPassword = !showConfirmationPassword"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn
                        id="reset"
                        ref="reset"
                        style="color: #1976d2 !important; caret-color: #1976d2 !important;"
                        @click="passwordReset()"
                        color="primary"
                        :loading="loading"
                        text
                >Reset Password
                </v-btn>
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
