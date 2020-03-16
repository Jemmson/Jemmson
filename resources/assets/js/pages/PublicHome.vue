<template>
    <div>
        <v-app-bar
                color="#95ca97"
                fixed
                height="62px"
        >
            <!--            <v-img-->
            <!--                src="img/premiumlogo/onlinelogomaker-031420-0923-4133-2000-transparent.png"-->
            <!--                max-height="62px"-->
            <!--            >-->
            <!--            </v-img>-->
            <img src="img/premiumlogo/onlinelogomaker-031420-0923-4133-2000-transparent.png"
                 style="max-height:50px"
            >
            <v-spacer></v-spacer>
            <v-app-bar-nav-icon
                    @click="openMenu()"
            ></v-app-bar-nav-icon>
        </v-app-bar>
        <section
                class="section1-background flex align-center justify-center section-height"
        >
            <v-btn
                    color="white"
                    class="formatting"
                    @click="openMenu()"
            >Sub A Job And Get Paid
            </v-btn>
        </section>

        <section class="flex flex-wrap justify-center section-height">
            <div class="flex flex-col subsection-features">
                <img :src="'/img/jobs.svg'" alt="" srcset="" class="img-height mb-2">
                <h5 class="text-center f-bold">Initiate Jobs</h5>
                <p class="text-center" style="color: black; margin: 0 auto; width: 20rem;">
                    Initiate a job with a customer EASILY
                </p>
            </div>
            <div class="flex flex-col subsection-features">
                <img :src="'/img/tasks.svg'" alt="" srcset="" class="img-height mb-2">
                <h5 class="text-center f-bold">Sub It Out In A Snap</h5>
                <p class="text-center" style="color: black; margin: 0 auto; width: 20rem;">
                    Sub Out A Job In a snap. Wait for a bid. mark it up for a quick profit.
                </p>
            </div>
            <div class="flex flex-col subsection-features">
                <img :src="'/img/invoices.svg'" alt="" srcset="" class="img-height mb-2">
                <h5 class="text-center f-bold">Get Paid Quickly</h5>
                <p class="text-center" style="color: black; margin: 0 auto; width: 20rem;">
                    We do payment processing through stripe. a fast, secure, industry leading payment processing company
                </p>
            </div>
        </section>

        <section class="flex flex-wrap justify-center section-height bg-black">
            <div class="subsection">
                <h4 class="subsection-title">Features</h4>
                <div class="subsection-item">Invoicing</div>
                <div class="subsection-item">Payments</div>
                <div class="subsection-item">Subbing</div>
                <div class="subsection-item">Maps</div>
                <div class="subsection-item">Estimating</div>
            </div>

            <div class="subsection">
                <h4 class="subsection-title">Company</h4>
                <div class="subsection-item">About</div>
            </div>

            <div class="subsection">
                <h4 class="subsection-title">Resources</h4>
                <div class="subsection-item">Pricing</div>
            </div>
        </section>

        <v-dialog
                v-model="menu"
                width="500"
                fullscreen
                hide-overlay
        >
            <v-card>
                <login></login>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    import {mapActions} from 'vuex'
    import AppBar from '../components/header/AppBar'
    import Login from '../components/public/Login'

    export default {
        computed: {
            csrf() {
                if (document.querySelector('meta[name="csrf-token"]')) {
                    return document.querySelector('meta[name="csrf-token"]').getAttribute('content')

                }
            }
        },
        components: {
            AppBar,
            Login
        },
        data() {
            return {
                menu: false,
                currentWindow: window.location.origin,
                disabled: false
            }
        },
        methods: {
            openMenu() {
                this.menu = !this.menu
            },
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

<style>

    .bg-black {
        background-color: black;
    }

    .subsection-features {
        min-width: 150px;
        margin-top: 3rem;
    }

    .subsection {
        width: 116px;
        margin-top: 3rem;
    }

    .subsection-title {
        color: grey;
        font-size: 14pt;
    }

    .subsection-item {
        color: grey;
    }

    .section-height {
        height: 900px
    }

    .graphic-margins {
        margin-top: 60px;
    }

    .img-height {
        height: 7rem;
    }

    .formatting {
        color: white;
        font-size: x-large;
        font-weight: bolder;
    }

    .section1-background {
        /*background-image: url("https://res.cloudinary.com/jemmson-inc/image/upload/v1584213763/IMG_0054_c44cch.jpg");*/
        background-image: url(https://res.cloudinary.com/jemmson-inc/image/upload/a_360,h_900/v1584213763/IMG_0054_c44cch.jpg) !important;
        /*background-image: url(https://res.cloudinary.com/jemmson-inc/image/upload/v1584213763/IMG_0054_c44cch.jpg) !important;*/
        background-color: #666;
        background-size: auto auto;
        min-height: 800px;
        padding-bottom: 90px;
    }

    .v-dialog--fullscreen {
        margin-top: 62px !important;
    }

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