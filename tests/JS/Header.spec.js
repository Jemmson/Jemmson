import { shallowMount, createLocalVue, mount } from '@vue/test-utils'
import Header from '../../resources/assets/js/components/shared/Header'
import sinon from 'sinon'
import expect from 'expect'

import Vuex from 'vuex'

const localVue = createLocalVue()
localVue.use(Vuex)

require('./bootstrap')

describe('Header', () => {

  it.skip('should show contractor jobs when contractor link is clicked', function() {

  })

  it('should show bio information if I am on the home page', function() {

    let store = new Vuex.Store({
      state: {
        page: '/home'
      }
    })

    const wrapper = mount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: '',
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    expect(wrapper.find({ref: 'homePage'}).exists()).toBe(true)
    expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

  })

  it('should show header bio component if on the invoices page', function() {

    let store = new Vuex.Store({
      state: {
        page: '/invoices'
      }
    })

    const wrapper = mount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: '',
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(true)
    expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

  })

  it('should show header bio component if on the tasks page and user is a customer', function() {

    let store = new Vuex.Store({
      state: {
        page: '/tasks'
      }
    })

    const wrapper = mount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'customer',
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(true)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

})

it('should show job toggle header component if on the tasks page and user is a contractor', function() {

  let store = new Vuex.Store({
    state: {
      page: '/tasks'
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor'
      }
    }
  })

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(true)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

})

it('should show header back button component if on the add tasks page and user is a contractor', function() {

  let store = new Vuex.Store({
    state: {
      page: '/job/add/task'
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor'
      }
    }
  })

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(true)

})

it.skip('[DO NOT HAVE THE ADD SUB BUTTON WORKING CORRECTLY] should show header back button component if on the add subs page and user is a contractor', function() {

  let store = new Vuex.Store({
    state: {
      page: ''
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor'
      }
    }
  })

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(true)

})

it('should show bio component if on the initiate bids page and user is a contractor', function() {

  console.log('this should be working but it is not')

  let store = new Vuex.Store({
    state: {
      page: '/initiate-bid'
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor',
        first_name: 'Joe',
        last_name: 'Jakson'
      }
    }
  })

  // console.log((wrapper.vm.onInitiateBidPage() ||
  //   wrapper.vm.onSettingsPage() ||
  //   wrapper.vm.onImagesPage())
  //   && !wrapper.vm.isCustomer())


  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(true)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

})

it.skip('[SETTINGS PAGE IS NOT SETUP CORRECTLY YET] should show bio component if on the settings bids page and user is a contractor', function() {

  let store = new Vuex.Store({
    state: {
      page: '/settings'
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor',
        first_name: 'Joe',
        last_name: 'Jakson'
      }
    }
  })

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(true)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

})

it('[SHOULD BE WORKING BUT IT IS NOT] should show bio component if on the images page and user is a contractor', function() {

  let store = new Vuex.Store({
    state: {
      page: '/arg/arg1/images'
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'contractor',
        first_name: 'Joe',
        last_name: 'Jakson'
      }
    }
  })

  // console.log(wrapper.vm.onImagesPage())

  expect(wrapper.find({ref: 'homePage'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage1'}).exists()).toBe(true)
  expect(wrapper.find({ref: 'homePage2'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'homePage3'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerJobToggle'}).exists()).toBe(false)
  expect(wrapper.find({ref: 'headerBackButton'}).exists()).toBe(false)

})


it('should show the job name if the page is a bid', function() {

  let store = new Vuex.Store({
    state: {
      page: '/bid/9',
      bidsContractorSectionPicked: '',
      user: {
        user: {
          usertype: 'customer',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      },
      job: {
        model: {
          job_name: 'pool work'
        }
      }
    }
  })

  const wrapper = mount(Header, {
    store,
    localVue,
    propsData: {
      user: {
        usertype: 'customer',
        first_name: 'Joe',
        last_name: 'Jakson',
        contractor: {
          company_name: 'pool work'
        }
      }
    }
  })

  //
  expect(wrapper.find({ref: 'headerJobNameSettingsLogout'}).text()).toContain('pool work')

})

})