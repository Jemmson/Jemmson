
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */

// /bid components
Vue.component('bid-details', require('./job/BidDetails.vue'));
Vue.component('general-contractor-bid-actions', require('./job/GeneralContractorBidActions.vue'));
Vue.component('bid-tasks', require('./job/BidTasks.vue'));
Vue.component('completed-tasks', require('./job/CompletedTasks.vue'));


// Shared Components
Vue.component('card', require('./shared/Card.vue'));
Vue.component('search-bar', require('./shared/SearchBar.vue'));

Vue.component('modal', require('./shared/Modal.vue'));
Vue.component('brand', require('./shared/Brand.vue'));
Vue.component('feedback', require('./shared/Feedback.vue'));
Vue.component('jem-footer', require('./shared/JemmsonFooter.vue'));


// require('./Autocomplete.vue');

// stripe
Vue.component('stripe', require('./stripe/Stripe'));
Vue.component('connect-with-stripe', require('./stripe/ConnectWithStripe'));
Vue.component('express-dashboard-stripe', require('./stripe/StripeExpressDashboard'));
Vue.component('pay-with-stripe', require('./stripe/PayWithStripe'));
Vue.component('subscripe-stripe', require('./stripe/Subscripe'));
Vue.component('signup-with-stripe', require('./stripe/SignupWithStripe'));


// forms
Vue.component('approve-bid', require('./job/ApproveBid'));
Vue.component('sub-invite-modal', require('./task/SubInviteModal'));
Vue.component('deny-task-modal', require('./task/DenyTaskModal'));
Vue.component('update-task-location-modal', require('./task/UpdateTaskLocationModal'));
Vue.component('bid-add-task', require('./task/BidAddTask.vue'));


Vue.component('task-images', require('./task/UploadTaskImages.vue'));





