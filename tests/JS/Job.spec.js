import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import Job from '../../resources/assets/js/pages/Job'
import Status from '../../resources/assets/js/components/mixins/Status'
import Utilities from '../../resources/assets/js/components/mixins/Utilities'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Vue from 'vue'

// window.Vue = Vue
import Vuetify from 'vuetify'

// config.stubs['v-card'] = '<div />'
config.stubs['bid-details'] = '<div />'
config.stubs['approve-bid'] = '<div />'
config.stubs['general-contractor-bid-actions'] = '<div />'
config.stubs['completed-tasks'] = '<div />'
config.stubs['stripe'] = '<div />'

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex)

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

  test.skip('in_progress status shows up as In Progress status', () => {
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

    expect(wrapper.find('h1').text()).toContain('General Contractor')

    expect(wrapper.text()).toContain('Job Status:')
    // expect(wrapper.find({ref: 'jobStatus'}).text()).toBe('sent on 12/06/2019')

  })

})