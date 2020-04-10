import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import Register from '../../resources/assets/js/pages/Register'
import Vuex from 'vuex'
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
import Vuetify from 'vuetify'

const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(Vuex)
global.Bus = new Vue()

describe('Register', function() {
  let wrapper
  let vuetify
  let storeOptions
  let store

  beforeEach(() => {
    vuetify = new Vuetify()
    storeOptions = {
      actions: {
        // checkMobileNumber: jest.fn(() => Promise.resolve())
      },
      mutations: {
        setPhoneLoadingValue: jest.fn()
      },
      getters: {
        getMobileValidResponse: jest.fn()
      }
    }
    store = new Vuex.Store(storeOptions)
  })

  test.skip('show license box when I press the add a license button', () => {
    wrapper = shallowMount(Register, {
      vuetify,
      localVue,
      store
    })
    expect(wrapper.find('#licenseDialog').exists()).toBe(false)
    const btn = wrapper.find('#addContractorLicenseButton')
    btn.trigger('click')
    Vue.nextTick(() => {
      expect(wrapper.find('#licenseDialog').exists()).toBe(true)
    })
  })

  test.skip('that the dialog exits when the cancel button is clicked', () => {
    wrapper = shallowMount(Register, {
      vuetify,
      localVue,
      store
    })
    const btn = wrapper.find('#addContractorLicenseButton')
    btn.trigger('click')
    Vue.nextTick(() => {
      expect(wrapper.find('#licenseDialog').exists()).toBe(true)
    })
    const closebtn = wrapper.find('#addLicenseCloseBtn')
    btn.trigger('click')
    Vue.nextTick(() => {
      expect(wrapper.find('#licenseDialog').exists()).toBe(false)
    })
  })

  test.skip('test that license array gets populated when I hit the add button on the dialog', () => {
    wrapper = shallowMount(Register, {
      vuetify,
      localVue,
      store
    })

    wrapper.setData({
      registerForm: {
        licenses: []
      },
      license: {
        name: '',
        number: '',
        state: '',
        type: '',
      },
    })

    const btn = wrapper.find('#addContractorLicenseButton')
    btn.trigger('click')
    Vue.nextTick(() => {
      expect(wrapper.find('#licenseDialog').exists()).toBe(true)
    })

    const state = wrapper.find('#licenseState');
    const name = wrapper.find('#name');
    const type = wrapper.find('#type');
    const number = wrapper.find('#number');

    state.setValue('AZ')
    name.setValue('Swimming Pool License')
    type.setValue('A-9')
    number.setValue('1234')

    const addbtn = wrapper.find('#addLicense')
    addbtn.trigger('click')

    Vue.nextTick(() => {
      expect(wrapper.vm.$data.license.state).toBe('AZ')
      expect(wrapper.vm.$data.license.name).toBe('Swimming Pool License')
      expect(wrapper.vm.$data.license.type).toBe('A-9')
      expect(wrapper.vm.$data.license.number).toBe('1234')
    })

  })

  test.skip('when I delete a license then I should only be able to remove that one from the license array', () => {
    wrapper = shallowMount(Register, {
      vuetify,
      localVue,
      store
    })

    wrapper.setData({
      registerForm: {
        licenses: [
          {
            name: 'swimming pool',
            number: '1',
            state: 'arizona',
            type: 'A-4',
          },
          {
            name: 'swimming pool',
            number: '2',
            state: 'alabama',
            type: 'A-7',
          },
          {
            name: 'swimming pool',
            number: '2',
            state: 'alabama',
            type: 'A-7',
          },
          {
            name: 'swimming pool',
            number: '3',
            state: 'colorado',
            type: 'A-5',
          }
        ]
      },
      license: {
        name: 'swimming pool',
        number: '2',
        state: 'alabama',
        type: 'A-7',
      },
    })
    console.log('license length', wrapper.vm.registerForm.licenses.length)
    wrapper.vm.removeLicense(wrapper.vm.license)
    console.log('license length', wrapper.vm.registerForm.licenses.length)
    expect(wrapper.vm.registerForm.licenses.length).toBe(3)

  })



})