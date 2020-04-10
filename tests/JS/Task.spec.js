import {createLocalVue, shallowMount} from '@vue/test-utils'
import Task from '../../resources/assets/js/components/task/Task'
import Vuetify from 'vuetify'
import VueMask from 'v-mask'
import Format from "../../resources/assets/js/classes/Format";

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
            }
        })

        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('test that I can pull back cents if there are only 2 digits', () => {

        let wrapper = shallowMount(Task, {
            localVue,
            directives: {
                mask() {
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