
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */

require('./../spark-components/bootstrap');

require('./home');
require('./registration');
require('./InitiateBid.vue');
require('./job/Job.vue');
require('./job/Task.vue');
require('./job/InfoLabel.vue');
require('./job/Contracts.vue');
require('./job/CurrentTasksForJob.vue');

Vue.component('subcontractor-tasks', require('./task/SubContractorTasks.vue'));
Vue.component('modal', require('./shared/Modal.vue'));

// require('./Autocomplete.vue');
