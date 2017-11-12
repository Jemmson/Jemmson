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

require('./components/bootstrap');

import InitiateBid from './components/InitiateBid.vue';
Vue.component('initiate-bid', InitiateBid);

import Job from './components/job/Job.vue';
Vue.component('job', Job);

Spark.forms.register = {
  usertype: ''
};

var app = new Vue({
  mixins: [require('spark')]
});
