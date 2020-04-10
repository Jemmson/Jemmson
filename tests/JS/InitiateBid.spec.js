import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'
import Vue from 'vue'
import VueRouter from 'vue-router'

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

Vue.use(Vuetify)

const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(VueRouter)

const router = new VueRouter()

describe('InitiateBid', () => {
  let wrapper
  let vuetify
  vuetify = new Vuetify()

  beforeEach(() => {
    wrapper = shallowMount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {}
      }
    })
  })

  window.Vue = {
    toasted: {
      success: jest.fn(),
      error: jest.fn()
    }
  }

  test('is a Vue instance', () => {
    wrapper = shallowMount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {}
      }
    })
    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  test.skip('must have a title saying "Add New Job"', () => {
    wrapper = mount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {}
      }
    })
    expect(wrapper.find('#title').text()).toBe('Add New Job')

  })

  test.skip('test that the first name validation works', async () => {
    wrapper = mount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {}
      }
    })
    let fname = wrapper.find({ref: 'firstName'})
    wrapper.setData({
      fname: 'asdasdasdasdasdasd'
    })
    fname.trigger('keyup')
    await Vue.nextTick(() => {
      console.log('fname', fname.html())
      expect(wrapper.text()).toContain('Name must be less than 16 characters')
    })
  })

  test('that I am able to transalte the returned data into format that the combobox is looking for', () => {
    wrapper = mount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {
          comboResults: []
        }
      }
    })

    const data = [
      {
        'id': 3,
        'name': 'Shawn Pike',
        'first_name': 'Shawn',
        'last_name': 'Pike',
        'phone': '4807034902',
        'email': 'pike.shawn@gmail.com',
        'tax_rate': 0,
        'quickbooks_id': null
      },
      {
        'id': 2,
        'name': 'Jack Ripper',
        'first_name': 'Shawn',
        'last_name': 'Pike',
        'phone': '4807034902',
        'email': 'pike.shawn@gmail.com',
        'tax_rate': 0,
        'quickbooks_id': null
      }
    ]

    const transformedData = wrapper.vm.transformDataForComboBox(data)

    expect(transformedData).toEqual([
      {
        text: 'Shawn Pike',
        value: 3
      },
      {
        text: 'Jack Ripper',
        value: 2
      }
    ])

  })

  test('test that I can filter out the correct computed result from the selected result', () => {
    wrapper = mount(InitiateBid, {
      localVue,
      vuetify,
      router,
      mocks: {
        $store: {
          commit: jest.fn(),
        }
      },
      data() {
        return {
          results: [
            {
              'id': 3,
              'name': 'Shawn Pike',
              'first_name': 'Shawn',
              'last_name': 'Pike',
              'phone': '4807034902',
              'email': 'pike.shawn@gmail.com',
              'tax_rate': 0,
              'quickbooks_id': null
            },
            {
              'id': 2,
              'name': 'Jack Ripper',
              'first_name': 'Shawn',
              'last_name': 'Pike',
              'phone': '4807034902',
              'email': 'pike.shawn@gmail.com',
              'tax_rate': 0,
              'quickbooks_id': null
            }
          ]
        }
      }
    })

    const selected = {
      text: 'Shawn Pike',
      value: 3
    }

    const filteredResult = wrapper.vm.getComboResult(selected)

    expect(filteredResult).toEqual(
      {
        'id': 3,
        'name': 'Shawn Pike',
        'first_name': 'Shawn',
        'last_name': 'Pike',
        'phone': '4807034902',
        'email': 'pike.shawn@gmail.com',
        'tax_rate': 0,
        'quickbooks_id': null
      },
    )
  })

})