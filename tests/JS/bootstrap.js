import Vue from 'vue';
import User from '../../resources/assets/js/classes/User';
import Format from '../../resources/assets/js/classes/Format';
import Language from '../../resources/assets/js/classes/Language';
import GeneralContractor from '../../resources/assets/js/classes/GeneralContractor';
import SubContractor from '../../resources/assets/js/classes/SubContractor';
import Customer from '../../resources/assets/js/classes/Customer';
import TaskUtil from '../../resources/assets/js/classes/TaskUtil';

global.User = new User({});
global.Format = Format;
global.Language = Language;
global.GeneralContractor = GeneralContractor;
global.SubContractor = SubContractor;
global.Customer = Customer;
global.TaskUtil = TaskUtil;

// Vue.use(Toasted, {
//     duration: 5000,
//     theme: 'bubble',
// });
Vue.toasted = {};
Vue.toasted.success = () => {
    console.log('success toast');
    
};

Vue.toasted.error = () => {
    console.log('error toast');

};

global.Vue = Vue;