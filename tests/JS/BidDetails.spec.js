import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import BidDetails from '../../resources/assets/js/components/job/BidDetails'
import Vuetify from "vuetify"
import Vuex from "vuex";
import VueRouter from 'vue-router'


import Vue from 'vue'

global.Spark = {
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: ''
            },
            usertype: 'customer'
        }
    }
}

require('./setup')

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuetify)
localVue.use(Vuex)
global.Bus = new Vue()

const router = new VueRouter()

global.User = {
    getAllPayableTasks(jobTasks) {
        return [{}];
    },
    isContractor() {
        return true;
    },
    isCustomer() {
    },
    contractor: {}
}

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
                setUser: jest.fn()
            },
            getters: {
                getMobileValidResponse: jest.fn()
            }
        }
        store = new Vuex.Store(storeOptions)
    })

    test('test that if one task has the status changed then the approve button is not visible and all tasks that say ' +
        'WAITING FOR CUSTOMER APPROVAL now say WAITING ON BID SUBMISSION', async () => {

        wrapper = shallowMount(BidDetails, {
            vuetify,
            localVue,
            store,
            propsData: {
                isCustomer: true,
                bid: {
                    job_tasks: [
                        {
                            unit_price: 1,
                            cust_final_price: 123,
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
                            unit_price: 234,
                            cust_final_price: 234,
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
                            unit_price: 345,
                            cust_final_price: 345,
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

        wrapper.setData({
            show: {
                jobTask: true
            }
        })

        await wrapper.vm.$nextTick()

        let task1 = wrapper.find({ref: 'jobTaskStatus-0'});
        let task2 = wrapper.find({ref: 'jobTaskStatus-1'});
        let task3 = wrapper.find({ref: 'jobTaskStatus-2'});
        let approvebtn = wrapper.find('#approveButton');
        let viewTasks = wrapper.find('#viewTasks');

        expect(task1.text()).toBe('changed');
        expect(task2.text()).toBe('WAITING ON BID SUBMISSION');
        expect(task3.text()).toBe('WAITING ON BID SUBMISSION');
        expect(approvebtn.exists()).toBe(false);
        expect(viewTasks.exists()).toBe(true);

    })

    test('location button should only show if the user is a contractor', () => {

        // TODO: test fails but it works in the ui and all for the requirements for it be successful test seem to
        // be there but I dont know why it is failing.

        const wrapper = shallowMount(BidDetails, {
            localVue,
            propsData: {

                bid: {
                    job_status: [{
                        status_number: 1,
                    }],
                    status: 'bid.sent',
                    job_tasks: [
                        {
                            unit_price: 1,
                            cust_final_price: 123,
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
                        }
                    ],
                },
                isCustomer: true
            }
        });

        wrapper.setData({
            show: {
                jobStepper: false,
                details: false,
                jobTask: false,
                location: true,
                images: false
            },
        });

        expect(wrapper.find({ref: 'location'}).exists()).toBe(false);
    })

    test('that if a customer looks at a bid that has been initiated or in progress' +
        ' then the subtitle for details should say' +
        ' PLEASE WAIT UNTIL YOUR CONTRACTOR SUBMITS BID', async () => {

        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            router,
            store,
            stubs: [
                'approve-bid'
            ],
            propsData: {
                bid: {
                    status: 'bid.sent',
                    job_name: 'Task1',
                    agreed_start_date: null,
                    job_tasks: [
                        {
                            unit_price: 1,
                            cust_final_price: 123,
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
                        }
                    ],
                    contractor: {
                        contractor: {
                            company_name: 'ACME'
                        }
                    },
                    job_status: [
                        {
                            status: 'in_progress'
                        }
                    ]
                },
                isCustomer: true
            }
        });

        wrapper.setData({
            show: {
                details: true
            }
        })

        expect(wrapper.find({ref: 'details-subtitle'})
            .text()).toBe('PLEASE WAIT UNTIL YOUR CONTRACTOR SUBMITS BID')

    })

    test('i am a contractor and submit bid should be disabled if no tasks have been added', async () => {

        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_status: [{
                        status_number: 1,
                    }],
                    job_tasks: [],
                },
                isCustomer: false
            }
        });

        await wrapper.vm.$nextTick()

        expect(wrapper.vm.noJobTasks()).toBe(true)
    })

    test.skip('that Job Tasks nav button is disabled if there are no job tasks', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_status: [{
                        status_number: 1,
                    }],
                    job_tasks: [],
                },
                isCustomer: false
            }
        });

        wrapper.setData({
            show: {
                jobStepper: false,
                details: false,
                jobTask: true,
                location: false,
                images: false
            },
        });

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'jobTaskNavButton'})
            .attributes().disabled).toBe('disabled')

    })

    test.skip('that Job Tasks nav button is not disabled if there are job tasks', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_status: [{
                        status_number: 1,
                    }],
                    job_tasks: [{
                        unit_price: 1,
                        cust_final_price: 123,
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
                    }],
                },
                isCustomer: false
            }
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'jobTaskNavButton'})
            .attributes().disabled).toBe(undefined)

    })

    test.skip('that Job Tasks nav button text says Need Tasks if there are no job tasks', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_status: [{
                        status_number: 1,
                    }],
                    job_tasks: [],
                },
                isCustomer: false
            }
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'jobTaskNavButton'})
            .text()).toBe('Need Tasks')

    })

    test.skip('that Job Tasks nav button text says Job Tasks if there are job tasks', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_tasks: [{
                        unit_price: 1,
                        cust_final_price: 123,
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
                    }],
                },
                isCustomer: false
            }
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'jobTaskNavButton'})
            .text()).toBe('View Tasks (1) / Add A Sub')

    })

    test.skip('Job Tasks button should show the correct number of tasks in the name if Job Tasks exist', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_tasks: [
                        {
                            unit_price: 1,
                            cust_final_price: 123,
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
                            unit_price: 1,
                            cust_final_price: 123,
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
                            unit_price: 1,
                            cust_final_price: 123,
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
                        }
                    ],
                },
                isCustomer: false
            }
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'jobTaskNavButton'})
            .text()).toBe('View Tasks (3) / Add A Sub')

    })

    test.skip('test that there is a counter next to the images name if there are atleast one image', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    images: [
                        {},
                        {},
                        {}
                    ]
                },
                isCustomer: false
            }
        });

        wrapper.setData({
            show: {
                jobStepper: false,
                details: false,
                jobTask: false,
                location: false,
                images: true
            },
        });

        console.log('numberOfImages', wrapper.vm.getNumberOfImages())

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'imagesNavButton'})
            .text()).toBe('Images (3)')

    })

    test.skip('test that there is not a counter next to the images name if there are no images', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    images: []
                },
                isCustomer: false
            }
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'imagesNavButton'})
            .text()).toBe('Images')

    })

    test('that the task button is red if there are more than one task', async () => {
        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    job_tasks: [
                        {
                            task: {
                                name: 'Task 1',
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
                                name: 'Task 3',
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
                        }
                    ],
                    job_statuses: [
                        {
                            status: 'sdasads'
                        }
                    ]
                },
                isCustomer: false
            }
        });

        wrapper.setData({
            show: {
                jobStepper: false,
                details: false,
                jobTask: true,
                location: false,
                images: false
            },
        });

        await wrapper.vm.$nextTick()
        expect(wrapper
            .find({ref: 'job-add-task-icon'})
            .attributes().class).toBe('v-icon notranslate nav-btn-position v-icon--link mdi mdi-plus-thick theme--light red--text')

    })
    
    test('that assessor button appears for the address if the address is from AZ', async () => {

        const wrapper = mount(BidDetails, {
            localVue,
            vuetify,
            store,
            router,
            stubs: [
                'approve-bid',
                'sub-invite-modal'
            ],
            propsData: {
                bid: {
                    location_id: '1',
                    location: {
                        state: 'AZ'
                    }
                },
                isCustomer: false
            }
        });

        wrapper.setData({
            show: {
                jobStepper: false,
                details: false,
                jobTask: false,
                location: true,
                images: false
            },
        });

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'assessor'}).exists()).toBe(true)

        wrapper.setProps({
            bid: {
                location_id: '1',
                location: {
                    state: 'CA'
                }
            },
        })

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'assessor'}).exists()).toBe(false)
        
    })

})