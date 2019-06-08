import { createLocalVue, mount } from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon'
import moxios from 'moxios'
import Vuex from 'vuex'
import Register from '../../resources/assets/js/pages/Register'

const localVue = createLocalVue()
localVue.use(Vuex)

console.log('Register.spec')

describe('Register', () => {
  let wrapper
  // let actions
  // let getters
  // let mutations
  // let store
  const checkValidData = sinon.spy()
  // const getTheCompanyInfo = sinon.spy()

  beforeEach(() => {

  })

  afterEach(function() {
    // import and pass your custom axios instance to this method
    moxios.uninstall()
  })

  it('should highlight the change the color of the Customer Button to green and the Contractor Button to blue if the customer button is clicked', function() {

    wrapper = mount(Register)

    let customerButton = wrapper.find({ref: 'customerButton'})
    let contractorButton = wrapper.find({ref: 'contractorButton'})

    expect(customerButton.classes()).not.toContain('selected-button')
    expect(contractorButton.classes()).not.toContain('selected-button')

    customerButton.trigger('click');

    expect(customerButton.classes()).toContain('selected-button')
    expect(contractorButton.classes()).not.toContain('selected-button')


  })

  it('should have the registration.busy be false if the first_name param is false', function() {

    wrapper.setData({
      registerForm: {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: '',
        errors: {},
        usertype: '',
        busy: false
      }
    })

    // wrapper.

  })

  it('should call the checkValidata method on a keyup', function() {
    wrapper = mount(Register)
    let first_name = wrapper.find({ref: 'first_name'})
    first_name.trigger('keyup')
    expect(checkValidData.calledOnce).toBe(true)
  })

})
