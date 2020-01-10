import { createLocalVue, shallowMount } from '@vue/test-utils'
import Task from '../../resources/assets/js/components/task/Task'
import Vuetify from 'vuetify'

require('./setup')

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('Task', () => {

  test('is a Vue instance', () => {

    let wrapper = shallowMount(Task, {
      localVue,
      mocks: {
        $router: {
          push: jest.fn()
        }
      }
    })

    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  test('test that I can pull back cents if there are only 2 digits', () => {

    let wrapper = shallowMount(Task, {
      localVue
    })
    wrapper.setData({
      formattedBidPrice: ''
    })

    wrapper.vm.formatInput('23')

    expect(wrapper.vm.$data.formattedBidPrice).toBe('.23')

  })

  test('test that I can pull back dollars if there are more than 2 digits', () => {

    let wrapper = shallowMount(Task, {
      localVue
    })
    wrapper.setData({
      formattedBidPrice: ''
    })
    wrapper.vm.formatInput('123456789')
    expect(wrapper.vm.$data.formattedBidPrice).toBe('1234567.89')

    wrapper.vm.$data.formattedBidPrice = 123456789
    wrapper.vm.formatInput(123456789)
    expect(wrapper.vm.$data.formattedBidPrice).toBe(1234567.89)

    wrapper.vm.$data.formattedBidPrice = 1200
    wrapper.vm.formatInput(1200)
    expect(wrapper.vm.$data.formattedBidPrice).toBe(12.00)

  })

  test('that i can convert the number string to the correct number value', () => {

    let wrapper = shallowMount(Task, {
      localVue
    })

    let val = wrapper.vm.convertPriceToIntegers('$ 4.50')
    expect(val).toBe(4.50)

    val = wrapper.vm.convertPriceToIntegers('$ .50')
    expect(val).toBe(.50)

    val = wrapper.vm.convertPriceToIntegers('$ .')
    expect(val).toBe(null)

    val = wrapper.vm.convertPriceToIntegers('')
    expect(val).toBe(null)

  })

  test ('that I can convert num to string', () => {

    let wrapper = shallowMount(Task, {
      localVue
    })

    let val = wrapper.vm.convertNumToString(4.50)
    expect(val).toBe('4.50')

    val = wrapper.vm.convertNumToString(4.00)
    expect(val).toBe('4.00')

    val = wrapper.vm.convertNumToString(4)
    expect(val).toBe('4.00')

    val = wrapper.vm.convertNumToString(4.89)
    expect(val).toBe('4.89')

  })

})