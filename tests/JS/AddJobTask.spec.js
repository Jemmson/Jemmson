import {
    shallowMount
} from "@vue/test-utils";
import AddJobTask from '../../resources/assets/js/pages/AddJobTask';

require('./bootstrap');

describe('AddJobTask', () => {

    const wrapper = shallowMount(AddJobTask, {
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
        },
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('should show the current date in the start date on mount', function() {
        wrapper.setData({
            addNewTaskForm: {
                start_date: ''
            }
        })
        wrapper.vm.setDefaultStartDate();
        expect(wrapper.find({ref: 'start_date'}).element.value).toBe('2019-08-13');
    })

    it.skip('should show drop down of tasks once the substring has two letters of a task in the database', function() {

    })

    it.skip('should show update and add task and dont update and add task if the name matches the name  ' +
      'of a task in the database that is associated ot the contractor', function() {

    })

    it.skip('should not show names in the drop down if the substring matches the name of a task of ' +
      'another contractors task', function() {

    })

    it.skip('should not show names in the drop down if the user does a on blur and the substring does' +
      ' match names in the db of the contractor\'s tasks', function() {

    })

    it.skip('should show names in the drop down if the substring matches names in the database ' +
      'for the contractors tasks and the user does a on focus for the task description textbox', function() {

    })

    it.skip('should not show the contractors tasks if the task has already been added to the job', function() {

    })

    it.skip('should nameExistsInDB flag should be true after a drop down is selected', function() {

    })

    it.skip('should nameExistsInDB flag should be true if name is typed in the task description and the user ' +
      'does an on blur', function() {

    })

    it.skip('should trigger the fillTaskValues method if a drop down item is selected', function() {

    })

    it.skip('should not show drop down box if any other item is put in focus', function() {
        
    })

    it.skip('should show drop down box if task description loses focus and nut no other item in the vue component is selected', function() {

    })

    it.skip('should disable all inputs if task name is typed into the task description box and it matches the ' +
      'the name of an existing contractor task. there should be an error that is displayed saying ' +
      '`This Task Has Already Been Added`', function() {

    })

    it.skip('should show add button and no drop downs when the job task does not exist in the database and the task is not ' +
      'apart of the job', function() {
        /*
        * Given
        * no tasks are apart of the job
        * the added task does not exist in the database
        * */



        /*
        * Action
        * type in name of task
        * onblur
        * */


        /*
        * Result
        * there are no drop downs with the tasks name
        * add task button is shown
        * */
    })

    it.skip('should ', function() {
        
    })

});