import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import BidDetails from '../../resources/assets/js/components/job/BidDetails'
import Vuetify from "vuetify"
import Vuex from "vuex";

import VueRouter from 'vue-router'
import Vue from 'vue'
import Register from "../../resources/assets/js/pages/Register";

require('./setup')

const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(Vuex)
global.Bus = new Vue()

describe('BidDetails', function () {

    let wrapper
    let vuetify
    let storeOptions
    let store

    beforeEach(() => {
        vuetify = new Vuetify()
        storeOptions = {
            commit: jest.fn(),
            actions: {
                // checkMobileNumber: jest.fn(() => Promise.resolve())
            },
            mutations: {
                setPhoneLoadingValue: jest.fn()
            },
            getters: {
                getMobileValidResponse: jest.fn()
            }
        }
        store = new Vuex.Store(storeOptions)
    })


    test.skip('test that if one task has the status changed then the approve button is not visible and all tasks that say ' +
        'WAITING FOR CUSTOMER APPROVAL now say WAITING ON BID SUBMISSION', () => {

        wrapper = shallowMount(BidDetails, {
            vuetify,
            localVue,
            store,
            mocks: {
                $router: {
                    push: jest.fn()
                }
            },
            props: {
                isCustomer: false,
                bid: {
                    job_tasks: [
                        {
                            task: {
                                name: 'Task 1',
                                qty: 1,
                                unit_price: 1,
                                cust_final_price: 123
                            },
                            job_task_status: [
                                {
                                    status: "initiated"
                                }, {
                                    status: "waiting_for_customer_approval"
                                }, {
                                    status: "changed"
                                },
                            ]
                        },
                        {
                            task: {
                                name: 'Task 2',
                                qty: 1,
                                unit_price: 234,
                                cust_final_price: 234
                            },
                            job_task_status: [
                                {
                                    status: "initiated"
                                }, {
                                    status: "waiting_for_customer_approval"
                                }
                            ]
                        },
                        {
                            task: {
                                name: 'Task 3',
                                qty: 1,
                                unit_price: 345,
                                cust_final_price: 345
                            },
                            job_task_status: [
                                {
                                    status: "initiated"
                                }, {
                                    status: "waiting_for_customer_approval"
                                }
                            ]
                        },
                    ],
                    job_status: [
                        {
                            status_number: 4
                        }
                    ]
                }
            }
        })

        !expect(wrapper.vm.getJobTasks()).toBe(undefined);
        console.log('isCustomer', wrapper.vm.isCustomer)
        console.log('bid', wrapper.vm.bid)
        console.log('jobtask length', wrapper.vm.getJobTasks())
        expect(!wrapper.vm.isCustomer && wrapper.vm.bid && wrapper.vm.getJobTasksLength() > 0).toBe(false)

        let general = wrapper.find('#general')
        console.log('general', general.text());

        let task1 = wrapper.find('#jobTaskStatus-0');
        let task2 = wrapper.find('#jobTaskStatus-1');
        let task3 = wrapper.find('#jobTaskStatus-2');
        let approvebtn = wrapper.find('#approveButton');
        let viewTasks = wrapper.find('#viewTasks');

        expect(task1.text()).toBe('CHANGED');
        expect(task2.text()).toBe('WAITING ON BID SUBMISSION');
        expect(task3.text()).toBe('WAITING ON BID SUBMISSION');
        expect(approvebtn.exists()).toBe(false);
        expect(viewTasks.exists()).toBe(false);

    })

})