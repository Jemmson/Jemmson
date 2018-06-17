import {mount} from 'vue-test-utils'
import expect from 'expect'
import Home from '../../resources/assets/js/pages/Home.vue'

describe ('Home', () => {
  // let wrapper;
  //
  // beforeEach (() => {
  //   wrapper = mount(Home);
  // })

  const wrapper = mount (Home, {
    propsData: {
      user: {
        usertype: 'contractor',
        contractor: {
          company_name: 'KPS Pools'
        }
      }
    },
    data () {
      return {
        bids: {
          needsApproval: 6,
          inProcess: 4,
          waitingToBeFinished: 3,
          finishedJobs: 2,
        },
        tasks: {
          needsToBeBidOn: 10,
          toBeFinished: 9,
          watingOnCustomerPayment: 8,
          paid: 7,
        }
      }
    }
  });

  it ('needsApproval should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="needsApproval">6</h5>');
  });

  it ('inProcess should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="inProcess">4</h5>');
  });

  it ('waitingToBeFinished should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="waitingToBeFinished">3</h5>');
  });

  it ('finishedJobs should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="finishedJobs">2</h5>');
  });

  it ('needsToBeBidOn should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="needsToBeBidOn">10</h5>');
  });

  it ('toBeFinished should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="toBeFinished">9</h5>');
  });

  it ('watingOnCustomerPayment should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="watingOnCustomerPayment">8</h5>');
  });

  it ('paid should render the correct markup', function () {
    expect (wrapper.html ()).toContain ('<h5 class="paid">7</h5>');
  });

  // it('Contractor not connected to Stripe rendering the correct markup', () => {
  // const wrapper = mount(Home, {
  //   propsData: {
  //     user:{
  //       usertype: 'contractor',
  //       contractor: {
  //         company_name: 'KPS Pools',
  //         stripe_express: null,
  //       }
  //     }
  //   }
  // })
  // const vm = wrapper.vm;
  // // console.log(vm)
  // console.log(vm.propsData)
  // console.log(wrapper);
  // expect(wrapper.html()).toContain(`Initiate a Bid Here`)
  // expect(wrapper.html()).toContain(`Look at Bids Here`)
  // expect(wrapper.html()).toContain(`Look at Tasks Here`)
  // expect(wrapper.html()).toContain(`Past Invoices`)
  // expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
  // })

  // it('Contractor connected to Stripe renders the correct markup', () => {
  //   const wrapper = mount(Home, {
  //     propsData: {
  //       user:{
  //         usertype: 'contractor',
  //         contractor: {
  //           company_name: 'KPS Pools',
  //           stripe_express: true,
  //         }
  //       }
  //     }
  //   })
  //   expect(wrapper.html()).toContain(`Initiate a Bid Here`)
  //   expect(wrapper.html()).toContain(`Look at Bids Here`)
  //   expect(wrapper.html()).toContain(`Look at Tasks Here`)
  //   expect(wrapper.html()).toContain(`Past Invoices`)
  //   expect(wrapper.html()).toContain(`Stripe Dashboard`)
  // })
  //
  // it('Customer not connected to Stripe rendering the correct markup', () => {
  //   const wrapper = mount(Home, {
  //     propsData: {
  //       user:{
  //         usertype: 'customer',
  //         customer: null
  //       }
  //     }
  //   })
  //   expect(wrapper.html()).not.toContain(`Initiate a Bid Here`)
  //   expect(wrapper.html()).not.toContain(`Look at Bids Here`)
  //   expect(wrapper.html()).not.toContain(`Look at Tasks Here`)
  //   expect(wrapper.html()).not.toContain(`Past Invoices`)
  //   expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
  // })
  //
  // it('Customer connected to Stripe renders the correct markup', () => {
  //   const wrapper = mount(Home, {
  //     propsData: {
  //       user:{
  //         usertype: 'customer',
  //         customer: true
  //       }
  //     }
  //   })
  //   expect(wrapper.html()).toContain(`Look at Bids Here`)
  //   expect(wrapper.html()).toContain(`Past Invoices`)
  //   expect(wrapper.html()).not.toContain(`Initiate a Bid Here`)
  //   expect(wrapper.html()).not.toContain(`Look at Tasks Here`)
  //   expect(wrapper.html()).not.toContain(`Stripe Dashboard`)
  // })

  // it ('only show customer and contractor fields if the user is a customer', () => {
  //   expect ()
  // });

  // afterEach (() => {
  //
  // })

  // it ('displays all fields if the user is a contractor', () => {
  //   // expect (wrapper.html()).toBe('Item One</h2>')
  //   // wrapper.setData({
  //   //   user: 'contractor'
  //   // })
  //   // expect(wrapper.find('h2').text()).toContain('Item One')
  // });
  //
  // it ('only show customer and contractor fields if the user is a customer', () => {
  //   expect ()
  // });
  //
  // it ('only shows contractor and sub fields if the user is a subcontractor', () => {
  //   expect ()
  // });
  //
  // it ('only shows tasks related to the contractor and the sub if the user is a sub contractor', () => {
  //   expect ()
  // });
  //
  // it ('will have the customer proposed price field as editable if the user is a customer', () => {
  //   expect ()
  // });
  //
  // it ('will have the contractor customer proposed price field and the contractor sub proposed price field as editable if the user is a contractor', () => {
  //   expect ()
  // });
  //
  // it ('will only show the final price to customer and contractor if the accepted button has been clicked by both', () => {
  //   expect ()
  // });
  //
  // it ('will only show final price for contractor and sub if the accept button as been clicked by both', () => {
  //   expect ()
  // });
  //
  // it ('show accept button if customer has null for for cust_cont_accepted field', () => {
  //   expect ()
  // });
  //
  // it ('show accept button if contractor has null for for cont_sub_accepted field', () => {
  //   expect ()
  // });
  //
  // it ('show accept button if contractor has null for for cust_sub_accepted field', () => {
  //   expect ()
  // });
  //
  // it ('show accept button if sub has null for for sub_cont_accepted field', () => {
  //   expect ()
  // });
  //
  // it ('show add task display for contractor when add task button is pushed', () => {
  //   expect ()
  // });
  //
  // it ('trigger notification to customer when the submit button has been clicked and changes have ' +
  //   'been made to the customer fields by a contractor', () => {
  //   expect ()
  // });
  //
  // it ('trigger notification to subcontractor when submit button has been clicked and changes ' +
  //   'have been made to sub fields by a contractor', () => {
  //   expect ()
  // });
  //
  // it ('trigger notification to contractor when submit button has been clicked and changes ' +
  //   'have been made to contractor fields by a sub', () => {
  //   expect ()
  // });
  //
  // it ('trigger notification to contractor when submit button has been clicked and changes have been made ' +
  //   'to the contractor fields by a customer', () => {
  //   expect ()
  // });
  //
  // it ('a new task shows up in the task list section when submit task has been clicked', () => {
  //   expect ()
  // });
  //
  // it ('display fields that relate to the sub if the user is a contractor and a task is being added for a sub to look at it', () => {
  //   expect ()
  // });
  //
  // it ('if a sub is selected for a task then the customer cannot see a price until the accept button for both ' +
  //   'for contractor and sub has been selected', () => {
  //   expect ()
  // });
  //
  // it ('sub cannot propose a new price if the accept button has been selected by the contractor and the customer', () => {
  //   expect ()
  // });
  //
  // it ('sub connot see price that contractor has set between the customer and the contractor', () => {
  //   expect ()
  // });
  //
  // it ('when a task name is input then a drop down will show of possibilities that are specific ' +
  //   'to the contractor', () => {
  //   expect ()
  // });
  //
  // it ('if a task name is input and it is not assigned to the contractor then the task ' +
  //   'will be added to the tasks of the contractor', () => {
  //   expect ()
  // });
  //
  // it ('sub info only shows up if a sub has been selected for a job', () => {
  //   expect ()
  // });

})