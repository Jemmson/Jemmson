import { shallowMount } from '@vue/test-utils'
import AddJobTask from '../../resources/assets/js/pages/AddJobTask'
require('./setup');

describe('AddJobTask', () => {

  const wrapper = shallowMount(AddJobTask, {
    mocks: {
      $router: {
        push: jest.fn()
      }
    }
  })

  test('is a Vue instance', () => {
    expect(wrapper.isVueInstance()).toBeTruthy()
  })



})