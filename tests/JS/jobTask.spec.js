import { createLocalVue, shallowMount, mount } from '@vue/test-utils'
import JobTask from '../../resources/assets/js/pages/JobTask'
import sinon from 'sinon'
import expect from 'expect'
import Vuex from 'vuex'

import VueRouter from 'vue-router'

require('./bootstrap')

const localVue = createLocalVue()
localVue.use(Vuex)
localVue.use(VueRouter)

describe('JobTask', () => {
  const router = new VueRouter()
  let getters
  let store

  getters = {
    getMobileValidResponse: () => ['phone', 'mobile', 'land'],
  }

  store = new Vuex.Store({
    getters
  })

  beforeEach(() => {

  })

  afterEach(() => {

  })

  it.skip('should update the total cust price final price when the Price is updated', function() {
    const isAssignedToMeStub = sinon.stub
    const wrapper = shallowMount(JobTask, {
      router,
      store,
      stub: {
        authUser: {}
      },
      mocks: {
        $store: {
          state: {
            job: {
              id: 1,
              model: {
                job_tasks: [
                  {
                    contractor_id: 1,
                    task: {name: 'sarah'}
                  }
                ],
                status: 'bid.initiated'
              }
            }
          }
        }
      }
    })

    wrapper.setData({
      jobTask: {
        id: 1,
        unit_price: 15,
        contractor_id: 1,
        cust_final_price: 10,
        images: [
          {id: 1, url: ''}
        ],
        bid_contractor_job_tasks: [
          {
            name: '',
            payment_type: '',
            id: 1,
            contractor: {
              name: ''
            },
            bid_price: ''
          },
        ],
        task: {
          id: 1,
          name: 'sarah',
        }
      },
      user: {
        id: 1,
        usertype: 'contractor'
      }
    })

    // console.log('usertype', wrapper.vm.$data.user.usertype)
    // console.log('isContractor => ', wrapper.vm.isContractor())
    // expect(wrapper.vm.isContractor()).toBe(true)
    // expect(wrapper.vm.isGeneral()).toBe(true)
    // expect(wrapper.vm.jobStatus).toBe('bid.initiated')
    // expect(wrapper.vm.showTaskPriceInput()).toBe(true)

    // console.log('jobTask', wrapper.find({ref: 'jobTask'}).html())
    // console.log('test', wrapper.find({ref: 'test'}).html())
    // console.log('prices', wrapper.find({ref: 'prices'}).html())
    // console.log('unitPrice', wrapper.find({ref: 'unitPrice'}).html())


    // let price = wrapper.find({ref: 'price'})

    //
    // price.setValue(100)
    // price.trigger('blur')
    //
    // expect(wrapper.find({ref: 'totalTaskPrice'}).text()).toBe("$10.00")

  })

  it.skip('is General should evaluate to true if jobTask is not null and the contractor and the user id are the same', function() {

    const wrapper = shallowMount(JobTask, {
      router,
      mocks: {
        $store: {
          state: {
            job: {
              model: {
                job_tasks: [
                  {task: {name: 'sarah'}}
                ],
                status: 'bid.initiated'
              }
            }
          }
        }
      }
    })

    wrapper.setData({
      jobTask: {
        contractor_id: 1,
        bid_contractor_job_tasks: [
          {
            name: '',
            payment_type: '',
            id: '',
            contractor: {
              name: ''
            },
            bid_price: ''
          },
        ],
        task: {
          name: 'sarah',
        }
      },
      user: {
        id: 1,
        usertype: 'contractor'
      }
    })

  })

})