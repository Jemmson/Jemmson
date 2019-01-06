import {
    shallowMount
} from "@vue/test-utils";
import BidAddTask from '../../../../resources/assets/js/components/task/BidAddTask';

require('../../bootstrap');

describe('BidAddTask', () => {
    const wrapper = shallowMount(BidAddTask, {
        propsData: {
            isCustomer: false,
            customerName: 'Jane Doe',
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: '2018-08-01 10:58:37',
                status: 'bid.initiated',
                contractor_id: 1,
                bid_price: 99.00,
                declined_message: null,
                location: null
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('should show drop down of tasks once the substring has two letters of a task in the database', function() {

    })

    it('should show update and add task and dont update and add task if the name matches the name  ' +
      'of a task in the database that is associated ot the contractor', function() {

    })

    it('should not show names in the drop down if the substring matches the name of a task of ' +
      'another contractors task', function() {

    })

    it('should not show names in the drop down if the user does a on blur and the substring does' +
      ' match names in the db of the contractor\'s tasks', function() {

    })

    it('should show names in the drop down if the substring matches names in the database ' +
      'for the contractors tasks and the user does a on focus for the task description textbox', function() {

    })

    it('should not show the contractors tasks if the task has already been added to the job', function() {

    })

    it('should nameExistsInDB flag should be true after a drop down is selected', function() {

    })

    it('should nameExistsInDB flag should be true if name is typed in the task description and the user ' +
      'does an on blur', function() {

    })

    it('should trigger the fillTaskValues method if a drop down item is selected', function() {

    })

    it('should not show drop down box if any other item is put in focus', function() {
        
    })

    it('should show drop down box if task description loses focus and nut no other item in the vue component is selected', function() {
        
    })

});