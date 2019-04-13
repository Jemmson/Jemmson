import { mount, createLocalVue, shallowMount } from '@vue/test-utils'
import Vuex from 'vuex'
import moxios from 'moxios'

const localVue = createLocalVue()

localVue.use(Vuex)

import InitiateBid from '../../resources/assets/js/pages/InitiateBid'

describe('InitiateBid', () => {
  let actions
  let getters
  let mutations
  let store

  beforeEach(() => {
    moxios.install();

    actions = {
      checkMobileNumber: () => ''
    }
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

  afterEach(() => {
    moxios.uninstall();
  });

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

  it('should display "mobile" when a mobile number is put in the text box', async function () {

    // mocha.withMock('axios')

    // moxios.stubRequest('/api/user/validatePhoneNumber', {
    //   status: 200,
    //   response: [
    //     'failure', 'landline', 'hkjsdas'
    //   ]
    // })

    // axios.get('/say/hello').then(onFulfilled);

    // expect.assertions(1);

    const wrapper = shallowMount(InitiateBid, {
      store,
      localVue,
      actions,
      getters,
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
    const i = wrapper.find('#phone')
    i.setValue('4807034902')

    // console.log('hello');
    // console.log(i);
    // console.log(wrapper.vm.$options);
    // console.log(wrapper);

    // input.trigger('blur')

    // await flushPromises();

    // expect(wrapper.vm.value).toBe('mobile')

    // wrapper.vm.$nextTick(() => {
    //   expect(wrapper.vm.value).toBe('mobile')
    //   done()
    // })


    // expect(wrapper.find('#mobileNetworktype').exists()).toBe(true)

  })

  it.skip('should display "Landline" when a landline number is put in the text box and then the blur action occurs', function () {

  })

  it.skip('the submit button should be disabled when only the customer field is filled out and the other fields are blank', function () {

  })

  it('the submit button should be disabled when only the phone field is filled out and the other fields are blank', function () {
    const wrapper = shallowMount(InitiateBid, {
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
    const phone = wrapper.find('#phone')
    const customername = wrapper.find('#customerName')
    const jobname = wrapper.find('#jobName')
    const submit = wrapper.find('#submit')
    phone.setValue('4807034902')
    customername.setValue('')
    jobname.setValue('')
    // phone.trigger('blur')

    expect(wrapper.find("#submit").attributes().disabled).toBe('disabled')


  })

  it('the submit button should be disabled when only the job name field is filled out and the other fields are blank', function () {
    const wrapper = shallowMount(InitiateBid, {
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
    const phone = wrapper.find('#phone')
    const customername = wrapper.find('#customerName')
    const jobname = wrapper.find('#jobName')
    const submit = wrapper.find('#submit')
    phone.setValue('')
    customername.setValue('mookie blaylock')
    jobname.setValue('')
    // phone.trigger('blur')

    expect(wrapper.find("#submit").attributes().disabled).toBe('disabled')
  })

  it('the submit button should be disabled when only the job name and phone field is filled out and the other fields are blank', function () {
    const wrapper = shallowMount(InitiateBid, {
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
    const phone = wrapper.find('#phone')
    const customername = wrapper.find('#customerName')
    const jobname = wrapper.find('#jobName')
    const submit = wrapper.find('#submit')
    phone.setValue('4807034902')
    customername.setValue('')
    jobname.setValue('my job')
    // phone.trigger('blur')

    expect(wrapper.find("#submit").attributes().disabled).toBe('disabled')
  })

  it('the submit button should be disabled when only the job name and customer name field is filled out and the other fields are blank', function () {
    const wrapper = shallowMount(InitiateBid, {
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
    const phone = wrapper.find('#phone')
    const customername = wrapper.find('#customerName')
    const jobname = wrapper.find('#jobName')
    const submit = wrapper.find('#submit')
    phone.setValue('')
    customername.setValue('ajskdlsdakj')
    jobname.setValue('my job')
    // phone.trigger('blur')

    expect(wrapper.find("#submit").attributes().disabled).toBe('disabled')
  })

  it('the submit button should not be disabled when the phone and customer name field is filled out and the other fields are blank', function () {
    const wrapper = shallowMount(InitiateBid, {
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
    const phone = wrapper.find('#phone')
    const customername = wrapper.find('#customerName')
    const jobname = wrapper.find('#jobName')
    const submit = wrapper.find('#submit')
    phone.setValue('4807034902')
    customername.setValue('ajskdlsdakj')
    jobname.setValue('')
    // phone.trigger('blur')

    expect(wrapper.find("#submit").attributes().disabled).toBe(undefined)
  })

  it('should show error if phone number is bad and the submit button is clicked', function () {
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

    const name = wrapper.find('#customerName');
    name.setValue('long john silver');
    expect(wrapper.vm.form.customerName).toBe('long john silver');
  })

  it('should show phone number for Shara Barnett', function() {
    const wrapper = mount(InitiateBid, {
      store,
      localVue
    });
    wrapper.setData({
      form: new SparkForm({
        email: '',
        quickbooks_id: '',
        phone: '',
        customerName: '',
        jobName: ''
      })
    })
    let result = JSON.parse('{"id":3,"location_id":3,"name":"Shara Barnett","email":"Shara@Barnett.com","usertype":"customer","password_updated":0,"photo_url":"https://www.gravatar.com/avatar/477300cee74332fecb664d30a163c37f.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4807034902","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-04-13 16:30:51","updated_at":"2019-04-13 16:30:51","first_name":"Shara","last_name":"Barnett","tax_rate":0}');
    wrapper.vm.fillFields(result);
    expect(wrapper.$data.form.email).toBe('Shara@Barnett.com');
  })

  it.skip('should show an error if the same phone number is for a different customer and the submit button is sent', function() {
    
  })

  it.skip('should show an error if the customer name exists and the phone number is wrong and the submit button is pressed', function() {
    
  })

  it.skip('should only show name of customer in drop down that exists for this particular contractor', function() {

  })

  it.skip('should not show the name of the customer ' +
    'if the customer is not associated with that contractor once the form is submitted', function() {

  })

  it.skip('should send a contractor to the subscription page and not ' +
    'create a new bid if the sixth free bid trying to be sent', function () {

  })

  it.skip('should create a unique job name if bid is created and ' +
    'the job name is left blank', function () {

  })
  
})
