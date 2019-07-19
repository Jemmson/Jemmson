import {
  shallowMount
} from '@vue/test-utils'
import BidDetails from '../../resources/assets/js/components/job/BidDetails'
// import Customer from '../../../../resources/assets/js/classes/Customer'
import User from '../../resources/assets/js/classes/User'

import sinon from 'sinon'
import expect from 'expect'

require('./bootstrap')

describe('BidDetails', () => {

  const initializePayWithCashMessageValueStub = sinon.stub()
  const updateGeneralContractorNotesStub = sinon.stub()
  const submitPayWithCashMessageStub = sinon.stub()
  // const User.status = sinon.stub()

  const wrapper = shallowMount(BidDetails, {
    // methods: {
    //   status: sinon.stub()
    // },
    propsData: {
      isCustomer: false,
      customerName: 'Jane Doe',
      bid: {
        job_name: 'Pool Job',
        customer: {
          customer: {
            notes: 'Eaque rerum omnis odio ipsum doloremque unde. Ex reiciendis voluptas omnis. Sed neque ut voluptas voluptatem accusantium beatae dolorem.'
          },
        },
        location: {
          address_line_1: '',
          city: '',
          state: '',
          zip: ''
        },
        paid_with_cash_message: 'Pay In Person',
        status: 'bid.initiated',
        in_progress: true,
        bid_price: 99.00,
        declined_message: null,
        agreed_start_date: '2018-08-01 10:58:37'
      }
    }
  })

  it('if user is a customer the contractor name should show and the customer name should be hidden', function() {

    wrapper.setProps({
      isCustomer: true,
      customerName: 'Joe Customer',
      bid: {
        status: 'bid.initiated'
      }
    })

    expect(wrapper.find({ref: 'details_customer_name'}).exists()).toBe(false)
    expect(wrapper.find({ref: 'details_contractor_name'}).exists()).toBe(true)
    // expect(wrapper.find({ref: 'details_start_date'}).exists()).toBe(true)
    // expect(wrapper.find({ref: 'details_total_bid_price'}).exists()).toBe(true)
    // expect(wrapper.find({ref: 'details_contractor_customer_accepted_price'}).exists()).toBe(true)

  })

  it('if user is a contractor the customer name should show and the contractor name should be hidden', function() {

    wrapper.setProps({
      isCustomer: false,
      contractorName: 'Joe Customer',
      bid: {
        status: 'bid.initiated'
      }
    })

    expect(wrapper.find({ref: 'details_customer_name'}).exists()).toBe(true)
    expect(wrapper.find({ref: 'details_contractor_name'}).exists()).toBe(false)
    // expect(wrapper.find({ref: 'details_start_date'}).exists()).toBe(true)
    // expect(wrapper.find({ref: 'details_total_bid_price'}).exists()).toBe(true)
    // expect(wrapper.find({ref: 'details_contractor_customer_accepted_price'}).exists()).toBe(true)

  })

  it('if start date not set then show "Not Set"', function() {

    wrapper.setProps({
      bid: {
        agreed_start_date: null
      }
    })

    expect(wrapper.find({ref: 'details_start_date'}).text()).toBe('Not Set')

  })

  it('if bid status is bid.initiated then bid_price should show "In Process"', function() {

    wrapper.setProps({
      isCustomer: false,
      customerName: 'Joe Customer',
      bid: {
        status: 'bid.initiated'
      }
    })

    expect(wrapper.find({ref: 'details_total_bid_price'}).text()).toBe('In Process')

  })

  it('if bid status is bid.in_progress then bid_price should show "In Process"', function() {

    wrapper.setProps({
      isCustomer: false,
      customerName: 'Joe Customer',
      bid: {
        status: 'bid.in_progress'
      }
    })

    expect(wrapper.find({ref: 'details_total_bid_price'}).text()).toBe('In Process')

  })

  it('if bid status is not bid.initiated or bid.in_progress then bid_price should show the bid price', function() {

    wrapper.setProps({
      isCustomer: false,
      customerName: 'Joe Customer',
      bid: {
        status: 'bid.submitted',
        bid_price: 1000
      }
    })

    expect(wrapper.find({ref: 'details_total_bid_price'}).text()).toBe('$ 10.00')

  })

  it('should show payment Instructions when the cash drop down is selected', function() {

    wrapper.setData({
      selectedPayment: 'cash'
    })

    expect(wrapper.find({ref: 'paymentInstructions'}).exists()).toBe(true)

  })

  it('should show payment Instructions when the credit card drop down is selected', function() {

    wrapper.setData({
      selectedPayment: 'creditCard'
    })

    expect(wrapper.find({ref: 'paymentInstructions'}).exists()).toBe(false)

  })

  it('should trigger payment with cash method when submit button is triggered', function() {

    wrapper.setData({
      selectedPayment: 'cash'
    })

    wrapper.setMethods({
      submitPayWithCashMessage: submitPayWithCashMessageStub
    })

    let paywithCashButton = wrapper.find({ref: 'paywithCashButton'})

    paywithCashButton.trigger('click')

    expect(submitPayWithCashMessageStub.called).toBe(true)

  })

  it.skip('should initialize with paid_with_cash_message from the database for the customer if the message is not null', function() {

    // TODO: Test should be working. not sure why it is not

    wrapper.setProps({
      bid: {
        paid_with_cash_message: 'pay in person'
      }
    })

    wrapper.setData({
      selectedPayment: 'cash',
      payWithCashMessage: ''
    })

    wrapper.setMethods({
      initializePayWithCashMessageValue: initializePayWithCashMessageValueStub
    })

    expect(wrapper.vm.$data.payWithCashMessage).toBe('Pay In Person')

  })

  it('should not see the job address section if the user is a customer', function() {

    wrapper.setProps({
      isCustomer: false,
      bid: {
        location_id: 3,
        location: {
          address_line_1: '705 E Oxford Dr.',
          address_line_2: '',
          area: 'Tempe',
          city: 'Tempe',
          country: null,
          created_at: '2019-07-11 04:55:03',
          default: 1,
          id: 3,
          lat: null,
          long: null,
          state: 'AZ',
          updated_at: '2019-07-11 04:55:03',
          user_id: 8,
          zip: '85283'
        }
      }
    })

    expect(wrapper.find({ref: 'job_address'}).exists()).toBe(true)

    wrapper.setProps({
      isCustomer: true,
      bid: {
        location_id: 3,
        location: {
          address_line_1: '705 E Oxford Dr.',
          address_line_2: '',
          area: 'Tempe',
          city: 'Tempe',
          country: null,
          created_at: '2019-07-11 04:55:03',
          default: 1,
          id: 3,
          lat: null,
          long: null,
          state: 'AZ',
          updated_at: '2019-07-11 04:55:03',
          user_id: 8,
          zip: '85283'
        }
      }
    })

    expect(wrapper.find({ref: 'job_address'}).exists()).toBe(false)

  })

  it.skip('a customer should be able to update the job address during the bid.initiated and the bid.submitted stages', function() {

  })

  it.skip('should call the update customer notes button when the customer push button in the update customer notes section', function() {

    // TODO: test should work but it doesnt

    wrapper.setMethods({
      updateGeneralContractorNotes: updateGeneralContractorNotesStub
    })

    let btn = wrapper.find({
      ref: 'update_customer_notes_button'
    })

    btn.trigger('click');

    expect(updateGeneralContractorNotesStub.called).toBe(true)

  })

  it('should show total number of job tasks when the job tasks is not undefined', function() {

    wrapper.setProps({
      isCustomer: true,
      bid: {
        job_tasks: [
          {task_id: 1},
          {task_id: 2},
          {task_id: 3}
        ]
      }
    })

    expect(wrapper.find({ref: 'job_task_length'}).text()).toBe('3')

  })

  it('should not show add new task if the user is a customer', function() {

    wrapper.setProps({isCustomer: true})

    expect(wrapper.find({ref: 'add_new_task'}).exists()).toBe(false);

    wrapper.setProps({isCustomer: false})

    expect(wrapper.find({ref: 'add_new_task'}).exists()).toBe(true);


  })

  it.only('should show save message in the customernotes variable when notes are being entered', function() {
    wrapper.setData({
      customerNotesMessage: ''
    })

    let textarea = wrapper.find({ ref: 'message_text_area'})

    textarea.setValue('my message')

    expect(wrapper.vm.$data.customerNotesMessage).toBe('my message');

  })

  it.skip('should not be able to submit customer notes if the notes are blank', function() {

  })

  it.skip('should be able to delete notes for the customer notes', function() {

  })

  // it.only('should hide one item', function() {
  //
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //
  //   expect(wrapper.find({ref: 'hello'}).isVisible()).toBe(false)
  //
  // })
  //
  // it('should hide one item with a parent div element', function() {
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   expect(wrapper.find({ref: 'hello'}).isVisible()).toBe(false)
  // })
  //
  // it('should hide one item with a parent div element', function() {
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'hello'}).isVisible()).toBe(false)
  // })
  //
  // it('should hide one item with a parent div element', function() {
  //   wrapper.setData({
  //     customerNotes: true
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'hello'}).isVisible()).toBe(false)
  // })
  //
  // it('should hide one item with a parent div element', function() {
  //   wrapper.setData({
  //     customerNotes: true
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   expect(wrapper.find({ref: 'hello'}).isVisible()).toBe(true)
  // })
  //
  // it('should show customer notes for job when the button is clicked', function() {
  //   // given
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   wrapper.setData({
  //     customerNotes: true
  //   })
  // })
  //
  //
  // //#######################################
  // //# Customer Notes Button
  // //#######################################
  //
  // it('as a customer I should see the customer notes button', function() {
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   expect(wrapper.find({
  //     ref: 'notesForCustomerButton_customer'
  //   }).isVisible())
  //
  // })
  //
  // it('as a contractor I should not see the customer notes button', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({
  //     ref: 'notesForCustomerButton_customer'
  //   }).isVisible()).toBe(false)
  //
  // })
  //
  //
  // //#######################################
  // //# Customer Notes Text Area
  // //#######################################
  //
  // it('customerNotes data field should turn from false to true customer Notes button click', function() {
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   expect(wrapper.vm.customerNotes).toBe(false)
  //
  //   let customerNotesButton = wrapper.find({
  //     ref: 'notesForCustomerButton_customer'
  //   })
  //
  //   customerNotesButton.trigger('click')
  //
  //   expect(wrapper.vm.customerNotes).toBe(true)
  // })
  //
  // it('as a customer when I click on the customer notes button then I should see the customer notes textarea', function() {
  //
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   let customerNotesButton = wrapper.find({
  //     ref: 'notesForCustomerButton_customer'
  //   })
  //
  //   customerNotesButton.trigger('click')
  //
  //   expect(wrapper.find({
  //     ref: 'customerNotesTextArea'
  //   }).isVisible()).toBe(true)
  //
  // })
  //
  // it('as a customer when I click the submit button then the updateGeneralContractorNotes method should be called', function() {
  //
  //   const updateGeneralContractorNotes = sinon.spy()
  //
  //   wrapper.setMethods({
  //     updateGeneralContractorNotes
  //   })
  //
  //   wrapper.setData({
  //     customerNotes: true
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   let submit = wrapper.find({
  //     ref: 'customerNotesSubmitButton'
  //   })
  //
  //   submit.trigger('click')
  //
  //   expect(updateGeneralContractorNotes.calledOnce).toBe(true)
  //
  // })
  //
  //
  // it ('as a contractor I should not see both customer message and the default message', () => {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   wrapper.setProps({
  //     customerNotes_contractor: true,
  //     bid: {
  //       customer: {
  //         customer: {
  //           notes: 'Pay In Person'
  //         }
  //       }
  //     }
  //   })
  //
  //   expect(wrapper.find({ref: 'customerNotesInfo_contractor_empty'}).isVisible()).toBe(true);
  //   expect(wrapper.find({ref: 'customerNotesInfo_contractor'}).isVisible()).toBe(false);
  //
  // });
  //
  // it('as a contractor I should be able to see the Customer Notes for Job - Contractor button ', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'notesForCustomerButton_contractor'}).isVisible()).toBe(true)
  // })
  //
  // it('as a contractor I should not be able to see the Customer Notes for Job - Customer button ', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'notesForCustomerButton_customer'}).isVisible()).toBe(false)
  // })
  //
  // it('as a contractor I should not be able to see the customer notes text area', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'customerNotesTextArea'}).isVisible()).toBe(false)
  //
  // })
  //
  // it('as a contractor I should not be able to see the customer notes text area button', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   expect(wrapper.find({ref: 'customerNotesSubmitButton'}).isVisible()).toBe(false)
  //
  // })
  //
  // it('as a contractor I should not be able to see the customer notes information', function() {
  //   wrapper.setProps({
  //     isCustomer: false
  //   })
  //
  //   wrapper.setData({
  //     customerNotes_contractor: false
  //   })
  //
  //   expect(wrapper.find({ref: 'customerNotesTextArea'}).isVisible()).toBe(false)
  // })
  //
  // it('as a contractor I should be able to see the customer notes information when the notesForCustomerButton_contractor is clicked and the customer has notes', function() {
  //   wrapper.setProps({
  //     isCustomer: false,
  //     bid: {
  //       customer: {
  //         customer: {
  //           notes: 'Pay In Person'
  //         }
  //       }
  //     }
  //   })
  //
  //   wrapper.setData({
  //     customerNotes_contractor: false
  //   })
  //
  //   let btn = wrapper.find({
  //     ref: 'notesForCustomerButton_contractor'
  //   })
  //
  //   btn.trigger('click')
  //
  //   expect(wrapper.find({ref: 'customerNotesInfo_contractor'}).isVisible()).toBe(true)
  //
  // })
  //
  //
  //
  //
  // it('as a contractor with the status as - bid.initiated - the screen should show - bid in progress', () => {
  //
  //   const getLabelClass = sinon.spy()
  //
  //   wrapper.setProps({
  //     isCustomer: false,
  //     bid: {
  //       status: 'bid.initiated'
  //     }
  //   })
  //
  //   wrapper.setMethods({
  //     getLabelClass
  //   })
  //
  //   let bs = wrapper.find({
  //     ref: 'bidStatus'
  //   })
  //
  //   expect(getLabelClass.calledOnce).toBe(true)
  //
  // })
  //
  // it('as a contractor I should be able to see the paid with cash button if the customer paid with cash', () => {
  //   wrapper.setProps({
  //     isCustomer: false,
  //     bid: {
  //       paid_with_cash_message: 'Pay In Person'
  //     }
  //   })
  //
  //   console.log(wrapper.vm.$props.bid.paid_with_cash_message)
  //
  //   let pwcm = wrapper.find({ref: 'paidWithCashBlock'});
  //
  //   // console.log(pwcm.html())
  //
  //   expect(wrapper.find({ref: 'paidWithCashBlock'}).isVisible()).toBe(true);
  //
  //
  //   // let paidWithCashBtn = wrapper.find({ref: 'paidWithCashBtn'});
  //   // console.log(paidWithCashBtn);
  //
  //   // expect(paidWithCashBtn.isVisible()).toBe(true)
  //
  // })

  // it('should show customer notes if customer notes for job is selected', function() {
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //   const customer_notes_for_job = wrapper.find({
  //     ref: 'cnfj_1'
  //   })
  //   customer_notes_for_job.trigger('click')
  //   console.log(wrapper.vm.customerNotes)
  //   expect(wrapper.vm.customerNotes).toBe(true)
  // })
  //
  // it('should show customer notes textarea when notes for customer note is pressed', function() {
  //   wrapper.setData({
  //     customerNotes: false
  //   })
  //
  //   wrapper.setProps({
  //     bid: {
  //       paid_with_cash_message: 'pay in person'
  //     }
  //   })
  //
  //   let customerNotesForJobButton = wrapper.find({
  //     ref: 'customerNotesForJobButton'
  //   })
  //
  //   customerNotesForJobButton.trigger('click')
  //
  //   expect(wrapper.vm.customerNotes).toBe(true)

  // const customer_notes_for_job = wrapper.find({
  //   ref: 'cnfj_1'
  // })

  // console.log('wrapper.vm.customerNotes')
  // console.log(wrapper.vm.customerNotes)
  // console.log('wrapper.find({ref: \'hello\'})')
  // console.log(wrapper.find({ref: 'hello'}))
  //
  // // let w = wrapper.find({ref: 'hello'});
  //
  // wrapper.setData({
  //   customerNotes: true
  // })
  //
  // console.log('wrapper.vm.customerNotes')
  // console.log(wrapper.vm.customerNotes)
  // console.log('wrapper.find({ref: \'hello\'})')
  // console.log(wrapper.find({ref: 'hello'}))
  //
  // // let w = wrapper.find({ref: 'hello'});
  //
  // // expect(wrapper.find({ref: 'hello'})).toBe(false);
  //
  //
  // console.log(wrapper.vm.customerNotes)
  // expect(wrapper.find({ref: 'hello'})).toBe(true);

  // let textarea = wrapper.find({
  //   ref: customerNotesTextArea
  // });
  //
  // expect(customer_notes_for_job.isVisible()).toBe(true)

  //ReferenceError: customerNotesTextArea is not defined

  // })

  // TODO: figure out why set value is not working on the textarea the below test does not work with out it
  // it('as a customer when I click on the customer notes button then I should see the customer notes data', function() {
  //
  //   wrapper.setData({
  //     customerNotes: false,
  //     customerNotesMessage: 'customer notes for job'
  //   })
  //
  //   wrapper.setProps({
  //     isCustomer: true
  //   })
  //
  //   let ta = wrapper.find({
  //     ref: 'customerNotesTextArea'
  //   })
  //
  //   console.log(ta.toString())
  //   console.log(ta.html())
  //
  //
  //   let customerNotesButton = wrapper.find({
  //     ref: 'notesForCustomerButton_customer'
  //   })
  //
  //   customerNotesButton.trigger('click')
  //
  //
  //   ta.setValue('hello')
  //
  //
  //   ta.element.value = 'hello'
  //   ta.trigger('input')
  //
  //   console.log(ta.text())
  //
  //   // expect(ta.text()).toBe('customer notes for job')
  //
  // })

})