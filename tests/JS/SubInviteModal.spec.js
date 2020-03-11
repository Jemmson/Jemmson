import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import SubInviteModal from '../../resources/assets/js/components/task/SubInviteModal'
import Vuetify from "vuetify";
import Vuex from "vuex";

import VueRouter from 'vue-router'
import Vue from 'vue'
import Register from "../../resources/assets/js/pages/Register";

require('./setup')

const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(Vuex)
global.Bus = new Vue()

describe('SubInviteModal', function () {

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


    test('that submit button is not disabled when the mobile phone number is mobile', () => {

        wrapper = shallowMount(SubInviteModal, {
            vuetify,
            localVue,
            store,
            mocks: {
                $router: {
                    push: jest.fn()
                }
            },
            data: {
                initiateBidForSubForm: {
                    firstName: 'Jane',
                    lastName: 'Smith',
                    companyName: 'Garden Bud',
                }
            }
        })

        let submit = wrapper.find('#submit')

        expect(submit.hasOwnProperty('disabled')).toBe(false)

    })

})