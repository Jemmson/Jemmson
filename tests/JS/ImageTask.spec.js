import { createLocalVue, shallowMount } from '@vue/test-utils'
import ImageTask from '../../resources/assets/js/components/task/ImageTask'
require('./setup');
import Vuetify from 'vuetify'

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('ImageTask', () => {

  test('is a Vue instance', () => {

    let wrapper = shallowMount(ImageTask, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  test.skip ('that I am not adding duplicate task images to the imageTasks array', () => {

    let wrapper = shallowMount(ImageTask, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      task: null,
      taskArray: []
    })

    expect(wrapper.vm.$data.taskArray.length).toBe(0)

    wrapper.vm.$data.task = 1

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

  })

  test.skip ('that I cant add the same image more than once', () => {
    let wrapper = shallowMount(ImageTask, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      task: null,
      taskArray: []
    })

    expect(wrapper.vm.$data.taskArray.length).toBe(0)

    wrapper.vm.$data.task = 1

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

    wrapper.vm.$data.task = 1

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

    wrapper.vm.$data.task = 2

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(2)

  })

  test.skip ('test that I can remove a task', () => {
    let wrapper = shallowMount(ImageTask, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      task: null,
      taskArray: []
    })

    expect(wrapper.vm.$data.taskArray.length).toBe(0)

    wrapper.vm.$data.task = 1

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

    wrapper.vm.$data.task = 1

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

    wrapper.vm.$data.task = 2

    wrapper.vm.associateImageToTask()

    expect(wrapper.vm.$data.taskArray.length).toBe(2)

    wrapper.vm.removeTaskId(1)

    expect(wrapper.vm.$data.taskArray.length).toBe(1)

  })

})