import {createLocalVue, shallowMount, mount} from '@vue/test-utils'
import JobTask from '../../resources/assets/js/pages/JobTask'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Vue from 'vue'
import Vuetify from 'vuetify'
import Feedback from "../../resources/assets/js/components/shared/Feedback";

require('./setup')

const localVue = createLocalVue()

global.Bus = new Vue()

// global.Bus.$on('taskAdded', (data) => {
//   wrapper.vm.$emit('openSettings', data);
// });

global.Bus.$on('taskAdded')

localVue.use(Vuex)
localVue.use(VueRouter)
localVue.use(Vuetify, {})

const router = new VueRouter()

describe('JobTask', () => {

    test('change task button shows when job is not approved', () => {

        // user is a customer
        let wrapper = createWrapper();
        // job has not been approved by the customer
        wrapper.setData({
            jobTask: {
                task: {
                    contractor_id: 1
                }
            },
            user: {
                id: 2
            },
            job: {
                job_statuses: [
                    {
                        id: 1,
                        job_id: 1,
                        status: 'initiated',
                        status_number: 1,
                        deleted_at: null,
                        created_at: '2019-12-05 22:15:18',
                        updated_at: '2019-12-05 22:15:18'
                    },
                    {
                        id: 2,
                        job_id: 1,
                        status: 'in_progress',
                        status_number: 2,
                        deleted_at: null,
                        created_at: '2019-12-05 22:17:17',
                        updated_at: '2019-12-05 22:17:17'
                    },
                    {
                        id: 3,
                        job_id: 1,
                        status: 'sent',
                        status_number: 3,
                        deleted_at: null,
                        created_at: '2019-12-06 21:52:13',
                        updated_at: '2019-12-06 21:52:13'
                    }
                ]
            }
        })

        let btn = wrapper.find('#changeTask')

        expect(btn.exists()).toBe(true)

    })

    test('delete task button shows when job is not approved', () => {

        // user is a customer
        let wrapper = createWrapper();
        // job has not been approved by the customer
        wrapper.setData({
            jobTask: {
                task: {
                    contractor_id: 1
                }
            },
            user: {
                id: 2
            },
            job: {
                job_statuses: [
                    {
                        id: 1,
                        job_id: 1,
                        status: 'initiated',
                        status_number: 1,
                        deleted_at: null,
                        created_at: '2019-12-05 22:15:18',
                        updated_at: '2019-12-05 22:15:18'
                    },
                    {
                        id: 2,
                        job_id: 1,
                        status: 'in_progress',
                        status_number: 2,
                        deleted_at: null,
                        created_at: '2019-12-05 22:17:17',
                        updated_at: '2019-12-05 22:17:17'
                    },
                    {
                        id: 3,
                        job_id: 1,
                        status: 'sent',
                        status_number: 3,
                        deleted_at: null,
                        created_at: '2019-12-06 21:52:13',
                        updated_at: '2019-12-06 21:52:13'
                    }
                ]
            }
        })

        let btn = wrapper.find('#changeTask')

        expect(btn.exists()).toBe(true)

    })

    test('that the task name is displayed correctly when a task name exists', () => {

        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    name: 'Task 1'
                }
            }
        });

        let taskName = wrapper.find('#taskName');
        Vue.nextTick(() => {
            expect(taskName.text()).toBe('Task 1');
        });

    })

    test('that the task name is empty when a task name does not exist', () => {

        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    name: ''
                }
            }
        });

        let taskName = wrapper.find('#taskName');
        Vue.nextTick(() => {
            expect(taskName.text()).toBe('');
        });

    })

    test('if a job is not complete then show the uncompleted Task Start Date', () => {
        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    name: ''
                },
                job_task_statuses: [{
                    status: "initiated"
                }]

            }
        });

        Vue.nextTick(() => {
            expect(wrapper.find('#uncompletedJob').exists()).toBe(true)
            expect(wrapper.find('#completedJob').exists()).toBe(false)
        });

    })

    test('if a job is complete then show the completed Task Start Date', () => {
        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    name: ''
                },
                job: {
                    paid_with_cash_message: ''
                },
                job_task_statuses: [{
                    status: "paid"
                }]
            }
        });

        Vue.nextTick(() => {
            expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
            expect(wrapper.find('#completedJob').exists()).toBe(true)
        });

        wrapper.vm.$data.jobTask.job_task_statuses[0].status = 'sub finished work';

        Vue.nextTick(() => {
            expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
            expect(wrapper.find('#completedJob').exists()).toBe(true)
        });

        wrapper.vm.$data.jobTask.job_task_statuses[0].status = 'general finished work';

        Vue.nextTick(() => {
            expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
            expect(wrapper.find('#completedJob').exists()).toBe(true)
        });

    })

    test('test that if task is complete then you will show the start date in local time in the input window', () => {

        let wrapper = createWrapper();
        wrapper.setData({
            jobTask: {
                task: {
                    name: ''
                },
                start_date: '2020-03-05 01:05:02'
            }
        });

        //TODO: I seem to be unable to work with the input date so I have to test the method directly

        // document.querySelector('input[type="date"]').value = '2020-03-05';
        // document.querySelector('input[type="date"]').trigger('change');
        // let inputDate = wrapper.find('#inputDate')
        // let inputDate = wrapper.find('input[type="date"]')
        // inputDate.setValue('2020-03-05');
        // console.log('inputDate', inputDate.html());
        // console.log('inputDate', inputDate.text());
        // console.log('inputDate', document.querySelector('input[type="date"]').value);

        // Vue.nextTick(() => {
        //     console.log('start_date', wrapper.vm.$data.jobTask.start_date);
        //     console.log('inputDate', inputDate.html());
        //     console.log('inputDate', inputDate.valueOf());
        //     expect(inputDate.text()).toBe('03/23/2021')
        // });

        expect(wrapper.vm.setStartDate(wrapper.vm.$data.jobTask)).toBe('2020-03-04')



    })

    test.skip('that I display the minimum price on the screen if the generals price is not high enough to cover the subs fee and the application fee', () => {

        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    contractor_id: 1
                }
            },
            user: {
                id: 2
            },
            job: {
                job_statuses: [
                    {
                        id: 1,
                        job_id: 1,
                        status: 'initiated',
                        status_number: 1,
                        deleted_at: null,
                        created_at: '2019-12-05 22:15:18',
                        updated_at: '2019-12-05 22:15:18'
                    },
                    {
                        id: 2,
                        job_id: 1,
                        status: 'in_progress',
                        status_number: 2,
                        deleted_at: null,
                        created_at: '2019-12-05 22:17:17',
                        updated_at: '2019-12-05 22:17:17'
                    },
                    {
                        id: 3,
                        job_id: 1,
                        status: 'sent',
                        status_number: 3,
                        deleted_at: null,
                        created_at: '2019-12-06 21:52:13',
                        updated_at: '2019-12-06 21:52:13'
                    }
                ]
            }
        })


        let contractorPrice = 0;
        let totalTaskPrice = wrapper.find('#totalTaskPrice')
        let minimumPrice = contractorPrice * wrapper.vm.appfees()
        expect(totalTaskPrice.text()).toBe(minimumPrice);

    })

    function createWrapper() {
        let actions
        let mutations
        let getters
        let state
        let store
        let storeOptions

        actions = {}
        mutations = {}
        getters = {}
        state = {
            job: {
                model: {
                    job_tasks: []
                }
            }
        }

        storeOptions = {
            actions: actions,
            mutations: mutations,
            getters: getters,
            state: state
        }
        store = new Vuex.Store(storeOptions)

        return shallowMount(JobTask, {
            store,
            router,
            localVue,
            mocks: {
                $store: {
                    commit: jest.fn(),
                    state: {
                        job: {
                            model: {
                                job_tasks: []
                            }
                        }
                    }
                }
            }
        })
    }

})