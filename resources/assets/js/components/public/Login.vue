<template>
    <v-dialog
            width="400"
            v-model="showLogin"
    >
        <v-card width="445" class="mx-auto mt-5">
            <v-card-title class="pb-0">
                <h1>Login</h1>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field
                            label="Email"
                            v-model="form.username"
                            prepend-icon="mdi-account-circle"
                    />
                    <v-text-field
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            label="Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                    />
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn
                        id="register"
                        to="/register"
                        width="40%"
                        color="success">
                    Register
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
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
                        href="/password/reset"
                >Forgot Your Password?
                </v-btn>
            </v-col>

        </v-card>
    </v-dialog>
</template>

<script>
  import { mapActions } from 'vuex'

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
      close(){
        this.showLogin = false
      },
      logIntoApp(form){
        this.login(form)
      },
    }
  }
</script>

<style>

</style>
