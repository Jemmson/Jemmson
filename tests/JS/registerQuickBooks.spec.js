import { createLocalVue, mount } from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon'
import Vuex from 'vuex'
import RegisterQuickBooks from '../../resources/assets/js/pages/RegisterQuickBooks'

const localVue = createLocalVue()
localVue.use(Vuex)

console.log('RegisterQuickBooks.spec')

describe('RegisterQuickBooks', () => {
  let wrapper
  let actions
  let getters
  let mutations
  let store
  const getCompanyInfo = sinon.spy()
  const getTheCompanyInfo = sinon.spy()
  const setTheMobileResponse = sinon.spy()

  beforeEach(() => {
    actions = {
      checkMobileNumber: () => ''
    }
    mutations = {
      setMobileResponse: () => '',
      setTheMobileResponse: () => ''
    }
    getters = {
      getMobileValidResponse: () => ['phone', 'mobile', 'land', 'virtual'],
    }
    store = new Vuex.Store({
      state: {},
      actions,
      getters,
      mutations
    })

    wrapper = mount(RegisterQuickBooks, {
      store,
      localVue,
      methods: {
        getCompanyInfo,
        getTheCompanyInfo
      },
      data() {
        return {
          companyInfo: {}
        }
      }
    })

  })

  it('should show the json company information on mount', function() {
    // visually verified

  })

  it('should call the getcompanyinfo from the server session variable on mount', function() {
    // visually verified

  })

  it('should show the edit button for each of the sections pulled back from the quickbooks get company info', function() {
    // visually verified

  })

  it.only('should show the cancel, reset button, and the save button for each section if the edit button is clicked', function() {
    // visually verified

    wrapper.setData({
      qbCompanyInfoWasUpdated: false,
      sections: {
        editGeneralInfo: false
      }
    })

    let edit_btn = wrapper.find({
      ref: 'edit_btn'
    })

    edit_btn.trigger('click')

    expect(wrapper.find({ref: 'cancel_btn'}).isVisible()).toBe(true)
    expect(wrapper.find({ref: 'reset_btn'}).isVisible()).toBe(true)
    expect(wrapper.find({ref: 'save_btn'}).isVisible()).toBe(true)

  })

  it('should reset all values to the original values if the reset button is clicked', function() {
    // visually verified

  })

  it('should cancel all editable changes from a previous save if the cancel button is clicked', function() {
    // visually verified

  })

  it('should save all changes to the editable fields if the save button is clicked', function() {
    // visually verified

  })

  it('should only send updated to quickbooks only if the data was ' +
    'actually updated otherwise it should be skipped', function() {
    //visually verified
  })

  it.only('should not update the qbCompanyInfoWasUpdated field if the cancel button was clicked. the value should be the original value ', function() {

    wrapper.setData({
      qbCompanyInfoWasUpdated: false,
      sections: {
        editGeneralInfo: true
      }
    })

    let cancel_btn = wrapper.find({
      ref: 'cancel_btn'
    })

    expect(wrapper.vm.qbCompanyInfoWasUpdated).toBe(false)

    wrapper.setData({
      qbCompanyInfoWasUpdated: true,
      sections: {
        editGeneralInfo: true
      }
    })

    cancel_btn.trigger('click')

    expect(wrapper.vm.qbCompanyInfoWasUpdated).toBe(true)

  })

  it.only('should update the qbCompanyInfoWasUpdated field to true if the save button was clicked', function() {

    wrapper.setData({
      qbCompanyInfoWasUpdated: false,
      sections: {
        editGeneralInfo: true
      }
    })

    let save_btn = wrapper.find({
      ref: 'save_btn'
    })

    save_btn.trigger('click')

    expect(wrapper.vm.qbCompanyInfoWasUpdated).toBe(true)

  })

  it.only('should revert to the qbCompanyInfoWasUpdated value to false if the reset button is clicked', function() {

    wrapper.setData({
      qbCompanyInfoWasUpdated: true,
      sections: {
        editGeneralInfo: true
      }
    })

    let reset_btn = wrapper.find({
      ref: 'reset_btn'
    })

    reset_btn.trigger('click')

    expect(wrapper.vm.qbCompanyInfoWasUpdated).toBe(false)

  })

  it('should have the original value for qbCompanyInfoWasUpdated be false when the page is loaded', function() {
    // visually verified
  })

  it('should update quickbooks data when the register button is clicked', function() {

  })

  it('should highlight the phone mandatory field if it is not returned in the quickbooks data', function() {
    // visually verified
  })

  it('should hide any read-only fields that are apart of the registration ' +
    'if they were not filled out on the quickbooks setup', function() {

  })

  it('should show all editable fields even if they were not mandatory in this ' +
    'app or quickbooks and they are empty in the get company info response', function() {
    //visually verified
  })

  it('should have a pop up if you do not save the edit section', function() {

  })

  it('should have an error pop up if there are capital letters in the email address', function() {
    
  })

  it('should have all blank values that are not mandatory and are related to the ' +
    'quickbooks api be changed to null before it the request is sent to update the API', function() {
    // manually verified
  })

  it('should have page specific documentation on how this page works', function() {

  })

  it('should not submit if the passwords do not match', function() {
    //visually verified
  })

  it('should not submit if the accept terms button is not checked', function() {
    //visually verified
  })

  it.only('if there is an error because there is an empty field then if this error is rectified and register is hit again' +
    ' then the form should go through', function() {

    wrapper.setData({
      companyInfo: {
        message: {
          Email: {
            Address: 'noreply@quickbooks.com',
          },
          CompanyAddr: {
            Line1: '123 Sierra Way',
            Line2: '',
            City: 'San Pablo',
            CountrySubDivisionCode: '',
            PostalCode: 'CA',
            Zip: '87999'
          },
          CompanyName: 'Sandbox Company_US_1',
          PrimaryPhone: ''
        }
      },
      form: {
        email: '',
        name: 'sdssdds',
        company_name: '',
        phone_number: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        zip: '',
        terms: false,
        notes: '',
        password: 'asdasd',
        password_confirmation: 'asdasd',
        email_contact: true,
        phone_contact: false,
        sms_text: false,
        errors: {
          errors: {}
        }
      }
    })

    let btn = wrapper.find({
      ref: 'register'
    });

    btn.trigger('click');

    expect(wrapper.find({ref: 'phoneError'}).isVisible()).toBe(true);

    wrapper.setData({
      companyInfo: {
        message: {
          PrimaryPhone: '4807034902'
        }
      }
    });

    btn.trigger('click');

    expect(wrapper.find({ref: 'phoneError'}).isVisible()).toBe(false);

  })

  it('if a mobile number was input and then that number is deleted and the field is empty then the mobile flag should be removed ' +
    'from the bottom of the test file', function() {

  })

  //#########################
  //#PHONE
  //#########################

  it.only('the phone number should have 10 digits when it is unformatted', function() {
    wrapper.setData({
      form: {
        phone_number: '(123) 456-7890'
      }
    })

    expect(wrapper.vm.unformatNumber(wrapper.vm.form.phone_number)).toBe(10)

  })

  it.only('the phone number should have 9 digits when it is unformatted', function() {
    wrapper.setData({
      form: {
        phone_number: '(123) 456-789'
      }
    })
    expect(wrapper.vm.unformatNumber(wrapper.vm.form.phone_number)).toBe(9)
  })

  it.only('the phoneNumberLength number field should be updated with the correct value when the ' +
    'unformatNumber methods is called  ', function() {

    wrapper.setData({
      form: {
        phone_number: '(123) 456-789'
      }
    })
    expect(wrapper.vm.unformatNumber(wrapper.vm.form.phone_number)).toBe(9)
    expect(wrapper.vm.phoneNumberLength).toBe(9)

  })

  it('should not submit if the phone number is not valid', function() {
    //visually verified
  })

  it('should check the PrimaryNumber field that is returned by quickbooks ' +
    'and show an error if that primary number is not a mobile number', function() {
    //visually verified
  })

  it('should prompt to save an entered text if the edit button is hit. the register button should not ' +
    'work', function() {

  })

  it('should check that the email that was entered does not already exist in the database', function() {
    // use an ajax call
  })

  it('should check that the email is not already in the database at load time and then throw an error if it already exists', function() {

  })

  it('should check that the email is not in the database at blur if the user makes a change to the email and then throw ' +
    'an error if it is', function() {

  })

  it('should check that the phone that was entered does not already exist in the database', function() {
    // use an ajax call
  })

  it('should check that the phone is not already in the database at load time and then throw an error if it already exists', function() {

  })

  it('should check that the phone is not in the database at blur if the user makes a change to the email and then throw ' +
    'an error if it is', function() {

  })

  it('should not allow the register button to be clickable if the email already exists in the database', function() {

  })

  it('should not allow the register button to be clickable if the phone number already exists in the database', function() {

  })

  it('should have an asterisk that says that the company information does not sync with quickbooks due to a quickbooks ' +
    'limitation', function() {

  })

  it('company name must not be empty', function() {

  })

  it('should have a first name and last name fields', function() {

  })

  it('first name field can not be blank', function() {

  })

  it('last name field can not be blank', function() {

  })

  it('register button should be disabled if first name is blank', function() {

  })

  it('register button should be disabled if last name is blank', function() {

  })

  it('if on blur the number is formatted then on the delete key it should unformat the number so that regular entry ' +
    'can continue', function() {

  })

  it('should redirect if the user is alread logged in', function() {

  })

  it('should go to top of page where the errors are shown if the register buttons are clicked and errors are thrown', function() {

  })

  it.only('should check that if the PrimaryNumber field that is returned by quickbooks ' +
    'is null then the class empty-field-name will be present', function() {
    wrapper.setData({
      companyInfo: {
        message: {
          PrimaryPhone: null
        }
      }
    })

    let primaryPhone = wrapper.find({ref: 'primaryPhone'})

    expect(primaryPhone.attributes().class).toBe('ml-2 empty-field-name')

  })

  it.only('should check that if the PrimaryNumber field that is returned by quickbooks ' +
    'is not null then the class empty-field-name will not be present', function() {
    wrapper.setData({
      companyInfo: {
        message: {
          PrimaryPhone: '1231231234'
        }
      }
    })

    let primaryPhone = wrapper.find({ref: 'primaryPhone'})

    expect(primaryPhone.attributes().class).toBe('ml-2')

  })

  it.only('should have the password length error show if the length of the password is less than 6', function() {
    wrapper.setData({
      errors: {
        password: {
          pw_length: 5
        }
      }
    })

    let pw_error = wrapper.find({
      ref: 'password_error'
    })

    expect(pw_error.isVisible()).toBe(true)
  })

  it.only('should have the password length error show if the length of the password is equal to 6', function() {
    wrapper.setData({
      errors: {
        password: {
          pw_length: 6
        }
      }
    })

    let pw_error = wrapper.find({
      ref: 'password_error'
    })

    expect(pw_error.isVisible()).toBe(false)
  })

  it.only('should have the password length error not show if the length of the password is greater than 6', function() {
    wrapper.setData({
      errors: {
        password: {
          pw_length: 7
        }
      }
    })

    let pw_error = wrapper.find({
      ref: 'password_error'
    })

    expect(pw_error.isVisible()).toBe(false)
  })

})

// describe('RegisterQuickBooks_register_button', () => {
//   let wrapper
//   const getCompanyInfo = sinon.spy()
//
// })
