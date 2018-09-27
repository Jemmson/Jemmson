import {
  mount,
  shallowMount,
  createLocalVue
}
  from '@vue/test-utils'
import sinon from 'sinon'

const $route = {
  path: '/#/bid/1',
  query: {
    success: 1,
    error: 0
  },
  params: {
    id: 1
  }
}

require('./bootstrap')

import Job from '../../resources/assets/js/pages/Job.vue'

describe('Job', () => {
  const wrapper = shallowMount(Job, {
    mocks: {
      $route
    },
    stubs: [
      'bid-details',
      'approve-bid',
      'completed-tasks',
      'bid-tasks',
      'bid-add-task',
      'stripe',
      'card',
    ],
    slots: {
      'card-footer': ['<general-contractor-bid-actions />']
    },
    methods: {
      getBid(id) {
      }
    },
    propsData: {
      user: global.User.user,
    }
  })

  wrapper.setData({
    bid: {
      id: 1,
      status: 'bid.in_progress',
      job_tasks: [{
        id: 1,
      }]
    }
  })

  it('Should render the card component', () => {
    expect(wrapper.find('card-stub').exists()).toBe(true)
  })

  it('Should render the bid-details component', () => {
    expect(wrapper.find('bid-details-stub').exists()).toBe(true)
  })

  it('Should not render the approve-bid component - contractor', () => {
    expect(wrapper.find('approve-bid-stub').exists()).toBe(false)
  })

  it('Should render the general-contractor-bid-actions component', () => {
    expect(wrapper.find('general-contractor-bid-actions').exists()).toBe(true) // wtf
  })

  it('Should render the completed-tasks component', () => {
    expect(wrapper.find('completed-tasks-stub').exists()).toBe(true)
  })

  it('Should render the bid-tasks component', () => {
    expect(wrapper.find('bid-tasks-stub').exists()).toBe(true)
  })

  it('Should render the bid-add-task component', () => {
    expect(wrapper.find('bid-add-task-stub').exists()).toBe(true)
  })

  it('Should render the stripe component', () => {
    expect(wrapper.find('stripe-stub').exists()).toBe(true)
  })

  it('should show all jobs where new is in the job name when searched', function() {

  })

  it('should show all jobs were the customer name of shawn is searched', function() {

  })

  it('should show all jobs where the status is "bid in progress"', function() {

  })

  it('should show all jobs where the status is "bid initiated"', function() {

  })

})