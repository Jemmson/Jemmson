<template>
    <v-card width="100%"
    >
        <v-card-title class="w-break pb-0 flex flex-col">
            <template
                    style="font-size: 1.5rem;"
            >Login Or
                <v-btn
                        id="register"
                        to="/register"
                        width="40%"
                        text
                        class="capitalize"
                        color="success">
                    Click here to Register
                </v-btn>
            </template>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-text-field
                        id="username"
                        label="Email"
                        v-model="form.username"
                        prepend-icon="mdi-account-circle"
                />
                <v-text-field
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        label="Password"
                        prepend-icon="mdi-lock"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPassword = !showPassword"
                />
            </v-form>
        </v-card-text>
        <v-card-actions>
            <div></div>
            <v-spacer></v-spacer>
            <v-btn
                    id="submit"
                    color="info"
                    :loading="form.busy"
                    width="40%"
                    @click="logIntoApp(form)"
            >Login
            </v-btn>
        </v-card-actions>
        <v-divider></v-divider>
        <v-col cols="12" class="text-center">
            <v-row justify="center" align-content="center">
                <v-checkbox
                        v-model="form.remember"
                        class=""
                        label="Remember Me"></v-checkbox>
            </v-row>

            <v-btn
                    text
                    color="primary"
                    link
                    @click="$router.push('/passwordEmailVerification')"
            >Forgot Your Password?
            </v-btn>
        </v-col>

    </v-card>
</template>

<script>
    import {mapActions} from 'vuex'

    export default {
        name: 'Login',
        data() {
            return {
                showLogin: true,
                showPassword: false,
                form: {
                    username: '',
                    password: '',
                    remember: null,
                    error: null,
                    busy: false
                },
            }
        },
        methods: {
            ...mapActions([
                'login'
            ]),
            logIntoApp(form) {
                this.login(form)
            },
        }
    }
</script>

<style>

</style>
