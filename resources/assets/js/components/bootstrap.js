
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

// /bid components
Vue.component('bidlist', require('./job/BidList.vue'));
Vue.component('bid', require('./job/Bid.vue'));
Vue.component('bid-details', require('./job/BidDetails.vue'));
Vue.component('general-contractor-bid-actions', require('./job/GeneralContractorBidActions.vue'));
Vue.component('bid-tasks', require('./job/BidTasks.vue'));
Vue.component('bid-task', require('./job/BidTask.vue'));
Vue.component('bid-add-task', require('./job/BidAddTask.vue'));

Vue.component('modal', require('./shared/Modal.vue'));

// require('./Autocomplete.vue');

// stripe
Vue.component('stripe', require('./stripe/Stripe'));
Vue.component('connect-with-stripe', require('./stripe/ConnectWithStripe'));
Vue.component('express-dashboard-stripe', require('./stripe/StripeExpressDashboard'));
Vue.component('pay-with-stripe', require('./stripe/PayWithStripe'));
Vue.component('subscripe-stripe', require('./stripe/Subscripe'));
Vue.component('signup-with-stripe', require('./stripe/SignupWithStripe'));

