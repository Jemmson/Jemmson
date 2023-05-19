<template>
    <div>
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

            <v-spacer></v-spacer>
          <v-btn @click="openMenu()">LOGIN</v-btn>
<!--            <v-app-bar-nav-icon-->
<!--                    id="loginModalButton"-->
<!--                    data-cy="loginModalButton"-->
<!--                    @click="openMenu()"-->
<!--            ></v-app-bar-nav-icon>-->


        </v-app-bar>

        <v-main>
            <section id="hero">
                <v-row no-gutters>
                    <v-img
                            :min-height="'calc(100vh - ' + $vuetify.application.top + 'px)'"
                            src="https://res.cloudinary.com/jemmson-inc/image/upload/a_90/v1588213756/oUG0dypr9n3mJJ5AysO69ZvUg3WQujZgU3mkXIEz_ajhvan.jpg"
                    >
                        <v-theme-provider dark>
                            <v-container fill-height>
                                <v-row
                                        align="center"
                                        class="white--text mx-auto"
                                        justify="center"
                                >
                                    <v-col
                                            class="white--text text-center"
                                            cols="12"
                                            tag="h1"
                                    >
                    <span
                            :class="[$vuetify.breakpoint.smAndDown ? 'display-1' : 'display-2']"
                            class="font-weight-light"
                    >
                      WELCOME TO
                    </span>

                                        <br>

                                        <span
                                                :class="[$vuetify.breakpoint.smAndDown ? 'display-3': 'display-4']"
                                                class="font-weight-black"
                                        >
                      JEMMSON
                    </span>

                                    </v-col>

                                    <v-btn
                                            class="align-self-end"
                                            fab
                                            outlined
                                            @click="$vuetify.goTo('#about-me')"
                                    >
                                        <v-icon>mdi-chevron-double-down</v-icon>
                                    </v-btn>
                                </v-row>
                            </v-container>
                        </v-theme-provider>
                    </v-img>
                </v-row>
            </section>

            <section id="about-me">
                <div class="py-12"></div>

                <v-container class="text-center">
                    <h2 class="display-2 font-weight-bold mb-3">ABOUT ME</h2>

                    <v-responsive
                            class="mx-auto mb-8"
                            width="56"
                    >
                        <v-divider class="mb-1"></v-divider>

                        <v-divider></v-divider>
                    </v-responsive>

                    <v-responsive
                            class="mx-auto title font-weight-light mb-8"
                            max-width="720"
                    >
                        <p style="color:black">As a contractor, you know how important it is to find people to help finish the job right.  Who you choose to trust with your projects can make or break your professional reputation. On the other hand, during busy times if you try to do the all the work yourself, turnover time slows, stress rises, efficiency lags and customers are unhappy.</p>

                        <p style="color:black">What if there was a way for you to do what you do best and sub the rest? And what if you could create a passive income at the same time? JEMMSON makes that happen. Communicate with subcontractors and customers, view finished jobs, create instant invoices, and get paid all with a few taps on your smart phone, computer screen or other mobile device. Our site is simple, fast, and secure. Get some peace of mind.  Do what you do best… and sub the rest with JEMMSON!</p>

                    </v-responsive>

<!--                    <v-avatar-->
<!--                            class="elevation-12 mb-12"-->
<!--                            size="128"-->
<!--                    >-->
<!--                        <v-img src="https://cdn.vuetifyjs.com/images/john.png"></v-img>-->
<!--                    </v-avatar>-->

                    <div></div>

<!--                    <v-btn-->
<!--                            color="grey"-->
<!--                            href="https://vuetifyjs.com"-->
<!--                            outlined-->
<!--                            large-->
<!--                    >-->
<!--            <span class="grey&#45;&#45;text text&#45;&#45;darken-1 font-weight-bold">-->
<!--              Vuetify Documentation-->
<!--            </span>-->
<!--                    </v-btn>-->
                </v-container>

                <div class="py-12"></div>
            </section>

            <section
                    id="features"
                    class="grey lighten-3"
            >
                <div class="py-12"></div>

                <v-container class="text-center">
                    <h2 class="display-2 font-weight-bold mb-3">JEMMSON FEATURES</h2>

                    <v-responsive
                            class="mx-auto mb-12"
                            width="56"
                    >
                        <v-divider class="mb-1"></v-divider>

                        <v-divider></v-divider>
                    </v-responsive>

                    <v-row>
                        <v-col
                        >
                            <v-card
                                    class="py-12 px-4"
                                    color="grey lighten-5"
                                    flat
                            >
                                <v-theme-provider dark>
                                    <div>
                                        <v-avatar
                                                style="height: 6rem;
                                                    width: 100%;
                                                    padding-top: 1rem;
                                                    padding-bottom: 1rem;"
                                        >
                                            <img :src="'/img/jobs.svg'" alt="" srcset="" class="mb-2"
                                            >
<!--                                            <v-icon-->
<!--                                                    large-->
<!--                                                    v-text="icon"-->
<!--                                            ></v-icon>-->
                                        </v-avatar>
                                    </div>
                                </v-theme-provider>

                                <v-card-title
                                        class="justify-center font-weight-black text-uppercase"
                                >Initiate Jobs</v-card-title>

                                <v-card-text
                                        class="subtitle-1"
                                >
                                    Initiate a job with a customer EASILY
                                </v-card-text>
                            </v-card>
                            <v-card
                                    class="py-12 px-4 mt-1rem"
                                    color="grey lighten-5"
                                    flat
                            >
                                <v-theme-provider dark>
                                    <div>
                                        <v-avatar
                                                style="height: 6rem;
                                                    width: 100%;
                                                    padding-top: 1rem;
                                                    padding-bottom: 1rem;"
                                        >
                                            <img :src="'/img/tasks.svg'" alt="" srcset="" class="mb-2"
                                            >
                                            <!--                                            <v-icon-->
                                            <!--                                                    large-->
                                            <!--                                                    v-text="icon"-->
                                            <!--                                            ></v-icon>-->
                                        </v-avatar>
                                    </div>
                                </v-theme-provider>

                                <v-card-title
                                        class="justify-center font-weight-black text-uppercase"
                                >Sub It Out In A Snap</v-card-title>

                                <v-card-text
                                        class="subtitle-1"
                                >
                                    Sub Out A Job In a snap. Wait for a bid. mark it up for a quick profit.
                                </v-card-text>
                            </v-card>
                            <v-card
                                    class="py-12 px-4 mt-1rem"
                                    color="grey lighten-5"
                                    flat
                            >
                                <v-theme-provider dark>
                                    <div>
                                        <v-avatar
                                                style="height: 6rem;
                                                    width: 100%;
                                                    padding-top: 1rem;
                                                    padding-bottom: 1rem;"
                                        >
                                            <img :src="'/img/invoices.svg'" alt="" srcset="" class="mb-2"
                                            >
                                            <!--                                            <v-icon-->
                                            <!--                                                    large-->
                                            <!--                                                    v-text="icon"-->
                                            <!--                                            ></v-icon>-->
                                        </v-avatar>
                                    </div>
                                </v-theme-provider>

                                <v-card-title
                                        class="justify-center font-weight-black text-uppercase"
                                >Get Paid Quickly</v-card-title>

                                <v-card-text
                                        class="subtitle-1"
                                >
                                    We do payment processing through stripe. a fast, secure, industry leading payment processing company
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>

                <div class="py-12"></div>
            </section>
        </v-main>

        <v-footer
                class="flex flex-wrap justify-center section-height bg-black"
                height="220"
                style="height: 219px;
                    background-color: black;"
        >
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
        </v-footer>

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
                disabled: false,
                articles: [
                    {
                        src: 'https://images.unsplash.com/photo-1423784346385-c1d4dac9893a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80',
                        title: 'Mobile first & Responsive',
                        text: 'Phasellus lorem enim, luctus ut velit eget, convallis egestas eros. Sed ornare ligula eget tortor tempor, quis porta tellus dictum.',
                    },
                    {
                        src: 'https://images.unsplash.com/photo-1475938476802-32a7e851dad1?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80',
                        title: 'Think outside the box',
                        text: 'Nam ut leo ipsum. Maecenas pretium aliquam feugiat. Aenean vel tempor est, vitae tincidunt risus. Sed sodales vestibulum nibh.',
                    },
                    {
                        src: 'https://images.unsplash.com/photo-1416339442236-8ceb164046f8?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1892&q=80',
                        title: 'Small changes, big difference',
                        text: 'Vestibulum in dictum velit, in rhoncus nibh. Maecenas neque libero, interdum a dignissim in, aliquet vitae lectus. Phasellus lorem enim, luctus ut velit eget.',
                    },
                ],
                features: [
                    {
                        icon: 'mdi-account-group-outline',
                        title: 'Vibrant Community',
                        text: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto cupiditate sint possimus quidem atque harum excepturi nemo velit tempora! Enim inventore fuga, qui ipsum eveniet facilis obcaecati corrupti asperiores nam',
                    },
                    {
                        icon: 'mdi-update',
                        title: 'Frequent Updates',
                        text: 'Sed ut elementum justo. Suspendisse non justo enim. Vestibulum cursus mauris dui, a luctus ex blandit. Lorem ipsum dolor sit amet consectetur adipisicing elit. qui ipsum eveniet facilis obcaecati corrupti consectetur adipisicing elit.',
                    },
                    {
                        icon: 'mdi-shield-outline',
                        title: 'Long-term Support',
                        text: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto cupiditate sint possimus quidem atque harum excepturi nemo velit tempora! Enim inventore fuga, qui ipsum eveniet facilis obcaecati corrupti asperiores nam',
                    },
                ],
                stats: [
                    ['24k', 'Github Stars'],
                    ['330+', 'Releases'],
                    ['1m', 'Downloads/mo'],
                    ['5m', 'Total Downloads'],
                ]
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
        margin-top: 100px !important;
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