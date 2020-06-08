AAZimport {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import Job from '../../resources/assets/js/pages/Job'
import Status from '../../resources/assets/js/components/mixins/Status'
import Utilities from '../../resources/assets/js/components/mixins/Utilities'
import Vuex from 'vuex'
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

// window.Vue = Vue
import Vuetify from 'vuetify'

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex)
localVue.use(Vuetify, {})

const router = new VueRouter()

describe('Job', () => {
    let storeOptions
    let store
    let wrapper
    let vuetify

    beforeEach(() => {
        vuetify = new Vuetify()
        storeOptions = {
            actions: {
                checkMobileNumber: jest.fn(() => Promise.resolve())
            },
            mutations: {
                setTheMobileResponse: jest.fn(),
                setCurrentPage: jest.fn()
            },
            getters: {
                getMobileValidResponse: jest.fn()
            }
        }
        store = new Vuex.Store(storeOptions)
    })

    window.Vue = {
        toasted: {
            success: jest.fn(),
            error: jest.fn()
        }
    }

    global.Bus = new Vue()
    // global.Bus.$on('taskAdded', (data) => {
    //   wrapper.vm.$emit('openSettings', data);
    // });
    global.Bus.$on('taskAdded')

    wrapper = shallowMount(Job, {
        store,
        router,
        vuetify,
        localVue,
        mixins: [Status, Utilities],
        mocks: {
            $store: {
                commit: jest.fn()
            },
        },
        propsData: {
            user: {
                user: {}
            }
        },
        data() {
            return {
                bidForm: {
                    id: ''
                }
            }
        }
    })

    test('sanity test', () => {
        expect(wrapper.text()).toContain('Job Status:')
        // console.log('Job', Job)
    })

    test('is a Vue instance', () => {
        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('in_progress status shows up as In Progress status', () => {
        wrapper.setData({
            bid: {
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
            },
            jobStatus: {
                status: 'sent',
                created_at: '12/06/2019'
            }
        })

        wrapper.setProps({
            user: {
                name: 'General Contractor'
            }
        })

        Vue.nextTick(() => {
            expect(wrapper.find({ref: 'jobStatus'}).text()).toBe('sent on 12/06/2019')
        })

    })

    test('Please wait until your contractor submits bid should show up if the job has not ' +
        'been submitted and the user is a customer', async () => {

        wrapper.setData({
            bid: {
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
                    }
                ]
            }
        })

        wrapper.setProps({
            user: {
                usertype: 'customer'
            }
        })

        await wrapper.vm.$nextTick()

        expect(wrapper.find({ref: 'jobNotSubmittedMessage'}).exists()).toBe(true)

    })

    test('Please wait until your contractor submits bid should show up if the job has not ' +
        'been submitted and the user is a customer', async () => {

        wrapper.setData({
            bid: {
                job_statuses: [
                    {
                        id: 1,
                        job_id: 1,
                        status: 'initiated',
                        status_number: 1,
                        deleted_at: null,
                        created_at: '2019-12-05 22:15:18',
                        updated_at: '2019-12-05 22:15:18'
                    }
                ]
            }
        })

        wrapper.setProps({
            user: {
                usertype: 'customer'
            }
        })

        await wrapper.vm.$nextTick()
        
        console.log('message', wrapper.find({ref: 'jobNotSubmittedMessage'}).html())

        expect(wrapper.find({ref: 'jobNotSubmittedMessage'}).exists()).toBe(true)

    })

})