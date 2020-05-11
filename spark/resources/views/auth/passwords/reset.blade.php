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
                >Reset Password
                </v-btn>
            </v-card-actions>
        </v-card>


    </v-container>

@endsection
