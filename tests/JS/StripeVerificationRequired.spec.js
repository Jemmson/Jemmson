import {createLocalVue, shallowMount} from '@vue/test-utils'
import StripeVerificationRequired from '../../resources/assets/js/components/stripe/StripeVerificationRequired'
import Vuetify from 'vuetify'
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
localVue.use(Vuetify, {})

describe('StripeVerificationRequired', () => {

    test('is a Vue instance', () => {

        let wrapper = shallowMount(StripeVerificationRequired, {
            localVue,
            propsData: {
                verification: {
                    current_deadline: "",
                    disabled_reason: "",
                    currently_due: "",
                    eventually_due: "",
                    past_due: "",
                    pending_verification: "",
                }
            }
        })

        expect(wrapper.isVueInstance()).toBeTruthy()
    })
    
    test('that if the contractor', async () => {
        const wrapper = shallowMount(StripeVerificationRequired, {
            localVue,
            propsData: {
                verification: {
                    current_deadline: "",
                    disabled_reason: "",
                    currently_due: "",
                    eventually_due: "",
                    past_due: "",
                    pending_verification: "",
                }
            }
        })
        await wrapper.vm.$nextTick()
        expect()
        
    })

})