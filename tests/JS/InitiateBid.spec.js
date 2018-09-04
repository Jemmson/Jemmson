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

  it('should display "Landline" when a landline number is put in the text box and then the blur action occurs', function () {

  })

  it('the submit button should be disabled when only the customer field is filled out and the other fields are blank', function () {

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

  it('should send a contractor to the subscription page and not create a new bid if the sixth free bid trying to be sent', function () {

  })

  it('should create a unique job name if bid is created and the job name is left blank', function () {

  })
})