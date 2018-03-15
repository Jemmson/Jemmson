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

// register the plugin on vue
import Toasted from 'vue-toasted';

Vue.use(Toasted, {
  duration: 5000,
  theme: 'bubble',
})

import { store } from './vuex/index';
import Format from './classes/Format';
import Language from './classes/Language';
import GeneralContractor from './classes/GeneralContractor';
import SubContractor from './classes/SubContractor';
import Customer from './classes/Customer';
import User from './classes/User';
import TaskUtil from './classes/TaskUtil';

window.Format = Format;
window.Language = Language;
window.User = new User(Spark.state.user);
window.GeneralContractor = new GeneralContractor(Spark.state.user);
window.SubContractor = new SubContractor(Spark.state.user);
window.Customer = new Customer(Spark.state.user);
window.TaskUtil = new TaskUtil();

require('./components/bootstrap');

Spark.forms.register = {
  usertype: ''
};

var app = new Vue({
  mixins: [require('spark')],
  store
});