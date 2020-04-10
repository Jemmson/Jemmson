import { shallowMount, createLocalVue } from '@vue/test-utils'
import UploadJobImages from '../../resources/assets/js/components/task/UploadJobImages'
const localVue = createLocalVue()

import Vuetify from 'vuetify'

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

require('./setup');
localVue.use(Vuetify, {})

describe('UploadJobImages', () => {
  let vuetify = new Vuetify()
  test('is a Vue instance', () => {
    let wrapper = shallowMount(UploadJobImages, {
      vuetify,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })
    expect(wrapper.isVueInstance()).toBeTruthy()
  })

})