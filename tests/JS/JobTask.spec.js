import { createLocalVue, shallowMount, mount } from '@vue/test-utils'
import JobTask from '../../resources/assets/js/pages/JobTask'
import Vuex from 'vuex'
import Vue from 'vue'

const localVue = createLocalVue()

global.Bus = new Vue()
// global.Bus.$on('taskAdded', (data) => {
//   wrapper.vm.$emit('openSettings', data);
// });
global.Bus.$on('taskAdded')

localVue.use(Vuex)

describe('JobTask', () => {
  let actions
  let mutations
  let getters
  let state
  let store
  let storeOptions

  actions = {}
  mutations = {}
  getters = {}
  state = {
    job: {
      model: {
        job_tasks: [
        ]
      }
    }
  }

  storeOptions = {
    actions: actions,
    mutations: mutations,
    getters: getters
  }
  store = new Vuex.Store(storeOptions)

  const $route = {
    params: {
      index: 123
    }
  }


  let wrapper = shallowMount(JobTask, {
    store,
    mocks: {
      $store: {
        commit: jest.fn(),
        state: state
      },
      $route
    },
  })

  test ('change task button shows when job is not approved', () => {

    // user is a customer

    // job has not been approved by the customer
    wrapper.setData({
      jobTask: {
        task: {
          contractor_id: 1
        }
      },
      user: {
        id: 2
      },
      job: {
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
      }
    })

    let btn = wrapper.find("#changeTask")

    expect(btn.exists()).toBe(true)

  })

  test ('delete task button shows when job is not approved', () => {

    // user is a customer

    // job has not been approved by the customer
    wrapper.setData({
      jobTask: {
        task: {
          contractor_id: 1
        }
      },
      user: {
        id: 2
      },
      job: {
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
      }
    })

    let btn = wrapper.find("#changeTask")

    expect(btn.exists()).toBe(true)

  })

})