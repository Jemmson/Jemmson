import {createLocalVue, shallowMount, mount} from '@vue/test-utils'
import JobTask from '../../resources/assets/js/pages/JobTask'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Vue from 'vue'
import Vuetify from 'vuetify'
import Feedback from "../../resources/assets/js/components/shared/Feedback";
import AddJobTask from "../../resources/assets/js/pages/AddJobTask";

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
require('./stripeSetup')

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

    test.skip('change task button shows when job is not approved', () => {

        // TODO: removing this feature for the time being until I can work out its workflow a little better

        // user is a customer
        let wrapper = createWrapper();
        // job has not been approved by the customer
        wrapper.setData({
            jobTask: {
                task: {
                    contractor_id: 1,
                    name: ''
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

    test('delete task button shows when job is not approved', async () => {

        let wrapper = createWrapper();
        wrapper.setData({
            jobTask: {
                contractor_id: 1,
                job: {
                    contractor_id: 1
                },
                qty: 0,
                task: {
                    name: '',
                    contractor_id: 1
                },
                job_task_statuses: [
                    {
                        status_number: 1
                    }
                ],
                bid_contractor_job_tasks: [
                    {
                        contractor: {
                            contractor: {
                                company_name: 'Acme'
                            }
                        }
                    }
                ]
            },
            user: {
                id: 1
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

        await wrapper.vm.$nextTick()

        let btn = wrapper.find({ref: 'generalCanDeleteTask'})

        expect(btn.exists()).toBe(true)

    })

    test('that the task name is displayed correctly when a task name exists', async () => {

        let wrapper = createWrapper();

        wrapper.setData({
            show: {
                details: true
            },
            jobTask: {
                task: {
                    name: 'Task 1'
                },
                job: {
                    contractor_id: 1
                },
                contractor_id: 1
            }
        });

        await wrapper.vm.$nextTick()

        let taskName = wrapper.find('#taskName');

        expect(taskName.text()).toBe('Task 1');

    })

    test('that the task name is empty when a task name does not exist', async () => {

        let wrapper = createWrapper();

        wrapper.setData({
            show: {
                details: true
            },
            jobTask: {
                task: {
                    name: ''
                },
                job: {
                    contractor_id: 1
                },
                contractor_id: 1
            }
        });

        await wrapper.vm.$nextTick()

        let taskName = wrapper.find('#taskName');
        expect(taskName.text()).toBe('');


    })

    test.skip('if a job is not complete then show the uncompleted Task Start Date', async () => {
        let wrapper = createWrapper();

        wrapper.setData({
            show: {
                details: true
            },
            jobTask: {
                task: {
                    name: ''
                },
                job: {
                    contractor_id: 1
                },
                contractor_id: 1,
                job_task_statuses: [{
                    status: "initiated"
                }]

            }
        });

        await wrapper.vm.$nextTick()

        expect(wrapper.find('#uncompletedJob').exists()).toBe(true)
        expect(wrapper.find('#completedJob').exists()).toBe(false)


    })

    test.skip('if a job is complete then show the completed Task Start Date', async () => {
        let wrapper = createWrapper();

        wrapper.setData({
            show: {
                details: true
            },
            jobTask: {
                task: {
                    name: ''
                },
                job: {
                    paid_with_cash_message: '',
                    contractor_id: 1
                },
                job_task_statuses: [{
                    status: "paid"
                }],
                contractor_is: 1
            }
        });

        await wrapper.vm.$nextTick()

        expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
        expect(wrapper.find('#completedJob').exists()).toBe(true)

        wrapper.vm.$data.jobTask.job_task_statuses[0].status = 'sub finished work';

        expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
        expect(wrapper.find('#completedJob').exists()).toBe(true)

        wrapper.vm.$data.jobTask.job_task_statuses[0].status = 'general finished work';

        expect(wrapper.find('#uncompletedJob').exists()).toBe(false)
        expect(wrapper.find('#completedJob').exists()).toBe(true)


    })

    test.skip('test that if task is complete then you will show the start date in local time in the input window', async () => {

        let wrapper = createWrapper();
        wrapper.setData({
            jobTask: {
                task: {
                    name: ''
                },
                job: {
                    contractor_id: 1
                },
                contractor_id: 1,
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

        await wrapper.vm.$nextTick()

        expect(wrapper.vm.setStartDate(wrapper.vm.$data.jobTask)).toBe('2020-03-04')


    })

    test.skip('that I display the minimum price on the screen if the generals price is not ' +
        'high enough to cover the subs fee and the application fee', async () => {

        let wrapper = createWrapper();

        wrapper.setData({
            jobTask: {
                task: {
                    name: '',
                    contractor_id: 1
                }
            },
            user: {
                id: 2
            },
            contractor_id: 1,
            job: {
                contractor_id: 1,
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

        await wrapper.vm.$nextTick()

        let contractorPrice = 0;
        let totalTaskPrice = wrapper.find('#totalTaskPrice')
        let minimumPrice = contractorPrice * wrapper.vm.appfees()
        expect(totalTaskPrice.text()).toBe(minimumPrice);

    })

    test('the sub panel should show if ' +
        'there are subs for the given task ' +
        'the user is a general contractor', async () => {

        let wrapper = createWrapper();
        wrapper.setData({
            show: {
                subPanel: true,
            },
            jobTask: {
                contractor_id: 1,
                job: {
                    contractor_id: 1,
                    payment_type: 'cash'
                },
                qty: 0,
                task: {
                    name: '',
                    contractor_id: 1
                },
                job_task_statuses: [
                    {
                        status_number: 1
                    }
                ],
                bid_contractor_job_tasks: [
                    {
                        contractor: {
                            contractor: {
                                company_name: 'Acme'
                            }
                        }
                    }
                ]
            },
            user: {
                id: 1,
                usertype: 'contractor'
            },
            contractor_id: 1,
            job: {
                contractor_id: 1,
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

        wrapper.vm.$store.state.job.model.status = 'job.initiated'

        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'subPanelSection'}).exists()).toBe(true)

    })

    test('should show a counter after the images text if there are more than one image', async () => {
        let wrapper = createWrapper();
        wrapper.setData({
            show: {
                subPanel: true,
            },
            jobTask: {
                images: [{}, {}],
                contractor_id: 1,
                job: {
                    contractor_id: 1,
                    payment_type: 'cash'
                },
                qty: 0,
                task: {
                    name: '',
                    contractor_id: 1
                },
                job_task_statuses: [
                    {
                        status_number: 1
                    }
                ],
                bid_contractor_job_tasks: [
                    {
                        contractor: {
                            contractor: {
                                company_name: 'Acme'
                            }
                        }
                    }
                ]
            },
            user: {
                id: 1,
                usertype: 'contractor'
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

        wrapper.vm.$store.state.job.model.status = 'job.initiated'

        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'jobTaskNavImage'}).text()).toBe('mdi-image-edit (2)')

    })

    test('should not show a counter after the images text if there are no images', async () => {
        let wrapper = createWrapper();
        wrapper.setData({
            show: {
                subPanel: true,
            },
            jobTask: {
                images: [],
                contractor_id: 1,
                job: {
                    contractor_id: 1,
                    payment_type: 'cash'
                },
                qty: 0,
                task: {
                    name: '',
                    contractor_id: 1
                },
                job_task_statuses: [
                    {
                        status_number: 1
                    }
                ],
                bid_contractor_job_tasks: [
                    {
                        contractor: {
                            contractor: {
                                company_name: 'Acme'
                            }
                        }
                    }
                ]
            },
            user: {
                id: 1,
                usertype: 'contractor'
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

        wrapper.vm.$store.state.job.model.status = 'job.initiated'

        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'jobTaskNavImage'}).text()).toBe('mdi-image-edit')

    })

    test('should show a counter after the show subs text if there are more than one sub', async () => {
        let wrapper = createWrapper();
        wrapper.setData({
            show: {
                subPanel: true,
            },
            jobTask: {
                images: [{}, {}],
                contractor_id: 1,
                job: {
                    contractor_id: 1,
                    payment_type: 'cash'
                },
                qty: 0,
                task: {
                    name: '',
                    contractor_id: 1
                },
                job_task_statuses: [
                    {
                        status_number: 1
                    }
                ],
                bid_contractor_job_tasks: [
                    {
                        contractor: {
                            contractor: {
                                company_name: 'Acme'
                            }
                        }
                    }
                ]
            },
            user: {
                id: 1,
                usertype: 'contractor'
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

        wrapper.vm.$store.state.job.model.status = 'job.initiated'

        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'subsNavButton'}).text()).toBe('mdi-face (1)')

    })

    test.skip('that if the sub price is higher than the generals price an error will show', async () => {

        let wrapper = createWrapper();

        wrapper.setData({
            show: {
                prices: true
            },
            jobTask: {
                cust_final_price: 100,
                contractor_id: 1,
                task: {
                    name: '',
                },
                job: {
                    contractor_id: 1
                }
            },
            errors: {
                subPriceTooHigh: {
                    exists: false,
                    message: 'Sub Price Can Not Be Higher Than Contractor Price'
                },
                general: {
                    errorExists: true,
                    message: 'Errors exist on page. Please review'
                },
                notANumber: {
                    price: false,
                    quantity: false,
                    subTaskPrice: false,
                    message: 'must be a number'
                }
            }
        })

        let tb = wrapper.find('#totalTaskPrice')
        tb.trigger('click')

        await wrapper.vm.$nextTick()
        expect(wrapper.find('.v-messages__message').text()).toContain('Sub Price Can Not Be Higher Than Contractor Price')

    })

    test.skip('test that when dialog button is pressed for prices that the dialog opens', async () => {
        let wrapper = createWrapper();

        wrapper.setData({
            infoDialog: {
                prices: false
            }
        });

        console.log(wrapper.vm.$data.infoDialog.prices);
        const btn = wrapper.find({ref: 'priceInfo'});
        btn.trigger('click');
        await wrapper.vm.$nextTick();
        console.log('', wrapper.vm.infoDialog.prices);
        expect(wrapper.vm.infoDialog.prices).toBe(true);
    })

    test('test that when the price dialog cancel button is pressed then the dialog disappears', async () => {

        let wrapper = createWrapper();

        wrapper.setData({
            infoDialog: {
                prices: false
            }
        });

        const pbtn = wrapper.find({ref: 'priceInfo'});
        pbtn.trigger('click');

        await wrapper.vm.$nextTick();

        const btn = wrapper.find({ref: 'cancel'});
        btn.trigger('click');

        await wrapper.vm.$nextTick();
        expect(wrapper.text()).not.toContain('How Job Task Prices Work');

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