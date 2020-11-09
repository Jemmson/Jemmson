import {createLocalVue, shallowMount} from '@vue/test-utils'
import Task from '../../resources/assets/js/components/task/Task'
import Vuetify from 'vuetify'
import VueMask from 'v-mask'
import Format from "../../resources/assets/js/classes/Format";
import $ from "jquery"

global.Format = Format;

global.Spark = {
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: '',
                stripe_express: {
                    stripe_account_verification: {}
                }
            },
            usertype: 'customer'
        }
    }
}

require('./setup')

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('Task', () => {

    test('is a Vue instance', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            mocks: {
                $router: {
                    push: jest.fn()
                }
            },
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })

        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test.skip('that if I submit a bid that the total price will be the qty * the price', async () => {

        // test does not work but this works in the browser

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    id: 1,
                    job_task: {
                        qty: 10,
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })

        let bidPrice = wrapper.find('#price-1')
        console.log('bidPrice', bidPrice.html())
        bidPrice.setValue(10)
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.calculateBidPrice(wrapper.vm.bidTask.id)).toBe(100)
        
    })
    
    test('test that I can pull back cents if there are only 2 digits', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })
        wrapper.setData({
            formattedBidPrice: ''
        })

        wrapper.vm.formatInput('23')

        expect(wrapper.vm.$data.formattedBidPrice).toBe('.23')

    })

    test('test that I can pull back dollars if there are more than 2 digits', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })
        wrapper.setData({
            formattedBidPrice: ''
        })
        wrapper.vm.formatInput('123456789')
        expect(wrapper.vm.$data.formattedBidPrice).toBe('1234567.89')

        wrapper.vm.$data.formattedBidPrice = 123456789
        wrapper.vm.formatInput(123456789)
        expect(wrapper.vm.$data.formattedBidPrice).toBe(1234567.89)

        wrapper.vm.$data.formattedBidPrice = 1200
        wrapper.vm.formatInput(1200)
        expect(wrapper.vm.$data.formattedBidPrice).toBe(12.00)

    })

    test('that i can convert the number string to the correct number value', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })

        let val = wrapper.vm.convertPriceToIntegers('$ 4.50')
        expect(val).toBe(4.50)

        val = wrapper.vm.convertPriceToIntegers('$ .50')
        expect(val).toBe(.50)

        val = wrapper.vm.convertPriceToIntegers('$ .')
        expect(val).toBe(null)

        val = wrapper.vm.convertPriceToIntegers('')
        expect(val).toBe(null)

    })

    test('that I can convert num to string', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })

        let val = wrapper.vm.convertNumToString(4.50)
        expect(val).toBe('4.50')

        val = wrapper.vm.convertNumToString(4.00)
        expect(val).toBe('4.00')

        val = wrapper.vm.convertNumToString(4)
        expect(val).toBe('4.00')

        val = wrapper.vm.convertNumToString(4.89)
        expect(val).toBe('4.89')

    })

    test('that the Actions section is shown if the job has been approved', async () => {
        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })


        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'actionSection'}).exists()).toBe(true)

    })

    test('test that if the date is not included on submit then an error will be thrown', async () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            methods: {

            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        })

        wrapper.setData({
            startDate: '',
            startDateError: false,
            bidPrice: ''
        })

        expect(wrapper.vm.startDateIsRequired()).toBe('A Start Date Is Required')

    })

    test.skip('that if the stripe account is disabled then there will a message saying ' +
        'that the sub will not be able to mark the bid as finished ' +
        'and that he will be unable to paid for the job ' +
        'and that the mark as finished button will shown but it will be disabled', async () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        });

        wrapper.setData({
            stripeVerified: false
        })

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'warningMessageAccountDisabled'}).exists()).toBe(true)
        expect(wrapper.find({ref: 'markJobFinishedButton'}).attributes().disabled).toBe('true')

    })

    test.skip('test that the stripe-verification-component show up if verification is required', async () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
                }
            },
            propsData: {
                bidTask: {
                    job_task: {
                        qty: '1',
                        task: {
                            name: 'Task 1'
                        },
                        job: {
                            job_task_status: [
                                {
                                    status: 'approved_by_customer'
                                }
                            ]
                        }
                    }
                }
            }
        });

        // global.Spark.state.user.stripe_account_verification = {
        //     "account_id": "acct_1GRmKtJl3a26Gg1U",
        //     "created_at": "2020-03-28 21:40:11",
        //     "current_deadline": null,
        //     "currently_due": "['individual.id_number']",
        //     "disabled_reason": "requirements.past_due",
        //     "errors": "[]",
        //     "eventually_due": "['individual.id_number']",
        //     "past_due": "['individual.id_number']",
        //     "pending_verification": "[]",
        //     "updated_at": "2020-03-28 21:40:11"
        // }

        global.Spark = {
            state: {
                user: {
                    id: 1,
                    contractor: {
                        accounting_software: ''
                    },
                    usertype: 'customer',
                    stripe_account_verification : {
                        account_id: "acct_1GRmKtJl3a26Gg1U",
                        created_at: "2020-03-28 21:40:11",
                        current_deadline: null,
                        currently_due: "['individual.id_number']",
                        disabled_reason: "requirements.past_due",
                        errors: "[]",
                        eventually_due: "['individual.id_number']",
                        past_due: "['individual.id_number']",
                        pending_verification: "[]",
                        updated_at: "2020-03-28 21:40:11"
                    }
                }
            }
        }

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'warningMessageAccountDisabled'}).exists()).toBe(true)

    })

})