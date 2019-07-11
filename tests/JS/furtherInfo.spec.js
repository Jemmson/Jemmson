import {
  shallowMount, mount, createLocalVue
} from '@vue/test-utils'
import sinon from 'sinon'
import FurtherInfo from '../../resources/assets/js/pages/FurtherInfo'
import moxios from 'moxios'
import Vuex from 'vuex'
import User from '../../resources/assets/js/classes/User'

const localVue = createLocalVue()

localVue.use(Vuex)

require('./bootstrap')

describe('FurtherInfo', () => {
  const submitFurtherInfo = sinon.spy()
  let getters
  let store
  let mutations

  moxios.install()

  getters = {
    getMobileValidResponse: () => ['phone', 'mobile', 'land'],
  }
  mutations = {
    setTheMobileResponse: () => 'hell',
  }
  store = new Vuex.Store({
    state: {
      features: {
        quickbooks: 1
      }
    },
    getters,
    mutations
  })

  const wrapper = mount(FurtherInfo, {
    store,
    localVue,
    data: () => {
      return {}
    },
    methods: {
      submitFurtherInfo,
      checkValidData() {
        return false
      },
      initAutocomplete() {
        return true
      }
    },
    propsData: {
      user: {
        name: 'John',
        email: 'john@john.com',
        contractor: {
          location: {
            address_line_1: '',
            address_line_2: '',
            city: '',
            state: '',
            zip: '',
          }
        },
        customer: {
          location: {
            address_line_1: '',
            address_line_2: '',
            city: '',
            state: '',
            zip: '',
          }
        },
        password_updated: false
      }
    }
  })

  it('Should render itself', () => {
    expect(wrapper.isEmpty()).toBe(false)
  })

  it('should render the correct phone number format', function() {
    let numLength = wrapper.vm.unformatNumber('123) 123 2345')
    expect(numLength).toBe(10)
  })

  it('Should render the contractor heading - contractor', () => {

    wrapper.setProps({
      user: {
        usertype: 'contractor'
      }
    })

    let heading = wrapper.find({
      ref: 'furtherInfoHeading'
    })

    expect(heading.html()).toContain('Please register your company')
  })

  it('Should not render customer heading - contractor', () => {
    expect(wrapper.html()).not.toContain('Please Add Additional Information')
  })

  it('Should show errors when there are errors', () => {
    wrapper.setData({
      form: {
        errors: {
          email: '',
          name: '',
          company_name: '',
          phone_number: '',
          address_line_1: '',
          city: '',
          state: '',
          zip: '',
          password: '',
          password_confirmation: '',
          errors: {
            email: ['The email field is required.'],
            address_line_1: ['The address line 1 field is required.'],
            city: ['The city field is required.'],
            zip: ['The zip field is required.']
          }
        }
      }
    })
    const errors = wrapper.findAll('.help-block')

    expect(errors.length).toBe(11)

    expect(wrapper.find({ref: 'emailError'}).text()).toBe('The email field is required.')
    expect(wrapper.find({ref: 'addressLine1Error'}).text()).toBe('The address line 1 field is required.')
    expect(wrapper.find({ref: 'cityError'}).text()).toBe('The city field is required.')
    expect(wrapper.find({ref: 'zipError'}).text()).toBe('The zip field is required.')

  })

  it('Should try and submit further info', () => {
    const submit = wrapper.find('[type=submit]')
    submit.trigger('click')
    expect(submitFurtherInfo.calledOnce).toBe(true)
  })

  it('Should render customer heading - customer', () => {
    wrapper.setProps({
      user: {
        usertype: 'customer'
      }
    })

    let heading = wrapper.find({
      ref: 'furtherInfoHeadingCustomer'
    })

    expect(heading.html()).toContain('Please Add Additional Information')

  })

  it('Should render contractor instuctions input - customer', () => {
    wrapper.setProps({
      user: {
        usertype: 'customer'
      }
    })

    const textArea = wrapper.find('#notes')
    expect(textArea.exists()).toBe(true)
  })

  it('Should render password fields', () => {

    wrapper.setProps({
      user: {
        usertype: 'contractor'
      }
    })

    const pass = wrapper.find({
      ref: 'password'
    })
    const cPass = wrapper.find({
      ref: 'password_confirmation'
    })

    expect(pass.exists()).toBe(true)
    expect(cPass.exists()).toBe(true)

  })
  //
  // it('Should not render password fields', () => {
  //   const wrapper = shallowMount(FurtherInfo, {
  //     store,
  //     localVue,
  //     data: () => {
  //       return {}
  //     },
  //     methods: {
  //       submitFurtherInfo,
  //       checkValidData() {
  //         return false
  //       },
  //       initAutocomplete() {
  //         return true
  //       }
  //     },
  //     propsData: {
  //       user: {
  //         name: 'John',
  //         email: 'john@john.com',
  //         contractor: null,
  //         password_updated: true
  //       }
  //     }
  //   })
  //   const pass = wrapper.find({
  //     ref: 'password'
  //   })
  //   const cPass = wrapper.find({
  //     ref: 'password_confirmation'
  //   })
  //
  //   expect(pass.exists()).toBe(false)
  //   expect(cPass.exists()).toBe(false)
  //
  // })

  it('Should show errors when passwords dont match', () => {
    const cPass = wrapper.find({
      ref: 'password_confirmation'
    })
    cPass.setValue('hello')

    cPass.trigger('keyup')
    expect(wrapper.html()).toContain('Passwords need to match.')
  })

  it('Should not show errors when passwords do match', () => {
    const pass = wrapper.find({
      ref: 'password'
    })
    const cPass = wrapper.find({
      ref: 'password_confirmation'
    })
    pass.setValue('hello')
    cPass.setValue('hello')

    cPass.trigger('keyup')
    expect(wrapper.html()).not.toContain('Passwords need to match.')
  })

})