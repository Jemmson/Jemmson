import { createLocalVue, mount, shallowMount } from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon'
import moxios from 'moxios'
import Vuex from 'vuex'
import Register from '../../../resources/assets/js/pages/Register'

const localVue = createLocalVue()
localVue.use(Vuex)

console.log('Register.spec')

describe('Register', () => {
  let wrapper
  // let actions
  let getters
  // let mutations
  let store
  // const getTheCompanyInfo = sinon.spy()

  getters = {
    getMobileValidResponse: () => ['phone', 'mobile', 'land'],
  }

  store = new Vuex.Store({
    getters
  })

  beforeEach(() => {
  })

  afterEach(function() {
    // import and pass your custom axios instance to this method
    moxios.uninstall()
  })

  wrapper = shallowMount(Register, {
    localVue,
    store
  })

  it('should highlight the change the color of the Customer Button to green and the Contractor Button to blue if the customer button is clicked', function() {

    // wrapper = mount(Register)

    let customerButton = wrapper.find({ref: 'customerButton'})
    let contractorButton = wrapper.find({ref: 'contractorButton'})

    expect(customerButton.classes()).not.toContain('selected-button')
    expect(contractorButton.classes()).not.toContain('selected-button')

    customerButton.trigger('click');

    expect(customerButton.classes()).toContain('selected-button')
    expect(contractorButton.classes()).not.toContain('selected-button')


  })

  it('should have the registration.busy be false if the first_name param is false', function() {


    const checkValidDataStub = sinon.stub()

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

    wrapper.setMethods({
      checkValidData: checkValidDataStub
    })


  })

  it('should have a field to input contractor licenses', function() {
    expect(wrapper.find({ref: 'contractor_label'}).text())
      .toBe("Please Click To Add A Contractor License")
  })

  it('should have a button to add a contractor license', function() {
    expect(wrapper.find({ref: "add_contractor_license_button"}).text())
      .toBe("Add A License")
  })

  it('clicking add a license field should create two textboxes for name license and license number', function() {

    addLicenseTextBox()

    expect(wrapper.find({ref: "license1"}).exists()).toBe(true);
  })

  it.skip('should display the values of the license under the license tag once the add button for the given license is clicked', function() {

    addLicenseTextBox()

  })

  function addLicenseTextBox(){
    let btn = wrapper.find({ref: "add_contractor_license_button"})
    btn.trigger('click')
  }

  it.skip('should call the checkValidata method on a keyup', function() {
    // wrapper = mount(Register)
    let first_name = wrapper.find({ref: 'first_name'})
    first_name.trigger('keyup')
    expect(checkValidDataStub.calledOnce).toBe(true)
  })

})
