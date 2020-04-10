import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import Subscription from '../../resources/assets/js/pages/Subscription'
import Vuetify from "vuetify"
import Vuex from "vuex";
import VueRouter from 'vue-router'
// const Stripe = require('./stripe')
import Vue from 'vue'
import {fn} from "moment";

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuetify)
localVue.use(Vuex)
global.Bus = new Vue()


require('./stripeSetup')


global.Spark = {
    stripeKey: 'sdksdklsd',
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: '',
                stripe_express: {
                    stripe_user_id: 'test_user_id'
                }
            },
            usertype: 'contractor'
        }
    }
}

require('./setup')

describe('Subscription', function () {

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
            },
            mocks: {
                Stripe: {
                    elements: jest.fn()
                }
            }
        }

        store = new Vuex.Store(storeOptions)

        wrapper = mount(Subscription, {
            vuetify,
            localVue
        });

    })

    test('that the component is set up correctly', async () => {

        await wrapper.vm.$nextTick()
        expect(true).toBe(true)

    })
    
    test('test that the select buttons will trigger ', async () => {

        await wrapper.vm.$nextTick()
        expect()
        
    })

})