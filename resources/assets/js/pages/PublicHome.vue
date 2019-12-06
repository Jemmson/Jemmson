<template>
    <div>
        <v-container>
            <v-row>
                <v-col>
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
                                    @click="login(form)"
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
                </v-col>
            </v-row>
            <div class="container bg-white">
                <div class="row main">
                    <div class="col-12">
                        <h2 class="text-center">Subcontract Work</h2>
                        <p>How often at a job do you run into work that you do not do or work you dont have time for?
                            How often do you simply lose income because you either call someone to handle the job for
                            you or
                            just tell the customer to find someone else? Now with this app you can easily create a task
                            and then
                            subcontract that task, mark up the task, and then easily get paid for the work.</p>
                    </div>
                </div>
                <div class="row pricing">
                    <div class="col-12">
                        <h2 class="text-center" style="color:black">Invoice Customers</h2>
                        <p style="color:black">You can create an invoice at the beginning of a job and then use that as
                            a
                            commmunication tool
                            throughout the jobs life cycle. You simply initiate a bid for all jobs. Then construct that
                            bid,
                            subcontract any of those tasks, and then submit the bid to the customer. The customer then
                            has
                            the ability to approve the bid. Once the bid has been approve then the work can be
                            performed. Once
                            the
                            work has been performed the customer can now approve the finish work and pay using Stripe or
                            cash.
                        </p>
                    </div>
                </div>
                <div class="row features">
                    <div class="col-12">
                        <h2 class="text-center">Get Paid Easily</h2>
                        <p>How much money is wasted chasing down a check? How much of a pain is it to have to write a
                            check
                            to a subcontractor after a job has been finished? With Stripe this will happen easily.
                            The customer pays the invoice and then the money is automatically split between the
                            contractor
                            and the sub.</p>
                    </div>
                </div>
            </div>
        </v-container>
    </div>
</template>

<script>
  import { mapActions } from 'vuex'

  export default {
    computed: {
      csrf() {
        if (document.querySelector('meta[name="csrf-token"]')) {
          return document.querySelector('meta[name="csrf-token"]').getAttribute('content')

        }
      }
    },
    data() {
      return {
        currentWindow: window.location.origin,
        form: {
          username: '',
          password: '',
          remember: null,
          error: null,
          busy: false
        },
        showPassword: false,
        disabled: false
      }
    },
    methods: {
      ...mapActions([
        'login'
      ]),
      route(value) {
        if (value === 'login') {
          window.location = '/login'
        } else if (value === 'register') {
          window.location = '/#/register'
        }
      },
    },
    mounted() {
      this.$store.commit('setCurrentPage', this.$router.history.current.path)
    },
  }
</script>

<style scoped>

    .header-items {
        display: flex;
        flex-direction: column;
        width: 90%;
        margin-top: 1rem;
        margin-bottom: 1rem;
        margin-left: auto;
        margin-right: auto;
    }

    .slogan {
        font-size: 54px;
        color: black !important;
        /*color: #fff !important;*/
        letter-spacing: -1.55px;
        line-height: 1.18;
        font-family: Montserrat, Helvetica, Arial, sans-serif;
        /*font-family: Sailec-Bold, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif;*/
        font-style: normal;
        font-variant-ligatures: normal;
        font-variant-caps: normal;
        font-variant-numeric: normal;
        font-variant-east-asian: normal;
    }

    .form {
        width: 100%;
    }

    .form-submit-section {
        align-items: center;
        justify-content: center;
    }

    .form-submit {
        justify-content: space-between;
        width: 100%;
    }

    .align-checkbox {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 1rem;
        font-size: 1.25rem;
        align-items: center;
    }

    .sub-slogan {
        font-size: 12.96pt;
        color: black !important;
        /*color: #fff !important;*/
        font-family: Montserrat, Helvetica, Arial, sans-serif;
        /*font-family: Sailec-Bold, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif;*/
        margin-bottom: 1rem;
        padding-top: 1rem;
        padding-bottom: 2rem;
        border-bottom: black thin solid;
    }

    .header-content {
        background-color: white;
        /*background-color: #3772EE;*/
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .section {
        height: 700px;
        width: 100%;
    }

    .title {
        background-color: beige;
        padding-top: 10px;
        padding-left: 10px;
        padding-bottom: 10px;
        display: flex;
        justify-content: center;
        font-weight: bolder;
        font-size: 15pt;
        border-bottom: black solid thin;
    }

    .main {
        background-color: #203C4E;
    }

    .pricing {
        background-color: white;
    }

    .features {
        background-color: #378372;
    }

    .place-items {
        justify-content: space-around;
        align-items: center;
        width: 70%;
        height: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .main > h2 {
        /*padding-left: 1rem;*/
        /*padding-top: 2rem;*/
        /*padding-right: 1rem;*/
        width: 100%;
        margin: 0rem;
    }

    div > h2 {
        padding-left: 1rem;
        padding-top: 2rem;
        padding-right: 1rem;
        width: 100%;
        margin: 0rem;
    }

    .input {
        height: 3rem;
        border: thin black solid;
        border-radius: 5px;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 1rem;
        margin-top: 1rem;
        width: 90%;
        font-size: 1rem;
    }

    .checkbox-sizing {
        width: 15px;
        height: 15px;
    }

    .login-color {
        background-color: #3772EE;
    }

    .register-color {
        background-color: red;
    }

    .sub-title {
        color: #0069ff;
        display: inline-block;
        font-size: 2.5rem;
    }

    .form-item {
        width: 90%;
        margin-bottom: 1rem;
        align-items: center;
    }

    .form-title {
        color: #031b4e;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -.1px;
        margin-top: 1rem;
        margin-bottom: 1rem;
        text-align: center;
    }

    .place-form-items {
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .login-form {
        background-color: white;
        height: auto;
        width: 100%;
        border-radius: 10px;
    }

    h2 {
        color: rgb(255, 255, 255);
        padding: 3rem;
        font-family: "Open Sans", sans-serif;
        font-size: 40px;
        font-weight: 300;
        height: 88px;
        margin-left: 1rem;
        margin-top: 1rem;
    }

    p {
        color: rgb(255, 255, 255);
        padding-left: 1rem;
        padding-right: 1rem;
        padding-top: 1rem;
        font-family: "Open Sans", sans-serif;
        font-size: 18px;
        font-weight: 400;
        text-align: justify;
        line-height: 27px;
        margin-top: 0;
        /*width: 345px;*/
    }

    @media (min-width: 576px) {
        .header-items {
            max-width: 576px;
        }

        .header-items {
            flex-direction: row;
            align-items: center;
            margin-top: auto;
            height: 100%;
            margin-bottom: auto;
            justify-content: space-around;
        }
    }

    @media (min-width: 768px) {
        .header-items {
            max-width: 768px;
        }
    }

    @media (min-width: 992px) {
        .header-items {
            max-width: 992px;
        }

        .header-items {
            width: 80%;
        }

        .header-content-left {
            margin-right: 3rem;
        }

        .header-content-right {
            margin-left: 1rem;
        }
    }

    @media (min-width: 1200px) {
        .header-items {
            max-width: 1200px;
        }

        .header-content-right {
            margin-left: 3rem;
            margin-right: 8rem;
        }
    }

</style>