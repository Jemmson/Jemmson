import {
  mount,
  createLocalVue,
  shallowMount
} from '@vue/test-utils'
import Vuex from 'vuex'

const localVue = createLocalVue()

localVue.use(Vuex)

import InitiateBid from '../../resources/assets/js/pages/InitiateBid'

describe('InitiateBid', () => {
  let actions
  let getters
  let mutations
  let store

  beforeEach(() => {
    actions = {}
    mutations = {
      setTheMobileResponse: () => 'hell',
    }
    getters = {
      getMobileValidResponse: () => ['phone', 'mobile', 'land'],
    }
    store = new Vuex.Store({
      state: {},
      actions,
      getters,
      mutations
    })
  })

  it('should display The Phone field is required. if name is input but not phone number', function () {
    const wrapper = mount(InitiateBid, {
      store,
      localVue,
      data () {
        return {
          query: '',
          results: [],
          form: new SparkForm({
            phone: '',
            customerName: ''
          }),
          disabled: {
            submit: false
          }
        }
      }
    })
  })

  it('should display "mobile" when a mobile number is put in the text box', function () {
    const wrapper = shallowMount (InitiateBid, {
      store,
      localVue,
      data () {
        return {
          query: '',
          results: [],
          form: new SparkForm({
            phone: '',
            customerName: ''
          }),
          disabled: {
            submit: false
          }
        }
      }
    })
    const input = wrapper.find('#customerName')
    input.setValue('mookie blaylock')
  })

  it('should display "Landline" when a landline number is put in the text box and then the blur action occurs', function () {

  })

  it('the submit button should be disabled when only the customer field is filled out and the other fields are blank', function () {

  })

  it('the submit button should be disabled when only the phone field is filled out and the other fields are blank', function () {

  })

  it('the submit button should be disabled when only the job name field is filled out and the other fields are blank', function () {

  })

  it('the submit button should be disabled when only the job name and phone field is filled out and the other fields are blank', function () {

  })

  it('the submit button should be disabled when only the job name and customer name field is filled out and the other fields are blank', function () {

  })

  it('the submit button should not be disabled when the phone and customer name field is filled out and the other fields are blank', function () {

  })

  it('should show error if phone number is bad and the submit button is clicked', function () {

  })

  it('should send a contractor to the subscription page and not create a new bid if the sixth free bid trying to be sent', function () {

  })

  it('should create a unique job name if bid is created and the job name is left blank', function () {

  })
})