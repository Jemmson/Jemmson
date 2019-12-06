import {shallowMount} from '@vue/test-utils'
import expect from 'expect'
import Home from '../../resources/assets/js/pages/Home.vue'

console.log('home.spec')

describe ('Home', () => {
  let wrapper;

  beforeEach (() => {
    wrapper = shallowMount(Home, {
      propsData: {
        user: {
          contractor: {
            stripe_express: null
          },
          usertype: 'contractor',
          name: 'KPS Pools'
        }
      }
    });

    wrapper.setData({
      bids: [],
      invoices: [],
      tasks: [],
      sBids: 0,
      sTasks: 0,
      sInvoices: 0
    });
  })

  it('stripe should return false if stripe_express is null', function() {

    wrapper.setProps({
      user: {
        contractor: {
          stripe_express: null
        },
        usertype: 'contractor',
        name: 'KPS Pools'
      }
    })

    expect(wrapper.vm.checkContractorStripeIsValid()).toBe(false);

  })


  it('stripe should return true if stripe_express is undefined', function() {

    wrapper.setProps({
      user: {
        contractor: {
          stripe_express: undefined
        },
        usertype: 'contractor',
        name: 'KPS Pools'
      }
    })

    expect(wrapper.vm.checkContractorStripeIsValid()).toBe(false);

  })


  it('stripe should return true if stripe_express is not null ' +
    'or undefined', function() {

    wrapper.setProps({
      user: {
        contractor: {
          stripe_express: 'sdjkjkdjksdkjsdkjsd'
        },
        usertype: 'contractor',
        name: 'KPS Pools'
      }
    })

    expect(wrapper.vm.checkContractorStripeIsValid()).toBe(true);

  })

});

// describe ('Home', () => {
//   // let wrapper;
//
//   beforeEach (() => {
//     // wrapper = mount(Home);
//   })
//
//   const wrapper = mount (Home, {
//     computed: {
//       job: {
//
//       }
//     },
//     propsData: {
//       user: {
//         usertype: 'contractor',
//         contractor: {
//           company_name: 'KPS Pools'
//         }
//       }
//     }
//   });
//
//   it ('should show that 1 bid is initiated', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.initiated'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Initiated');
//   });
//
//   it ('should show that 2 bids are initiated', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.initiated'},
//         {'status': 'bid.initiated'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 are Initiated');
//   });
//
//   it ('should show that 1 bid is in progress', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.in_progress'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 is in Progress');
//   });
//
//   it ('should show that 2 bids are in progress', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.in_progress'},
//         {'status': 'bid.in_progress'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 are in Progress');
//   });
//
//   it ('should show that 1 bid is sent', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.sent'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Sent');
//   });
//
//   it ('should show that 2 bids have been sent', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.sent'},
//         {'status': 'bid.sent'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Sent');
//   });
//
//   it ('should show that 1 bid is declined', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.declined'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Declined');
//   });
//
//   it ('should show that 2 bids have been declined', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'bid.declined'},
//         {'status': 'bid.declined'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Declined');
//   });
//
//   it ('should show that 1 bid is approved', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'job.approved'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Approved');
//   });
//
//   it ('should show that 2 bids have been approved', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'job.approved'},
//         {'status': 'job.approved'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 are Approved');
//   });
//
//   it ('should show that 1 bid is completed', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'job.completed'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Completed');
//   });
//
//   it ('should show that 2 bids have been completed', function () {
//     wrapper.setData ({
//       bids: [
//         {'status': 'job.completed'},
//         {'status': 'job.completed'}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 are Completed');
//   });
//
//   it ('should show that 1 task is completed', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.initiated'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Initiated');
//   });
//
//   it ('should show that 2 tasks have been completed', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.initiated'}},
//         {'job_task': {'status': 'bid_task.initiated'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 are Initiated');
//   });
//
//   it ('should show that 1 task has sent a bid', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.bid_sent'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Sent');
//   });
//
//   it ('should show that 2 tasks have sent a bid', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.bid_sent'}},
//         {'job_task': {'status': 'bid_task.bid_sent'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Sent');
//   });
//
//   it ('should show that 1 task has has been accepted', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.accepted'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Accepted');
//   });
//
//   it ('should show that 2 tasks have been accepted', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.accepted'}},
//         {'job_task': {'status': 'bid_task.accepted'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Accepted');
//   });
//
//   it ('should show that 1 task has has been finished by the sub', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.finished_by_sub'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Finished by the Sub');
//   });
//
//   it ('should show that 2 tasks have been finished by the sub', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.finished_by_sub'}},
//         {'job_task': {'status': 'bid_task.finished_by_sub'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Finished by the Sub');
//   });
//
//   it ('should show that 1 task has has been Approved by the General Contractor', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.approved_by_general'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Approved by the General Contractor');
//   });
//
//   it ('should show that 2 tasks have been Approved by the General Contractor', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.approved_by_general'}},
//         {'job_task': {'status': 'bid_task.approved_by_general'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Approved by the General Contractor');
//   });
//
//   it ('should show that 1 task has has been Finished by the General Contractor', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.finished_by_general'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Finished by the General Contractor');
//   });
//
//   it ('should show that 2 tasks have been Finished by the General Contractor', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.finished_by_general'}},
//         {'job_task': {'status': 'bid_task.finished_by_general'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Finished by the General Contractor');
//   });
//
//   it ('should show that 1 task has has been Approved by the Customer', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.approved_by_customer'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Approved by the Customer');
//   });
//
//   it ('should show that 2 tasks have been Approved by the Customer', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.approved_by_customer'}},
//         {'job_task': {'status': 'bid_task.approved_by_customer'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Approved by the Customer');
//   });
//
//   it ('should show that 1 customer has sent a payment ', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.customer_sent_payment'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has had the Customer Send Payment');
//   });
//
//   it ('should show that 1 task has has been Reopened', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.reopened'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Reopened');
//   });
//
//   it ('should show that 2 tasks have been Reopened', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.reopened'}},
//         {'job_task': {'status': 'bid_task.reopened'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Reopened');
//   });
//
//   it ('should show that 1 task has been Denied', function () {
//     wrapper.setData ({
//       tasks: [{
//         'job_task': {'status': 'bid_task.denied'}
//       }]
//     })
//     expect (wrapper.html ()).toContain ('1 has been Denied');
//   });
//
//   it ('should show that 2 tasks have been Denied', function () {
//     wrapper.setData ({
//       tasks: [
//         {'job_task': {'status': 'bid_task.denied'}},
//         {'job_task': {'status': 'bid_task.denied'}}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 have been Denied');
//   });
//
//   it ('should show that there are no invoices', function () {
//     wrapper.setData ({
//       invoices: []
//     })
//     expect (wrapper.html ()).toContain ('0 Invoices');
//   });
//
//   it ('should show that there is 1 invoice', function () {
//     wrapper.setData ({
//       invoices: [
//         {id: 1}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('1 Invoice');
//   });
//
//   it ('should show that there are 2 invoices', function () {
//     wrapper.setData ({
//       invoices: [
//         {id: 1},
//         {id: 2}
//       ]
//     })
//     expect (wrapper.html ()).toContain ('2 Invoices');
//   });
//
//   it ('should show Bids and Invoices if the user is a customer', function () {
//     wrapper.setProps ({
//       user: {
//         usertype: 'customer',
//         contractor: null
//       }
//     });
//     wrapper.setData ({})
//     expect (wrapper.html ()).toContain ('Bids');
//     expect (wrapper.html ()).toContain ('Invoices');
//     expect (wrapper.html ()).not.toContain ('Tasks');
//     expect (wrapper.html ()).not.toContain ('Stripe');
//   });
//
//   it ('should show Bids, Invoices, Tasks, and Stripe if the user is a Contractor', function () {
//     wrapper.setProps ({
//       user: {
//         usertype: 'contractor',
//         contractor: ''
//       }
//     });
//     wrapper.setData ({})
//     expect (wrapper.html ()).toContain ('Bids');
//     expect (wrapper.html ()).toContain ('Invoices');
//     expect (wrapper.html ()).toContain ('Tasks');
//     expect (wrapper.html ()).toContain ('Stripe');
//   });
//
//   it ('should show customers name', function () {
//     wrapper.setProps ({
//       user: {
//         name: 'Shawn',
//         contractor: null,
//       }
//     });
//     expect (wrapper.html ()).toContain ('Shawn');
//   });
//
//   it ('should show contractors company name', function () {
//     wrapper.setProps ({
//       user: {
//         name: 'Shawn',
//         usertype: 'contractor',
//         contractor: {
//           company_name: 'KPS Pools'
//         },
//       }
//     });
//     expect (wrapper.html ()).toContain ('Shawn');
//     expect (wrapper.html ()).toContain ('KPS Pools');
//   });
//
//   it ('should show stripe if they are contractor and stripe is not null', function () {
//     wrapper.setProps ({
//       user: {
//         name: 'Shawn',
//         usertype: 'contractor',
//         contractor: {
//           company_name: 'KPS Pools',
//           stripe_express: {}
//         },
//       }
//     });
//     expect (wrapper.html ()).toContain ('Stripe');
//     expect (wrapper.html ()).toContain ('KPS Pools');
//   });
//
//
//   // it ('should show that 2 tasks have been Approved by the Customer', function () {
//   //   wrapper.setData ({
//   //     tasks: [
//   //       {'job_task': {'status': 'bid_task.approved_by_customer'}},
//   //       {'job_task': {'status': 'bid_task.approved_by_customer'}}
//   //     ]
//   //   })
//   //   expect (wrapper.html ()).toContain ('2 have been Approved by the Customer');
//   // });
//
//
//   // it('Contractor not connected to Stripe rendering the correct markup', () => {
//   // const wrapper = mount(Home, {
//   //   propsData: {
//   //     user:{
//   //       usertype: 'contractor',
//   //       contractor: {
//   //         company_name: 'KPS Pools',
//   //         stripe_express: null,
//   //       }
//   //     }
//   //   }
//   // })
//   // const vm = wrapper.vm;
//   // // console.log(vm)
//   // console.log(vm.propsData)
//   // console.log(wrapper);
//   // expect(wrapper.html()).toContain(`Initiate a Bid Here`)
//   // expect(wrapper.html()).toContain(`Look at Bids Here`)
//   // expect(wrapper.html()).toContain(`Look at Tasks Here`)
//   // expect(wrapper.html()).toContain(`Past Invoices`)
//   // expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
//   // })
//
//   // it('Contractor connected to Stripe renders the correct markup', () => {
//   //   const wrapper = mount(Home, {
//   //     propsData: {
//   //       user:{
//   //         usertype: 'contractor',
//   //         contractor: {
//   //           company_name: 'KPS Pools',
//   //           stripe_express: true,
//   //         }
//   //       }
//   //     }
//   //   })
//   //   expect(wrapper.html()).toContain(`Initiate a Bid Here`)
//   //   expect(wrapper.html()).toContain(`Look at Bids Here`)
//   //   expect(wrapper.html()).toContain(`Look at Tasks Here`)
//   //   expect(wrapper.html()).toContain(`Past Invoices`)
//   //   expect(wrapper.html()).toContain(`Stripe Dashboard`)
//   // })
//   //
//   // it('Customer not connected to Stripe rendering the correct markup', () => {
//   //   const wrapper = mount(Home, {
//   //     propsData: {
//   //       user:{
//   //         usertype: 'customer',
//   //         customer: null
//   //       }
//   //     }
//   //   })
//   //   expect(wrapper.html()).not.toContain(`Initiate a Bid Here`)
//   //   expect(wrapper.html()).not.toContain(`Look at Bids Here`)
//   //   expect(wrapper.html()).not.toContain(`Look at Tasks Here`)
//   //   expect(wrapper.html()).not.toContain(`Past Invoices`)
//   //   expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
//   // })
//   //
//   // it('Customer connected to Stripe renders the correct markup', () => {
//   //   const wrapper = mount(Home, {
//   //     propsData: {
//   //       user:{
//   //         usertype: 'customer',
//   //         customer: true
//   //       }
//   //     }
//   //   })
//   //   expect(wrapper.html()).toContain(`Look at Bids Here`)
//   //   expect(wrapper.html()).toContain(`Past Invoices`)
//   //   expect(wrapper.html()).not.toContain(`Initiate a Bid Here`)
//   //   expect(wrapper.html()).not.toContain(`Look at Tasks Here`)
//   //   expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
//   // })
//
//   // it ('only show customer and contractor fields if the user is a customer', () => {
//   //   expect ()
//   // });
//
//   // afterEach (() => {
//   //
//   // })
//
//   // it ('displays all fields if the user is a contractor', () => {
//   //   // expect (wrapper.html()).toBe('Item One</h2>')
//   //   // wrapper.setData({
//   //   //   user: 'contractor'
//   //   // })
//   //   // expect(wrapper.find('h2').text()).toContain('Item One')
//   // });
//   //
//   // it ('only show customer and contractor fields if the user is a customer', () => {
//   //   expect ()
//   // });
//   //
//   // it ('only shows contractor and sub fields if the user is a subcontractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('only shows tasks related to the contractor and the sub if the user is a sub contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('will have the customer proposed price field as editable if the user is a customer', () => {
//   //   expect ()
//   // });
//   //
//   // it ('will have the contractor customer proposed price field and the contractor sub proposed price field as editable if the user is a contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('will only show the final price to customer and contractor if the accepted button has been clicked by both', () => {
//   //   expect ()
//   // });
//   //
//   // it ('will only show final price for contractor and sub if the accept button as been clicked by both', () => {
//   //   expect ()
//   // });
//   //
//   // it ('show accept button if customer has null for for cust_cont_accepted field', () => {
//   //   expect ()
//   // });
//   //
//   // it ('show accept button if contractor has null for for cont_sub_accepted field', () => {
//   //   expect ()
//   // });
//   //
//   // it ('show accept button if contractor has null for for cust_sub_accepted field', () => {
//   //   expect ()
//   // });
//   //
//   // it ('show accept button if sub has null for for sub_cont_accepted field', () => {
//   //   expect ()
//   // });
//   //
//   // it ('show add task display for contractor when add task button is pushed', () => {
//   //   expect ()
//   // });
//   //
//   // it ('trigger notification to customer when the submit button has been clicked and changes have ' +
//   //   'been made to the customer fields by a contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('trigger notification to subcontractor when submit button has been clicked and changes ' +
//   //   'have been made to sub fields by a contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('trigger notification to contractor when submit button has been clicked and changes ' +
//   //   'have been made to contractor fields by a sub', () => {
//   //   expect ()
//   // });
//   //
//   // it ('trigger notification to contractor when submit button has been clicked and changes have been made ' +
//   //   'to the contractor fields by a customer', () => {
//   //   expect ()
//   // });
//   //
//   // it ('a new task shows up in the task list section when submit task has been clicked', () => {
//   //   expect ()
//   // });
//   //
//   // it ('display fields that relate to the sub if the user is a contractor and a task is being added for a sub to look at it', () => {
//   //   expect ()
//   // });
//   //
//   // it ('if a sub is selected for a task then the customer cannot see a price until the accept button for both ' +
//   //   'for contractor and sub has been selected', () => {
//   //   expect ()
//   // });
//   //
//   // it ('sub cannot propose a new price if the accept button has been selected by the contractor and the customer', () => {
//   //   expect ()
//   // });
//   //
//   // it ('sub connot see price that contractor has set between the customer and the contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('when a task name is input then a drop down will show of possibilities that are specific ' +
//   //   'to the contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('if a task name is input and it is not assigned to the contractor then the task ' +
//   //   'will be added to the tasks of the contractor', () => {
//   //   expect ()
//   // });
//   //
//   // it ('sub info only shows up if a sub has been selected for a job', () => {
//   //   expect ()
//   // });
//
// })