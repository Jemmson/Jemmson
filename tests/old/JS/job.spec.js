import { mount, createLocalVue, shallowMount } from '@vue/test-utils'
import Vuex from 'vuex'
import VueRouter from 'vue-router'

import moxios from 'moxios'
import sinon from 'sinon';


const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex)

const router = new VueRouter()


import expect from 'expect'

// const $route = {
//   path: '/#/bid/1',
//   query: {
//     success: 1,
//     error: 0
//   },
//   params: {
//     id: 1
//   }
// }

// let $route = {};


// require('./bootstrap')

import Job from '../../resources/assets/js/pages/Job.vue'

describe('Job', () => {
  let actions
  let getters
  let mutations
  let store



  beforeEach(() => {
    moxios.install();

    actions = {
      checkMobileNumber: () => ''
    }
    mutations = {
      setTheMobileResponse: () => 'hell',
      setCurrentPage: () => 'hell',
    }
    getters = {
      getMobileValidResponse: () => ['phone', 'mobile', 'land'],
    }
    store = new Vuex.Store({
      state: {},
      actions,
      getters,
      mutations
    })
  })

  afterEach(() => {
    moxios.uninstall();
  });



  // const wrapper = shallowMount(Job, {
  //   router,
  //   store,
  //   localVue,
  //   mocks: {
  //     // $route
  //   },
  //   stubs: [
  //     'bid-details',
  //     'approve-bid',
  //     'completed-tasks',
  //     'bid-tasks',
  //     'bid-add-task',
  //     'stripe',
  //     'card',
  //     'general-contractor-bid-actions'
  //   ],
  //   slots: {
  //     'card-footer': ['<general-contractor-bid-actions />']
  //   },
  //   methods: {
  //     getBid(id) {
  //       switch (id) {
  //         case 1:
  //           this.bid = {
  //             id: 1,
  //             status: 'bid.in_progress',
  //             job_tasks: [{
  //               id: 1,
  //             }]
  //           };
  //           break;
  //         case 2:
  //           Vue.toasted.error('Error');
  //           break;
  //         default:
  //           break;
  //       }
  //     }
  //   },
  //   propsData: {
  //     user: new User({
  //       id: 1,
  //       usertype: 'contractor',
  //       name: 'John Doe',
  //       contractor: {
  //         id: 1,
  //         user_id: 1,
  //         company_name: 'KPS Pools',
  //         stripe_express: null
  //       }
  //     }),
  //   }
  // })

  // it('Should render the card component', () => {
  //   expect(wrapper.find('card-stub').exists()).toBe(true)
  // })
  //
  // it('Should render the bid-details component', () => {
  //   expect(wrapper.find('bid-details-stub').exists()).toBe(true)
  // })
  //
  // it('Should not render the approve-bid component - contractor', () => {
  //   expect(wrapper.find('approve-bid-stub').exists()).toBe(false)
  // })
  //
  // it('Should render the general-contractor-bid-actions component', () => {
  //   expect(wrapper.find('general-contractor-bid-actions').exists()).toBe(false) // wtf
  // })
  //
  // it('Should render the completed-tasks component', () => {
  //   expect(wrapper.find('completed-tasks-stub').exists()).toBe(true)
  // })
  //
  // it('Should render the bid-tasks component', () => {
  //   expect(wrapper.find('bid-tasks-stub').exists()).toBe(true)
  // })
  //
  // it('Should render the bid-add-task component', () => {
  //   expect(wrapper.find('bid-add-task-stub').exists()).toBe(true)
  // })
  //
  // it('Should render the stripe component', () => {
  //   expect(wrapper.find('stripe-stub').exists()).toBe(true)
  // })
  
});