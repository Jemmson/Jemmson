<template>
    <div class="wrapper">
        <div class="title"><h1 class="text-center">Jemsub</h1></div>
        <div class="section header-content">
            <div class="header-items">
                <div class="flex flex-col items-center header-content-left">
                    <button name="login"
                            @click="route('register')"
                            style="margin-top: 1rem; margin-bottom: 1rem;"
                            class="btn btn-sm btn-yellow">
                        <i class="fas m-r-xs fa-sign-in-alt mr-2"></i>Register Now
                    </button>
                    <div class="slogan text-center">Subbin' <br> Made Easy!</div>
                    <div class="sub-slogan text-center">Register or Login today to sub contract your work and get paid
                        without all
                        of the hassle
                    </div>
                </div>
                <div class="login-form place-form-items header-content-right">
                    <div class="flex flex-col">

                        <!--<h3 class="form-title">-->
                        <!--Simplify Subcontracting-->
                        <!--</h3>-->
                        <!--<h4 class="sub-title text-center">Create Your Bid</h4>-->
                        <h4 class="sub-title text-center">Login</h4>
                    </div>
                    <!--<form class="form" action="/login" method="post">-->
                    <form class="form form-horizontal" role="form">
                        <input type="hidden" name="_token" :value="csrf">
                        <div class="flex flex-col">

                            <!-- E-Mail Address -->
                            <input v-model="form.username"
                                   type="text"
                                   class="input"
                                   name="username"
                                   ref="username"
                                   placeholder="Email Address / Phone"
                                   autofocus>
                            <!-- Password -->
                            <input v-model="form.password"
                                   type="password"
                                   class="input"
                                   ref="password"
                                   placeholder="Password"
                                   name="password">
                        </div>
                        <!-- <div class="flex flex-col">
                            <div v-if="form.error !== null" class="bg-red-lightest border border-red-light text-red-dark px-4 py-3 rounded relative" role="alert">
                              <strong class="font-bold">Holy smokes!</strong>
                              <span class="block sm:inline">{{form.error}}</span>
                            </div>
                        </div> -->

                        <div class="flex flex-col form-submit-section">
                            <!-- Remember Me -->
                            <div class="checkbox align-checkbox flex">
                                <input v-model="form.remember" type="checkbox" class="checkbox-sizing mr-2"
                                       name="remember">
                                <div>Remember Me</div>
                            </div>
                            <div class="flex form-submit form-item">
                                <!-- Login Button -->
                                <button @click.prevent="login(form)"
                                        :disabled="form.busy"
                                        ref="submit"
                                        name="login" type="submit"
                                        class="btn btn-sm btn-blue">
                                    <i class="fas m-r-xs fa-sign-in-alt mr-2"></i>Login
                                </button>
                                <a class="" :href="currentWindow + '/password/reset'">Forgot Your
                                    Password?</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="section main">
            <h2 class="text-center">Subcontract Work</h2>
            <p>How often at a job do you run into work that you do not do or work you dont have time for?
                How often do you simply lose income because you either call someone to handle the job for you or
                just tell the customer to find someone else? Now with this app you can easily create a task and then
                subcontract that task, mark up the task, and then easily get paid for the work.</p>
        </div>
        <div class="section pricing">
            <h2 style="color:black">Invoice Customers</h2>
            <p style="color:black">You can create an invoice at the beginning of a job and then use that as a
                commmunication tool
                throughout the jobs life cycle. You simply initiate a bid for all jobs. Then construct that bid,
                subcontract any of those tasks, and then submit the bid to the customer. The customer then has
                the ability to approve the bid. Once the bid has been approve then the work can be performed. Once the
                work has been performed the customer can now approve the finish work and pay using Stripe or cash.</p>
        </div>
        <div class="section features">
            <h2>Get Paid Easily</h2>
            <p>How much money is wasted chasing down a check? How much of a pain is it to have to write a check
                to a subcontractor after a job has been finished? With Stripe this will happen easily.
                The customer pays the invoice and then the money is automatically split between the contractor
                and the sub.</p>
        </div>
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
          busy: false,
        }
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
          window.location = '/register#/'
        }
      },
    }
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
        padding-left: 1rem;
        padding-top: 2rem;
        padding-right: 1rem;
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