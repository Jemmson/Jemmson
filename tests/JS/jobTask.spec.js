import { createLocalVue, shallowMount, mount } from '@vue/test-utils'
import JobTask from '../../resources/assets/js/pages/JobTask'
import sinon from 'sinon'
import expect from 'expect'
import Vuex from 'vuex'

import VueRouter from 'vue-router'

// require('./bootstrap')

const localVue = createLocalVue()
localVue.use(Vuex)
localVue.use(VueRouter)

describe('JobTask', () => {
  const router = new VueRouter()

  // const $route = {
  //   params: {
  //     index: 0
  //   }
  // }

  beforeEach(() => {

  })

  afterEach(() => {

  })

  it('should update the total cust price final price when the Price is updated', function() {

    const index = '0'

    const wrapper = shallowMount(JobTask, {
      router,
      mocks: {
        $store: {
          state: {
            job: {
              model: {
                job_tasks: [
                  {task: {name: 'sarah'}}
                ]
              }
            }
          }
        }
      }
    })

    wrapper.setData({
      jobTask: {
        bid_contractor_job_tasks: [
          {
            name: '',
            payment_type: '',
            id: '',
            contractor: {
              name: ""
            },
            bid_price: ""
          },
        ],
        task: {
          name: 'sarah',
        }
      }
    })

    const price = wrapper.find({ref: 'price'})

    price.setValue(100)
    price.trigger('blur')

    expect(wrapper.find({ref: 'contractor_total_cost'})).toBe(100)

  })

})