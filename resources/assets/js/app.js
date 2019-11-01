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
// import { apiHost } from 'config'

window.Pusher = require('pusher-js')

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '07c3b89aa6d0a0206b23',
  cluster: 'mt1',
  encrypted: true
})

require('spark-bootstrap')

// register the plugin on vue
import Toasted from 'vue-toasted'
import Vue from 'vue'

Vue.use(Toasted, {
  duration: 5000,
  theme: 'bubble',
})

import Vuetify from 'vuetify'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/dist/vuetify.min.css'

const vuetifyOptions = {
  icons: {
    iconfont: 'mdi'
  }
}
Vue.use(Vuetify)
// const opts = {}
// export default new vuetify(opts)

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


import { router } from './router.js'

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
    path === '/benefits' ||
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

router.beforeEach((to, from, next) => {

  if (to.fullPath !== '/' && to.fullPath !== '/register') {
    axios.get('/checkAuth')
      .then(response => {

          if (response.data.auth) {
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

                axios.get('/user/current')
                  .then(response => {
                    this.user = response.data
                    console.log(JSON.stringify(this.user))
                    Spark.state.user = this.user

                    this.$store.commit('setUser', this.user)
                    window.User.user = this.user
                    window.GeneralContractor.user = this.user
                    window.SubContractor.user = this.user
                    window.Customer.user = this.user

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

                  })

              }

            }

            // need to send a user to further info if the user does not have
            // their password updated

            // a customer should not be able to initiate a bid or look at tasks
            // page

          } else {
            if (goingToANonAuthorizedPage(to.path)) {
              next()
            }
          }
        }
      )
      .catch(error => {
        console.log(JSON.stringify(error))
      })
  } else {
    next()
  }

})

router.afterEach((to, from) => {
  if (window.location.pathname === '/' && Object.keys(User).length > 0) {
    window.location = '/home'
  }
})

import MainHeader from './components/shared/Header'
import MainFooter from './components/shared/Footer'

var app = new Vue({
  mixins: [require('spark')],
  components: {
    MainHeader,
    MainFooter
  },
  router,
  store,
  vuetify: new Vuetify(vuetifyOptions),
  data: {
    user: window.User
  },
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

    // axios.post('/loadFeatures', {
    //   hello: 'world',
    //   location: location
    // }).then((response) => {
    //
    //   if (response.data.redirect) {
    //     if (response.data.redirect !== window.location.pathname) {
    //       window.location = response.data.redirect
    //     }
    //   } else {
    //     if (response.data.state[0]) {
    //       this.$store.commit('loadFeatures', response.data.state[0])
    //     }
    //     if (this.$store.state.user.user === '') {
    //       this.$store.commit('setUser', response.data.state[1])
    //     }
    //   }
    //
    // }).catch(function(error) {
    //   console.log(JSON.stringify(error))
    // })
  }
})

require('./bootstrap')

function checkThatCurrentJobExistsForRoutesThatNeedIt(route) {
  console.log('checking that job exists in store', route)
  if (route == '/job/tasks' ||
    route == '/job/add/task' ||
    route == '/job/task/' + route.split('/')[3]) {
    if (store.state.job.model === null) {
      router.push('/bids')
    }
  }
}
