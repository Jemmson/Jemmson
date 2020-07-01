import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import Assessor from "../../resources/assets/js/pages/Assessor";
import Vuetify from "vuetify"
import Vuex from "vuex";
import VueRouter from 'vue-router'


import Vue from 'vue'
import Job from "../../resources/assets/js/pages/Job";
import Status from "../../resources/assets/js/components/mixins/Status";
import Utilities from "../../resources/assets/js/components/mixins/Utilities";

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

describe('Assessor', function () {

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
                setCurrentPage: jest.fn()
            },
            getters: {
                // getMobileValidResponse: jest.fn()
            }
        }
        store = new Vuex.Store(storeOptions)

        wrapper = shallowMount(Assessor, {
            store,
            router,
            vuetify,
            localVue,
            // mixins: [Status, Utilities],
            mocks: {
                // $store: {
                //     commit: jest.fn()
                // },
            },
            propsData: {
                // user: {
                //     user: {}
                // }
            },
            data() {
                return {
                }
            }
        })
    })


    test('sanity test', async () => {
        await wrapper.vm.$nextTick()
        expect(wrapper.text()).toContain('Assessor')

    })

    test('is a Vue instance', async () => {
        await wrapper.vm.$nextTick()
        expect(wrapper.isVueInstance()).toBeTruthy()

    })

})