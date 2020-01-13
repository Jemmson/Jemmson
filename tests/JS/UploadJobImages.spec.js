import { shallowMount } from '@vue/test-utils'
import UploadJobImages from '../../resources/assets/js/components/task/UploadJobImages'
require('./setup');

describe('UploadJobImages', () => {

  test('is a Vue instance', () => {

    let wrapper = shallowMount(UploadJobImages, {
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    expect(wrapper.isVueInstance()).toBeTruthy()
  })

})