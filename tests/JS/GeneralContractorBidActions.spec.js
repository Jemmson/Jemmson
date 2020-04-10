import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import GeneralContractorBidActions from "../../resources/assets/js/components/job/GeneralContractorBidActions";
import Vuetify from "vuetify"
import Vue from 'vue'
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

    beforeEach(() => {
        vuetify = new Vuetify()
    })

    test('sanity', async () => {
        const wrapper = shallowMount(GeneralContractorBidActions)
        await wrapper.vm.$nextTick()
        expect(true).toBe(true)
    })

    test('submit button is disabled if the disableButton prop is true', async () => {
        const wrapper = shallowMount(GeneralContractorBidActions, {
            localVue,
            vuetify,
            propsData: {
                bid: {
                    job_statuses: [
                        {
                            status_number: 1
                        }
                    ]
                },
                disableButton: true
            }
        })
        await wrapper.vm.$nextTick()
        console.log('sub', wrapper.find({ref: 'submitBid'}).html())
        expect(wrapper.find({ref: 'submitBid'}).attributes().disabled).toBe('true')
    })

    test('submit button is not disabled if the disableButton prop is false', async () => {
        const wrapper = shallowMount(GeneralContractorBidActions, {
            localVue,
            vuetify,
            propsData: {
                bid: {
                    job_statuses: [
                        {
                            status_number: 1
                        }
                    ]
                },
                disableButton: false
            }
        })
        await wrapper.vm.$nextTick()
        console.log('sub', wrapper.find({ref: 'submitBid'}).html())
        expect(wrapper.find({ref: 'submitBid'}).attributes().disabled).toBe(undefined)
    })

})