import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'
import Vue from 'vue'

require('./setup')

// window.Vue = Vue
import Vuetify from 'vuetify'
Vue.use(Vuetify)

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('InitiateBid', () => {
  let wrapper
  let vuetify
  vuetify = new Vuetify()

  beforeEach(() => {
    wrapper = shallowMount(InitiateBid, {
      localVue,
      vuetify,
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
    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  test('must have a title saying "Add New Job"', () => {

    expect(wrapper.find('#title').text()).toBe('Add New Job')

  })

  test('test that the first name validation works', async () => {
    let fname = wrapper.find('#fname')
    wrapper.setData({
      fname: 'asdasdasdasdasdasd'
    })
    fname.trigger('keyup')
    await Vue.nextTick(() => {
      console.log('fname', fname.html())
      expect(
        wrapper.text()).toContain('Name must be less than 16 characters')
    })
  })

})