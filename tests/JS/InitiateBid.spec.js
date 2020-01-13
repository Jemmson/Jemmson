import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'
import Vue from 'vue'
import VueRouter from 'vue-router'

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

})