import {
  shallowMount, mount, createLocalVue
} from '@vue/test-utils'
import sinon from 'sinon'
import FurtherInfo from '../../resources/assets/js/pages/FurtherInfo'
import moxios from 'moxios'
import Vuex from 'vuex'

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
        contractor: null,
        password_updated: false
      }
    }
  })

  it('Should render itself', () => {
    expect(wrapper.isEmpty()).toBe(false)
  })

  it('should render the correct phone number format', function() {
    let numLength = wrapper.vm.unformatNumber('123) 123 2345');
    console.log(numLength);
    expect(numLength).toBe(10);
  })

  it('Should render the contractor heading - contractor', () => {
    wrapper.setProps({
      user: {usertype: 'customer'}
    })
    let heading = wrapper.find({
      ref: 'furtherInfoHeading'
    })
    // console.log(heading.html())
    // console.log(wrapper.props().user.usertype)
    // console.log(wrapper.vm.isContractor)
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
        }
      }
    })
    const errors = wrapper.findAll('.help-block')
    expect(errors.length).toBe(10)
  })

  it('Should try and submit further info', () => {
    const submit = wrapper.find('[type=submit]')
    submit.trigger('click')
    expect(submitFurtherInfo.calledOnce).toBe(true)
  })

  it('Should render customer heading - customer', () => {
    User.setUser({
      'name': 'jane',
      'usertype': 'customer',
      custoemr: null
    })
    const wrapper = shallowMount(FurtherInfo, {
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
          contractor: null,
        }
      }
    })
    expect(wrapper.html()).toContain('Please Add Additional Information')
  })

  it('Should render contractor instuctions input - customer', () => {
    User.setUser({
      'name': 'jane',
      'usertype': 'customer',
      custoemr: null
    })
    const wrapper = shallowMount(FurtherInfo, {
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
          contractor: null
        }
      }
    })
    const textArea = wrapper.find('#notes')
    expect(textArea.exists()).toBe(true)
  })

  it('Should render password fields', () => {
    const pass = wrapper.find({
      ref: 'password'
    })
    const cPass = wrapper.find({
      ref: 'password_confirmation'
    })

    expect(pass.exists()).toBe(true)
    expect(cPass.exists()).toBe(true)

  })

  it('Should not render password fields', () => {
    const wrapper = shallowMount(FurtherInfo, {
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
          contractor: null,
          password_updated: true
        }
      }
    })
    const pass = wrapper.find({
      ref: 'password'
    })
    const cPass = wrapper.find({
      ref: 'password_confirmation'
    })

    expect(pass.exists()).toBe(false)
    expect(cPass.exists()).toBe(false)

  })

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