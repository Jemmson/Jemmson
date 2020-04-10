import { shallowMount } from '@vue/test-utils'
import AddJobTask from '../../resources/assets/js/pages/AddJobTask'

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

describe('AddJobTask', () => {

  test('is a Vue instance', () => {

    let wrapper = shallowMount(AddJobTask, {
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  test ('test that addnewtaskform.updateTask is false and addnewtaskform.createnew os true', () => {

    let wrapper = shallowMount(AddJobTask, {
      mocks: {
        $router: {
          push: jest.fn()
        }
      },
      methods: {
        addNewTaskToBid: jest.fn()
      }
    })

    wrapper.setData({
      addNewTaskForm: {
        updateTask: true,
        createNew: false
      }
    })

    wrapper.vm.changeTask('New')

    expect(wrapper.vm.$data.addNewTaskForm.updateTask).toBe(false)
    expect(wrapper.vm.$data.addNewTaskForm.createNew).toBe(true)

  })

})