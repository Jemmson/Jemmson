import {createLocalVue, shallowMount} from '@vue/test-utils'
import CompletedTasks from '../../resources/assets/js/components/job/CompletedTasks'
import Vuetify from "vuetify";
import VueRouter from "vue-router";
import Vue from "vue";
import Vuex from 'vuex'

global.User = {
    getAllPayableTasks(jobTasks){
        return [{}];
    },
    isContractor(){
        return true;
    },
    isCustomer(){}
}

require('./setup');

Vue.use(Vuetify);
const localVue = createLocalVue();
localVue.use(Vuetify, {});
localVue.use(VueRouter);
localVue.use(Vuex)

describe('CompletedTasks', () => {
    let wrapper
    let vuetify;
    vuetify = new Vuetify();
    let storeOptions = {
        actions: {},
        mutations: {},
        getters: {
            // jobTasks: jest.fn()
        }
    };
    let store = new Vuex.Store(storeOptions);

    beforeEach(() => {
    })

    test('is a Vue instance', () => {
        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 14,
                                    "job_task_id": 4,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:42:43",
                                    "updated_at": "2020-02-24 01:42:43"
                                },
                                {
                                    "id": 16,
                                    "job_task_id": 4,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 18,
                                    "job_task_id": 4,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 20,
                                    "job_task_id": 4,
                                    "status": "sub_finished_work",
                                    "status_number": 8,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:49:18",
                                    "updated_at": "2020-02-24 01:49:18"
                                },
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "approved_subs_work",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 15,
                                    "job_task_id": 5,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:43:02",
                                    "updated_at": "2020-02-24 01:43:02"
                                },
                                {
                                    "id": 17,
                                    "job_task_id": 5,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 19,
                                    "job_task_id": 5,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "general_finished_work",
                                    "status_number": 7,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });
        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('that there are 2 payable tasks out of 3', () => {

        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 14,
                                    "job_task_id": 4,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:42:43",
                                    "updated_at": "2020-02-24 01:42:43"
                                },
                                {
                                    "id": 16,
                                    "job_task_id": 4,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 18,
                                    "job_task_id": 4,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 20,
                                    "job_task_id": 4,
                                    "status": "sub_finished_work",
                                    "status_number": 8,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:49:18",
                                    "updated_at": "2020-02-24 01:49:18"
                                },
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "approved_subs_work",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 15,
                                    "job_task_id": 5,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:43:02",
                                    "updated_at": "2020-02-24 01:43:02"
                                },
                                {
                                    "id": 17,
                                    "job_task_id": 5,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 19,
                                    "job_task_id": 5,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "general_finished_work",
                                    "status_number": 7,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });

        expect(wrapper.vm.show).toBe(true)
    })

    test('when neither task is checked then the total amount should be 100', () => {
        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 14,
                                    "job_task_id": 4,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:42:43",
                                    "updated_at": "2020-02-24 01:42:43"
                                },
                                {
                                    "id": 16,
                                    "job_task_id": 4,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 18,
                                    "job_task_id": 4,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 20,
                                    "job_task_id": 4,
                                    "status": "sub_finished_work",
                                    "status_number": 8,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:49:18",
                                    "updated_at": "2020-02-24 01:49:18"
                                },
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "approved_subs_work",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 15,
                                    "job_task_id": 5,
                                    "status": "initiated",
                                    "status_number": 1,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:43:02",
                                    "updated_at": "2020-02-24 01:43:02"
                                },
                                {
                                    "id": 17,
                                    "job_task_id": 5,
                                    "status": "waiting_for_customer_approval",
                                    "status_number": 2,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:47:39",
                                    "updated_at": "2020-02-24 01:47:39"
                                },
                                {
                                    "id": 19,
                                    "job_task_id": 5,
                                    "status": "approved_by_customer",
                                    "status_number": 6,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:48:17",
                                    "updated_at": "2020-02-24 01:48:17"
                                },
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "general_finished_work",
                                    "status_number": 7,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });
        expect(wrapper.find("#cctotal").text()).toBe("Total: $ " + 100);
        
    })

    test('that if some of the tasks are paid for then the class of yellow will be applied and the text will say ' +
        'Partial Payment Completed', async () => {

        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "paid",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "general_finished_work",
                                    "status_number": 7,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });

        wrapper.setData({
            paymentSucceeded: true
        });

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'fullPayment'}).exists()).toBe(false)
        expect(wrapper.find({ref: 'partialPayment'}).exists()).toBe(true)

    })

    test('that if all of the tasks are paid for then the class of green will be applied and the text will say ' +
        'Payment Succeeded', async () => {

        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "paid",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "paid",
                                    "status_number": 7,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });

        wrapper.setData({
            paymentSucceeded: true
        });

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'partialPayment'}).exists()).toBe(false)
        expect(wrapper.find({ref: 'fullPayment'}).exists()).toBe(true)

    })

    test.only('test that the v-dialog opens when the deny button is clicked', async () => {

        wrapper = shallowMount(CompletedTasks, {
            localVue,
            vuetify,
            store,
            stubs: {
                Stripe: true
            },
            mocks: {
                methods: {
                    addJobTaskToExcludedList: jest.fn()
                }
            },
            computed: {
                jobTasks () {
                    return this.bid.job_tasks
                }
            },
            propsData: {
                bid: {
                    "contractor": {
                        "id": 1,
                        "contractor": {
                            "stripe_id": "acct_1CENK6Bp6bf1LkLw"
                        }
                    },
                    job_tasks: [
                        {
                            "id": 1,
                            "job_task_status": [
                                {
                                    "id": 21,
                                    "job_task_id": 4,
                                    "status": "paid",
                                    "status_number": 10,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:50:32",
                                    "updated_at": "2020-02-24 01:50:32"
                                }
                            ],
                            cust_final_price: 50
                        },
                        {
                            "id": 2,
                            "job_task_status": [
                                {
                                    "id": 22,
                                    "job_task_id": 5,
                                    "status": "general_finished_work",
                                    "status_number": 8,
                                    "sent_on": null,
                                    "deleted_at": null,
                                    "created_at": "2020-02-24 01:51:05",
                                    "updated_at": "2020-02-24 01:51:05"
                                }
                            ],
                            cust_final_price: 50
                        }
                    ]
                }
            }
        });

        wrapper.setData({
            paymentSucceeded: true,
            denyDialog: false
        });


        await wrapper.vm.$nextTick()
        const btn = wrapper.find({ref: 'deny2'})
        btn.trigger('click')
        console.log('data', wrapper.vm.$data)
        console.log('denyDialog', wrapper.vm.$data.denyDialog)
        expect(wrapper.vm.$data.denyDialog).toBe(true)

    })

    test.skip('that when one of the two tasks is excluded then the total for the customer only show the total for the nonexcluded tasks', () => {

        //TODO: DONT KNOW WHY document.getElementById('exclude-' + jobTask.id).checked
        // is not recognized in vue-test-utils. it works fine in the browser but not n here
        // when I put this in debug mode then i dont seem to get the dom at all. interesting

        let cb = wrapper.find('#exclude-1');

        // debugger;
        cb.trigger('click');

        let cctotal = wrapper.find("#cctotal");
        expect(cctotal.text()).toBe("Total: $ " + 56.45);
    })

    test.skip('if one task is selected and no payable tasks are paid then the total will reflect the payable tasks + fees', () => {

        wrapper.setData({
           excluded: {
               1: false,
               2: true
           }
        });

        // const jobTask = wrapper.vm.bid.job_tasks;
        // wrapper.vm.addJobTaskToExcludedList(jobTask[1]);

        expect(wrapper.find("#cctotal").text()).toBe("Total: $ " + 54.25);

    })

})