import { createLocalVue, shallowMount } from '@vue/test-utils'
import ImageAssociation from '../../resources/assets/js/pages/ImageAssociation'
require('./setup');
import Vuetify from 'vuetify'

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('ImageAssociation', () => {

  test('is a Vue instance', () => {

    let wrapper = shallowMount(ImageAssociation, {
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

    let wrapper = shallowMount(ImageAssociation, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      imageTasks: []
    })

    expect(wrapper.vm.$data.imageTasks.length).toBe(0)

    wrapper.vm.associateImageToTask(1, 1)

    expect(wrapper.vm.$data.imageTasks.length).toBe(1)

    wrapper.vm.associateImageToTask(1, 1)

    expect(wrapper.vm.$data.imageTasks.length).toBe(1)

    wrapper.vm.associateImageToTask(1, 2)

    expect(wrapper.vm.$data.imageTasks.length).toBe(2)

  })

  
  test.skip ('that I can remove a task from the imageTasks array', () => {
    let wrapper = shallowMount(ImageAssociation, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      imageTasks: []
    })

    expect(wrapper.vm.$data.imageTasks.length).toBe(0)

    wrapper.vm.associateImageToTask(1, 1)
    wrapper.vm.associateImageToTask(1, 2)
    wrapper.vm.associateImageToTask(1, 3)
    wrapper.vm.associateImageToTask(1, 4)
    wrapper.vm.associateImageToTask(1, 5)

    // console.log('imageTasks', JSON.stringify(wrapper.vm.$data.imageTasks))

    expect(wrapper.vm.$data.imageTasks.length).toBe(5)

    wrapper.vm.clearTask(1, 3)

    expect(wrapper.vm.$data.imageTasks.length).toBe(4)

  })

  test.skip ('test that I can clear all tasks', () => {
    let wrapper = shallowMount(ImageAssociation, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    wrapper.setData({
      imageTasks: []
    })

    expect(wrapper.vm.$data.imageTasks.length).toBe(0)

    wrapper.vm.associateImageToTask(1, 1)
    wrapper.vm.associateImageToTask(1, 2)
    wrapper.vm.associateImageToTask(1, 3)
    wrapper.vm.associateImageToTask(1, 4)
    wrapper.vm.associateImageToTask(1, 5)
    wrapper.vm.associateImageToTask(2, 1)
    wrapper.vm.associateImageToTask(2, 2)
    wrapper.vm.associateImageToTask(2, 3)
    wrapper.vm.associateImageToTask(3, 1)
    wrapper.vm.associateImageToTask(3, 2)
    wrapper.vm.associateImageToTask(3, 3)

    console.log('imageTasks', JSON.stringify(wrapper.vm.$data.imageTasks))

    expect(wrapper.vm.$data.imageTasks.length).toBe(11)

    wrapper.vm.clearAllTasks(2)

    console.log('imageTasks', JSON.stringify(wrapper.vm.$data.imageTasks))

    expect(wrapper.vm.$data.imageTasks.length).toBe(8)

  })

})