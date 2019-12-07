import { createLocalVue, mount } from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon'
import moxios from 'moxios'
import Vuex from 'vuex'
import Register from '../../resources/assets/js/pages/Register.vue'

describe('Register', function() {

  it('should send the appropriate data to the server', function() {

    wrapper.shallowMount('Register')

    wrapper.setData(
      {
        addressLine1: "705 E Oxford Dr.",
        addressLine2: "",
        busy: true,
        city: "Tempe",
        companyName: "Jemmson",
        country: {
          name: "Afghanistan",
          code: "AF"},
        disabled: false,
        email: "",
        errors: {
          first_name: "",
          last_name: "",
          email: "",
          companyName: "",
          phoneNumber: "",
          addressLine1: "",
          addressLine2: '',
          city: '',
          state: '',
          zip: '',
          country: '',
          password: '',
          password_confirmation: '',
          terms: false
        },
        first_name: "Shawn",
        last_name: "Pike",
        password: "asdasd",
        password_confirmation: "asdasd",
        phoneNumber: "(480) 622-6441",
        state: {
          name: "Alabama",
          code: "AL"
        },
        terms: true,
        usertype: "contractor",
        zip: "jemmsoninc@gmail.com"
      }
    )

  })

})