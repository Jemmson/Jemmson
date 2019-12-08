import { shallowMount } from '@vue/test-utils'
import AddJobTask from '../../resources/assets/js/pages/AddJobTask'
// import SparkForm from '../../spark/resources/assets/js/forms/form.js'

global.Spark = {
  state: {
    user: {
      id: 1,
      contractor: {
        accounting_software: ''
      }
    }
  }
}

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