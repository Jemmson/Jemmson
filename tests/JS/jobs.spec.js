import {
  mount,
  shallowMount,
  createLocalVue,
  config
}
  from '@vue/test-utils'
import VuePaginate from 'vue-paginate'
import sinon from 'sinon'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import expect from 'expect'

require('./bootstrap')

const localVue = createLocalVue()
localVue.use(Vuex)

localVue.use(VuePaginate)
localVue.use(VueRouter)

import Jobs from '../../resources/assets/js/pages/Jobs.vue'

const $on = {
  bidUpdated: () => {
  }
}

describe('Jobs', () => {
  const search = sinon.stub()
  let store
  let storeOptions

  const router = new VueRouter({})

  let wrapper

  beforeEach(() => {
    storeOptions = {
      getters: {},
      actions: {},
      mutations: {
        setCurrentPage: sinon.spy()
      },
      state: {
        bidsContractorSectionPicked: true
      }
    }
    store = new Vuex.Store(storeOptions)
  })

  it('Should contain the name Clear up Green Pool', () => {

    wrapper = mount(Jobs, {
      localVue,
      store,
      router,
      mocks: {}
    })

    wrapper.setData({
      bids: [],
      sBids: [
        {
          id: 1,
          bid_price: 245,
          customer: {
            name: 'Laurel Ailie'
          },
          job_name: 'Clear up Green Pool',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        },
        {
          id: 2,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 3,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 4,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 5,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 6,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 7,
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent',
          job_tasks: [],
          contractor_id: 1
        }, {
          id: 8,
          bid_price: 245,
          customer: {
            name: 'John Doe'
          },
          job_name: 'Fix Pool',
          status: 'job.completed',
          job_tasks: [],
          contractor_id: 1
        }],
      showBid: false,
      bidIndex: 0,
      searchTerm: '',
      paginate: ['sBids']
    })

    wrapper.setProps({
      user: {
        id: 1,
        usertype: 'contractor'
      }
    })

    expect(wrapper.findAll({ref: 'job'}).length).toBe(8)
    expect(wrapper.findAll({ref: 'job_name'}).at(0).text()).toBe('Clear up Green Pool')

  })

  it('should show number of tasks for a given job if that job has tasks', function() {

    let w = mount(Jobs, {
      store,
      localVue,
      router,
      stubs: [
        'search-bar',
      ]
    })

    w.setData({
      sBids: [
        {
          job_tasks: [
            {task_id: 1},
            {task_id: 2},
            {task_id: 3}
          ]
        }]
    })

    w.setProps({
      isCustomer: true
    })

    expect(w.find({ref: 'show_number_of_job_tasks'}).text()).toBe('3 Tasks')
  })

  it('should show number the total number of subs bidding on all tasks for the job', function() {
    let w = mount(Jobs, {
      store,
      localVue,
      router,
      stubs: [
        'search-bar',
      ]
    })

    w.setData({
      sBids: [
        {
          job_tasks: [
            {
              task_id: 1,
              bid_contractor_job_tasks: [
                {id: 1},
                {id: 2},
                {id: 3}
              ]
            },
            {
              task_id: 2,
              bid_contractor_job_tasks: [
                {id: 1},
                {id: 2},
                {id: 3}
              ]
            },
            {
              task_id: 3,
              bid_contractor_job_tasks: [
                {id: 1},
                {id: 2},
                {id: 3}
              ]
            }
          ]
        }]
    })

    expect(w.find({ref: 'total_number_of_subs'}).text()).toBe('9 Subs')

  })

  it('should not show job tasks for a customer', function() {

    wrapper = mount(Jobs, {
      localVue,
      store,
      router,
      mocks: {}
    })

    wrapper.setData({
      bids: [],
      sBids: [
        {
          id: 1,
          bid_price: 245,
          customer: {
            name: 'Laurel Ailie'
          },
          job_name: 'Clear up Green Pool',
          status: 'bid.sent',
          contractor_id: 1
        }],
      showBid: false,
      bidIndex: 0,
      searchTerm: '',
      paginate: ['sBids']
    })

    wrapper.setProps({
      user:
        {
          id: 1,
          usertype: 'customer'
        }
    })

    expect(wrapper.find({ref: 'total_number_of_subs'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'show_number_of_job_tasks'}).exists()).toBe(false)

  })

  // it('Should have rendered 6 job items', () => {
  //   expect(wrapper.findAll('section')).toHaveLength(6)
  // })
  //
  // it('Should have 2 paginate links', () => {
  //   expect(wrapper.html()).toContain('<a>1</a>')
  //   expect(wrapper.html()).toContain('<a>2</a>')
  // })
  //
  // it('Clicking the next arrow should show the next list of jobs', () => {
  //   wrapper.find('.right-arrow > a').trigger('click')
  //   expect(wrapper.findAll('section')).toHaveLength(2)
  // })
  //
  // it('Clicking the previous arrow should show the previous list of jobs', () => {
  //   wrapper.find('.left-arrow > a').trigger('click')
  //   expect(wrapper.findAll('section')).toHaveLength(6)
  // })
  //
  // it('Search bar should update with input', () => {
  //   const input = wrapper.find('input')
  //   input.setValue('Clear up Green Pool')
  //   //input.trigger('keyup');
  //   expect(wrapper.vm.searchTerm).toBe('Clear up Green Pool')
  //   // expect(wrapper.findAll('section')).toHaveLength(1);
  // })

})