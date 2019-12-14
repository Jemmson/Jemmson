import {
  createLocalVue,
  shallowMount
} from '@vue/test-utils'
import sinon from 'sinon'
import Vuex from 'vuex'
import SubInviteModal from '../../resources/assets/js/components/task/SubInviteModal'
import moxios from 'moxios'

// require('../../bootstrap')
const localVue = createLocalVue()
localVue.use(Vuex)

describe('SubInviteModal', () => {

  let actions = {
    checkMobileNumber: () => ''
  }
  let mutations = {
    setTheMobileResponse: () => 'hell',
  }
  let getters = {
    getMobileValidResponse: () => ['phone', 'mobile', 'land'],
  }
  let store = new Vuex.Store({
    state: {},
    actions,
    getters,
    mutations
  })

  let wrapper = {};
  const sendSubInviteToBidOnTask = sinon.spy()
  const autoComplete = sinon.spy()

  wrapper = shallowMount(SubInviteModal, {
    methods: {
      sendSubInviteToBidOnTask,
      autoComplete
    },
    store,
    localVue,
    propsData: {
      jobTask: {
        bid_contractor_job_tasks: [],
        task: {
          name: 'Clean Pool'
        }
      }
    },
    data: () => {
      return {
        results: [
          {
            id: 1,
            name: 'Jane',
            email: 'jane@email.com',
            phone: '4903477834',
            contractor: {
              ccompany_name: 'ha'
            }
          }
        ]
      }
    }
  })

  beforeEach(() => {
    moxios.install()


  })

  afterEach(() => {
    moxios.uninstall()
  })

  it('Should render itself', () => {

    expect(wrapper.isEmpty()).toBe(false)
  })

  it('Should have tried to send a sub invite', () => {
    const submit = wrapper.find({
      ref: 'submit',
    })
    submit.trigger('click')

    expect(sendSubInviteToBidOnTask.calledOnce).toBe(true)
  })

  it('Should have tried to get existing subcontractors with the name in the name field', () => {
    const submit = wrapper.find('#contractorName')
    submit.trigger('keyup')

    expect(autoComplete.calledOnce).toBe(true)
  })

  it('Should fill input fields when a result is clicked', () => {

    wrapper.setData({
      results: [
        {
          name: 'Jane',
          email: 'jane@email.com',
          phone: '4903477834',
          contractor: {
            companyName: 'Jane\'s Company'
          }
        }
      ],
      initiateBidForSubForm: {
        name: '',
        email: '',
        phone: '',
        company_name: ''
      }
    })

    const subButton = wrapper.find(
      '#result0'
    )

    subButton.trigger('click')

    expect(wrapper.vm.initiateBidForSubForm.name).toBe('Jane')
    expect(wrapper.vm.initiateBidForSubForm.email).toBe('jane@email.com')
    expect(wrapper.vm.initiateBidForSubForm.phone).toBe('4903477834')
  })

  it('should show preferred payment method', function() {
    let ppm = wrapper.find('#preferredPaymentMethod')

    console.log(ppm.exists())

    expect(ppm.exists()).toBe(true)
  })

  it('should show whether a phone number is mobile or not', function() {

  })

})