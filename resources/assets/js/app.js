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

import VueRouter from 'vue-router'

Vue.use(VueRouter)

// register the plugin on vue
import Toasted from 'vue-toasted'

Vue.use(Toasted, {
  duration: 5000,
  theme: 'bubble',
})

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
import CheckAccountingApp from './pages/CheckAccountingApp'
import Home from './pages/Home'
import Feedback from './pages/Feedback'
import PublicHome from './pages/PublicHome'
import Jobs from './pages/Jobs'
import JobTasks from './pages/JobTasks'
import AddJobTask from './pages/AddJobTask'
import JobTask from './pages/JobTask'

import Job from './pages/Job'
import InitiateBid from './pages/InitiateBid'
import Tasks from './pages/Tasks'
import Invoices from './pages/Invoices'
import Invoice from './pages/Invoice'
import SubInvoice from './pages/SubInvoice'
import FurtherInfo from './pages/FurtherInfo'
import TaskImages from './pages/TaskImages'
import Benefits from './pages/Benefits'
import Demo from './pages/Demo'
import HowTo from './pages/HowTo'
import RegisterQuickBooks from './pages/RegisterQuickBooks'
import Register from './pages/Register'
import UserAuthorizationPage from './pages/UserAuthorizationPage'
// import BidTask from './components/job/BidTask';

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

// vue routes
const routes = [
  {
    path: '/bids',
    component: Jobs
  },
  {
    path: '/job/tasks',
    component: JobTasks
  },
  {
    path: '/job/task/:index',
    component: JobTask
  },
  {
    path: '/job/add/task',
    component: AddJobTask
  },
  {
    path: '/userAuthorizationPage',
    component: UserAuthorizationPage
  },
  {
    path: '/feedback',
    component: Feedback
  },
  {
    path: '/benefits',
    component: Benefits
  },
  {
    path: '/check_accounting',
    component: CheckAccountingApp
  },
  {
    path: '/demo',
    component: Demo
  },
  {
    path: '/howto',
    component: HowTo
  },
  {
    path: '/bid/:id',
    component: Job
  },
  {
    path: '/tasks',
    component: Tasks
  },
  {
    path: '/home',
    component: Home
  },
  {
    path: '/',
    component: PublicHome
  },
  {
    path: '/initiate-bid',
    component: InitiateBid
  },
  {
    path: '/invoices',
    component: Invoices
  },
  {
    path: '/invoice/:id',
    component: Invoice
  },
  {
    path: '/sub/invoice/:id',
    component: SubInvoice
  },
  {
    path: '/furtherInfo',
    component: FurtherInfo
  },
  {
    path: '/task/:id/images',
    component: TaskImages
  },
  {
    path: '/registerQuickBooks',
    component: RegisterQuickBooks
  },
  {
    path: '/register',
    component: Register
  },
  {
    path: '/#*'
  }

]

export const router = new VueRouter({
  routes
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
  return Object.keys(User).length > 0;
}

router.beforeEach((to, from, next) => {

  checkThatCurrentJobExistsForRoutesThatNeedIt(to.path)

  if (goingToANonAuthorizedPage(to.path)) {
    next()
  } else {
    let UserLength = Object.keys(window.User).length;
    // let loggedIn = UserLength > 0;
    if (UserLength > 0) {
      next()
    } else {
      if (to.path !== '/') {
        next('/')
      } else {
        next()
      }
    }
  }

})

router.afterEach((to, from) => {
  if (window.location.pathname === '/' && Object.keys(User).length > 0) {
    window.location = '/home'
  }
})

var app = new Vue({
  mixins: [require('spark')],
  router,
  store,
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

    axios.post('/loadFeatures', {
      hello: 'world',
      location: location
    }).then((response) => {

      if (response.data.redirect) {
        if (response.data.redirect !== window.location.pathname) {
          window.location = response.data.redirect
        }
      } else {
        if (response.data.state[0]) {
          this.$store.commit('loadFeatures', response.data.state[0])
        }
        if (this.$store.state.user.user === '') {
          this.$store.commit('setUser', response.data.state[1])
        }
      }

    }).catch(function(error) {
      console.log(JSON.stringify(error))
    })
  }
})

require('./bootstrap')


function checkThatCurrentJobExistsForRoutesThatNeedIt(route) {
  console.log('checking that job exists in store', route)

  if (route == '/job/tasks' || route == '/job/add/task') {
    if (store.state.job.model === null) {
      router.push('/bids')
    }
  }
}
