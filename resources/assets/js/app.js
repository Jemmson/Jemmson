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

window.Pusher = require ('pusher-js');

window.Echo = new Echo ({
  broadcaster: 'pusher',
  key: '07c3b89aa6d0a0206b23',
  cluster: 'mt1',
  encrypted: true
});

require ('spark-bootstrap');

import VueRouter from 'vue-router';

Vue.use (VueRouter);

// register the plugin on vue
import Toasted from 'vue-toasted';

Vue.use (Toasted, {
  duration: 5000,
  theme: 'bubble',
})

import VuePaginate from 'vue-paginate'

Vue.use (VuePaginate)

import {
  store
} from './vuex/index';
import Format from './classes/Format';
import Language from './classes/Language';
import GeneralContractor from './classes/GeneralContractor';
import SubContractor from './classes/SubContractor';
import Customer from './classes/Customer';
import User from './classes/User';
import TaskUtil from './classes/TaskUtil';


import Home from './pages/Home';
import PublicHome from './pages/PublicHome';
import Jobs from './pages/Jobs';
import Job from './pages/Job';
import InitiateBid from './pages/InitiateBid';
import Tasks from './pages/Tasks';
import Invoices from './pages/Invoices';
import Invoice from './pages/Invoice';
import FurtherInfo from './pages/FurtherInfo';
import TaskImages from './pages/TaskImages';


window.Format = Format;
window.Language = Language;
window.User = new User (Spark.state.user);
window.GeneralContractor = new GeneralContractor (Spark.state.user);
window.SubContractor = new SubContractor (Spark.state.user);
window.Customer = new Customer (Spark.state.user);
window.TaskUtil = new TaskUtil ();
window.autocomplete = {};

require ('./components/bootstrap');

Spark.forms.register = {
  usertype: ''
};


// vue routes
const routes = [{
  path: '/bids',
  component: Jobs
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
    path: '/furtherInfo',
    component: FurtherInfo
  },
  {
    path: '/task/:id/images',
    component: TaskImages
  },
  {
    path: '/#*'
  }

]

const router = new VueRouter ({
  routes
})

router.beforeEach ((to, from, next) => {
  // $('.navbar-collapse').collapse('hide');
  // console.log(to.path);
  // if (Spark.state.user === null) {
  //   location.href = '/login';
  // }

  if (to.path === '/furtherInfo') {
    let customer = Spark.state.user.customer;
    let contractor = Spark.state.user.contractor;
    if ((customer !== null && customer.location_id !== null) || (contractor !== null && contractor.location_id !== null)) {
      console.log ('wtf');
      next ('/home');
    }
  }
  if (to.path !== '/furtherInfo' && to.path !== '/#' && to.path !== '/' && from.path !== '/furtherInfo') {
    let customer = Spark.state.user.customer;
    let contractor = Spark.state.user.contractor;
    if (Spark.state.user === null) {
      location.href = '/login';
    }
    if ((customer !== null && customer.location_id === null) || (contractor !== null && contractor.location_id === null)) {
      console.log ('to further info');
      next ('/furtherInfo');
    } else {
      switch (to.path) {
        case '/initiate-bid':
          if (Spark.state.user.usertype === 'customer') {
            next ('/home');
          } else {
            next ();
          }
          break;
        case '/tasks':
          if (Spark.state.user.usertype === 'customer') {
            next ('/home');
          } else {
            next ();
          }
          break;
        default:
          next ();
          break;
      }
    }
  } else {
    next ();
  }
});


var app = new Vue ({
  mixins: [require ('spark')],
  router,
  store
});


require ('./bootstrap');
