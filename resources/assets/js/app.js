/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js')

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '07c3b89aa6d0a0206b23',
    cluster: 'mt1',
    encrypted: true
})

require('spark-bootstrap')

// https://github.com/shakee93/vue-toasted
import Toasted from 'vue-toasted'
import Vue from 'vue'

Vue.use(Toasted, {
    duration: 5000,
    theme: 'bubble',
})

import Vuetify from 'vuetify'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css'

import VueMask from 'v-mask'

Vue.use(VueMask)

const vuetifyOptions = {
    icons: {
        iconfont: 'mdi'
    },
    breakpoint: {
        thresholds: {
            xs: 340,
            sm: 540,
            md: 800,
            lg: 1280,
        },
        scrollBarWidth: 24,
    }
}
Vue.use(Vuetify)

import VuePaginate from 'vue-paginate'

Vue.use(VuePaginate)

import {
    store
} from './vuex/index'
import Format from './classes/Format'
import Language from './classes/Language'
import GeneralContractor from './classes/GeneralContractor'
import SubContractor from './classes/SubContractor'
import Customer from './classes/Customer'
import User from './classes/User'
import TaskUtil from './classes/TaskUtil'

import {router} from './router.js'

window.Format = Format
window.Language = Language
window.User = new User(Spark.state.user)
window.GeneralContractor = new GeneralContractor(Spark.state.user)
window.SubContractor = new SubContractor(Spark.state.user)
window.Customer = new Customer(Spark.state.user)
window.TaskUtil = new TaskUtil()
window.autocomplete = {}

require('./spark-components/bootstrap')
require('./components/bootstrap')

Spark.forms.register = {
    usertype: ''
}

const requireComponent = require.context(
    // The relative path of the components folder
    './components',
    // Whether or not to look in subfolders
    true,
    // The regular expression used to match base component filenames
    /Base[A-Z]\w+\.(vue|js)$/
)

requireComponent.keys().forEach(fileName => {
    // Get component config
    const componentConfig = requireComponent(fileName)

    // Get PascalCase name of component
    const componentName = upperFirst(
        camelCase(
            // Gets the file name regardless of folder depth
            fileName
                .split('/')
                .pop()
                .replace(/\.\w+$/, '')
        )
    )

    // Register component globally
    Vue.component(
        componentName,
        // Look for the component options on `.default`, which will
        // exist if the component was exported with `export default`,
        // otherwise fall back to module's root.
        componentConfig.default || componentConfig
    )
})

function goingToANonAuthorizedPage(path) {

    return path === '/demo' ||
        path === '/' ||
        path === '/check_accounting' ||
        path === '/howto' ||
        path === '/documentation' ||
        path === '/pricing' ||
        path === '/terms' ||
        path === '/benefits' ||
        path === '/passwordEmailVerification' ||
        path === '/register' ||
        path === '/registerQuickBooks'

}

function isUserLoggedIn() {
    return Object.keys(User).length > 0
}

function isUserInVuexStore() {
    return store.state.user.user !== ''
}

function isUserInSparkState() {
    if (Spark.state.user) {
        return true
    } else {
        return false
    }
}

function updateStoreWithSparkState() {
    store.state.user.user = Spark.state.user
}

function doAuthRouting(to, from, next) {

    if (store.state.user.user.password_updated === 0) {
        if (to !== '/#/furtherInfo')
            next('/#/furtherInfo')
    } else {
        next()
    }
}

function isAuthorized() {

    return localStorage.getItem('auth') === 'true'
}

function timeHasNotExpired() {
    const now = moment()
    const expiredTime = parseInt(localStorage.getItem('authTime'));

    return expiredTime > now.valueOf()
}

function getAuth() {
    return isAuthorized() && timeHasNotExpired()
}

function setAuthorizedInLocalStorage() {
    localStorage.setItem('auth', true)
}

function setTwoHourExpirationTimeInLocalStorage() {
    let now = moment.now()
    let expireTime = now + 7200000;
    localStorage.setItem('authTime', expireTime.toString())
}

function setAuth() {
    setAuthorizedInLocalStorage()
    setTwoHourExpirationTimeInLocalStorage()
    store.state.auth = true
}

function handleSuccessfulAuth(to, from, next) {

    checkThatCurrentJobExistsForRoutesThatNeedIt(to.path)

    if (isUserInVuexStore()) {

        // doAuthRouting(to, from, next)

        if (store.state.user.user.password_updated === 0) {
            if (to.fullPath === '/furtherInfo') {
                next()
            } else {
                next('/furtherInfo')
            }
        } else {
            next()
        }

    } else {

        if (isUserInSparkState()) {

            updateStoreWithSparkState()

            if (store.state.user.user.password_updated === 0) {
                if (to.fullPath === '/furtherInfo') {
                    next()
                } else {
                    next('/furtherInfo')
                }
            } else {
                next()
            }

        } else {

            axios.get('/user/current')
                .then(response => {
                    this.user = response.data
                    Spark.state.user = this.user
                    this.$store.commit('setUser', this.user)
                    window.User.user = this.user
                    window.GeneralContractor.user = this.user
                    window.SubContractor.user = this.user
                    window.Customer.user = this.user

                    if (store.state.user.user.password_updated === 0) {
                        if (to.fullPath === '/furtherInfo') {
                            next()
                        } else {
                            next('/furtherInfo')
                        }
                    } else {
                        next()
                    }

                })
        }
    }
}

import MainHeader from './components/shared/Header'
import MainFooter from './components/shared/Footer'

// router.beforeResolve((to, from, next) => {
//     // If this isn't an initial page load.
//     if (to.name) {
//         // Start the route progress bar.
//         NProgress.start()
//     }
//     next()
// })
//
// router.afterEach((to, from) => {
//     // Complete the animation of the route progress bar.
//     NProgress.done()
// })

router.beforeEach((to, from, next) => {
    if (to.fullPath !== '/' &&
        to.fullPath !== '/register' &&
        to.fullPath !== '/termsAuth' &&
        to.fullPath !== '/termsOfUse' &&
        to.fullPath !== '/privacyPolicy'
    ) {

        if (getAuth()) {
            handleSuccessfulAuth(to, from, next)
        } else {
            axios.get('/checkAuth')
                .then(response => {
                        if (response.data.auth) {
                            setAuth()
                            handleSuccessfulAuth(to, from, next)
                        } else {
                            if (goingToANonAuthorizedPage(to.path)) {
                                next()
                                store.state.auth = false
                            } else {
                                next('/')
                            }
                        }
                    }
                )
                .catch(error => {
                    store.state.auth = false
                    localStorage.setItem('auth', false)
                })
        }
    } else {
        next()
    }

})

router.afterEach((to, from) => {
    if (window.location.pathname === '/' && Object.keys(User).length > 0) {
        window.location = '/home'
    }
})

var app = new Vue({
    mixins: [require('spark')],
    components: {
        MainHeader,
        MainFooter
    },
    data: {
        user: window.User,
        showLogin: true,
        showPassword: false,
        showConfirmationPassword: false,
        showHome: false,
        loading: false,
        form: {
            email: '',
            password: '',
            password_confirmation: '',
            token: null
        }
    },
    methods: {
        async passwordReset() {
            if (this.passwordsMatch()) {
                this.loading = true;
                const {data} = await axios.post('/password/reset', {
                    email: this.form.email,
                    password: this.form.password,
                    password_confirmation: this.form.password_confirmation,
                    token: this.form.token
                });

                if (data.error) {
                } else {
                    Bus.$emit('updateUser');
                    this.$router.push('/home');
                    this.showHome = true;
                }
                this.loading = false;
            }
        },
        setToken(token) {
            this.form.token = token;
        },
        setEmail(email) {
            this.form.email = email;
        },
        passwordsMatch() {
            return this.form.password_confirmation === this.form.password
        },
        passwordsMatchRule() {
            return this.passwordsMatch() || 'Passwords Must Match'
        },
        goHome() {
            // this.$router.push('/home')
            window.location = '/#/home'
        }
    },
    store,
    router,
    vuetify: new Vuetify(vuetifyOptions),
    mounted() {
        let location = {
            hash: window.location.hash,
            host: window.location.host,
            hostname: window.location.hostname,
            href: window.location.href,
            origin: window.location.origin,
            pathname: window.location.pathname,
            port: window.location.port,
            protocol: window.location.protocol
        }
    }
})

// Add a response interceptor
axios.interceptors.response.use(function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response;
}, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    if (error.response.status === 419) {
        Vue.toasted.error("Session Has Expired. Please log in again.");
        window.location = '/logout';
    }
    return Promise.reject(error);
});


require('./bootstrap')

function checkThatCurrentJobExistsForRoutesThatNeedIt(route) {
    if (route == '/job/tasks' ||
        route == '/job/add/task') {
        if (store.state.job.model === null) {
            router.push('/bids')
        }
    }
}
