import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import GeneralContractorBidActions from "../../resources/assets/js/components/job/GeneralContractorBidActions";
import Vuetify from "vuetify"
import Vue from 'vue'
import {disableButtons} from "sweetalert2";
global.Bus = new Vue()

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
localVue.use(Vuetify)

describe('GeneralContractorBidActions', function () {
    let vuetify
    let wrapper

    beforeEach(() => {
        vuetify = new Vuetify()
        wrapper = shallowMount(GeneralContractorBidActions, {
            localVue,
            vuetify,
            propsData: {
                bid: {
                    job_tasks: [
                        {
                            task: {
                                name: 'mytask'
                            },
                            cust_final_price: 100,
                            sub_final_price: 50,
                        }
                    ],
                    job_statuses: [
                        {
                            status_number: 1
                        }
                    ]
                },
                disableButton: true
            }
        })
    })

    test('sanity', async () => {
        await wrapper.vm.$nextTick()
        expect(true).toBe(true)
    })

    test('submit button is disabled if the disableButton prop is true', async () => {
        wrapper.setProps({
            disableButton: false
        })
        wrapper.setData({
            subTaskWarning: false
        });
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'submitBid'}).attributes().disabled).toBe('true')
    })

    test('submit button is not disabled if the disableButton prop is false', async () => {
        wrapper.setProps({
            disableButton: true
        })
        wrapper.setData({
            subTaskWarning: true
        });
        await wrapper.vm.$nextTick()
        console.log('sub', wrapper.find({ref: 'submitBid'}).html())
        expect(wrapper.find({ref: 'submitBid'}).attributes().disabled).toBe(undefined)
    })

    test('that I am returning the correct values for my actions if there are no subs', async () => {
        wrapper.setProps({
            bid: {
                job_tasks: [
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 4.86,
                        sub_final_price: 0,
                    },
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 65,
                        sub_final_price: 0,
                    },
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 63.47,
                        sub_final_price: 0,
                    }
                ],
                job_statuses: [
                    {
                        status_number: 1
                    }
                ]
            },
        })
        wrapper.setData({
            tasks: [],
            tasksTotal: {
                general: 0,
                sub: 0,
                profit: 0,
                stripeFee: 0,
                netTotal: 0
            },
        });
        wrapper.vm.filterTasks();
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.tasksTotal.general.toFixed(2)).toBe("133.33");
        expect(wrapper.vm.tasksTotal.sub.toFixed(2)).toBe("0.00");
        expect(wrapper.vm.tasksTotal.stripeFee).toBe("4.24");
        expect(wrapper.vm.tasksTotal.netTotal).toBe("129.09");
    })

    test('that I am returning the correct values for my actions if there are subs', async () => {
        wrapper.setProps({
            bid: {
                job_tasks: [
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 4.86,
                        sub_final_price: 0,
                    },
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 65,
                        sub_final_price: 55,
                    },
                    {
                        task: {
                            name: 'mytask'
                        },
                        cust_final_price: 63.47,
                        sub_final_price: 0,
                    }
                ],
                job_statuses: [
                    {
                        status_number: 1
                    }
                ]
            },
        })
        wrapper.setData({
            tasks: [],
            tasksTotal: {
                general: 0,
                sub: 0,
                profit: 0,
                stripeFee: 0,
                netTotal: 0
            },
        });
        wrapper.vm.filterTasks();
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.tasksTotal.general.toFixed(2)).toBe("133.33");
        expect(wrapper.vm.tasksTotal.sub.toFixed(2)).toBe("55.00");
        expect(wrapper.vm.tasksTotal.stripeFee).toBe("4.24");
        expect(wrapper.vm.tasksTotal.netTotal).toBe("74.09");
        expect(wrapper.vm.tasks[1].profit).toBe(10);
    })
})