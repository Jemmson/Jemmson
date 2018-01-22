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

require('spark-bootstrap');

import Language from './classes/Language';
import GeneralContractor from './classes/GeneralContractor';
import SubContractor from './classes/SubContractor';
import Customer from './classes/Customer';
import User from './classes/User';

window.Language = Language;
window.GeneralContractor = GeneralContractor;
window.SubContractor = SubContractor;
window.Customer = Customer;
window.User = new User(Spark.state.user);

require('./components/bootstrap');

import InitiateBid from './components/InitiateBid.vue';
Vue.component('initiate-bid', InitiateBid);

import Job from './components/job/Job.vue';
Vue.component('job', Job);

import Task from './components/job/Task.vue';
Vue.component('task', Task);

import InfoLabel from './components/job/InfoLabel.vue';
Vue.component('infoLabel', InfoLabel);

import Contracts from './components/job/Contracts.vue';
Vue.component('contracts', Contracts);

import CurrentTasksForJob from './components/job/CurrentTasksForJob.vue';
Vue.component('currentTasksForJob', CurrentTasksForJob);

import { store } from './vuex/index';

Spark.forms.register = {
  usertype: ''
};

var app = new Vue({
  mixins: [require('spark')],
  store
});


// register the plugin on vue
import Toasted from 'vue-toasted';

Vue.use(Toasted, {
  duration: 5000,
  theme: 'bubble',
})