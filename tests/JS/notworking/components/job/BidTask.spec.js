import {
  mount,
  createLocalVue
} from '@vue/test-utils'
import VuePaginate from 'vue-paginate'
import BidTask from '../../../../resources/assets/js/components/job/BidTask'
import User from '../../../../resources/assets/js/classes/User'

// require('../../bootstrap')

const localVue = createLocalVue()
localVue.use(VuePaginate)

describe('BidTask', () => {
  const wrapper = mount(BidTask, {
    localVue,
    stubs: [
      'card',
      'sub-invite-modal',
      'deny-task-modal',
      'update-task-location-modal',
      'task-images'
    ],
    propsData: {
      bid: {
        id: '',
        status: ''
      },
      jobTask: {
        id: 0,
        cust_final_price: 0,
        customer_message: '',
        qty: 0,
        start_date: '',
        status: '',
        sub_message: '',
        sub_final_price: 0,
        unit_price: 0,
        bid_contractor_job_tasks: {},
        task: {
          name: '',
          qty: ''
        },
      }
    }
  })

  it('Should render itself ', () => {
    expect(wrapper.vm.show).toBe(true)
  })

  it('User should be a contractor ', () => {



    //Given
    // let user = JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}');
    let contractor = JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"customer","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}')
    // console.log(Spark.state.user.usertype)

    window.User = new User(contractor)


    // wrapper.setData({
    //     globalUser: JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}')
    // });

    // wrapper.vm.globalUser = user;
    // console.log(wrapper.vm.globalUser);
    // expect(wrapper.vm.globalUser.isContractor()).toBe(Spark.state.user.usertype);
    expect(wrapper.vm.isContractor()).toBe(true)
  })

  it('User should not be a customer ', () => {

    //Given
    // let user = JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}');
    let customer = JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"customer","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}')
    // console.log(Spark.state.user.usertype)

    window.User = new User(customer)

    // console.log(wrapper.vm.$data)
    // console.log(wrapper.vm.$data)
    // console.log(wrapper.vm)

    // wrapper.setData({
    //     globalUser: JSON.parse('{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02","first_name":null,"last_name":null,"subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":3,"company_name":"Jemmson","company_logo_name":"voluptas","email_method_of_contact":1,"sms_method_of_contact":1,"phone_method_of_contact":1,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-13 06:07:38","stripe_express":{"id":1,"contractor_id":1,"access_token":"sk_test_vewQOBeHnMCFtgEjOLQmXrdD","refresh_token":"rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi","stripe_user_id":"acct_1By18sB4l1AzsWS0","created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"},"location":{"id":1,"user_id":1,"default":0,"address_line_1":"102 Hope Divide","address_line_2":null,"city":"East Jennieville","state":"Missouri","zip":"43535-3735","area":null,"created_at":"2019-01-12 04:46:02","updated_at":"2019-01-12 04:46:02"}},"customer":null,"tax_rate":0}')
    // });

    // wrapper.vm.globalUser = user;
    // console.log(wrapper.vm.globalUser);
    // expect(wrapper.vm.globalUser.isContractor()).toBe(Spark.state.user.usertype);
    expect(wrapper.vm.isContractor()).toBe(false)
  })

  it('The quantity input should be hard coded after bid was accepted', () => {
    // Given


    const qty = wrapper.find({
      ref: 'quantity',
    })

    expect(qty.attributes().disabled).toBe('disabled');
  });


  it('Messages should not be disabled', () => {
    expect(wrapper.vm.disableMessages).toBe(false);
  });


  it('The price input should be disabled - contractor', () => {
    const qty = wrapper.find({
      ref: 'price',
    })

    expect(qty.attributes().disabled).toBe('disabled');
  });


  it('Should not render the subs panel', () => {
    expect(wrapper.vm.showSubsPanel).toBe(false);
  });

  it('Should render footer slot', () => {
    expect(false).toBe(true);
  });

  // it('Should render 2 cards', () => {
  //     const cards = wrapper.findAll('card-stub');
  //     expect(cards.length).toBe(2);
  // });
  //
  // it('Should have a status-grey class', () => {
  //     const status = wrapper.findAll('.status-grey');
  //     expect(status.length).toBe(1);
  // });
  //
  // it('Should have a status-green class', () => {
  //     const status = wrapper.findAll('.status-green');
  //     expect(status.length).toBe(1);
  // });
  //
  // it('Should render the text Customer Has Sent A Payment', () => {
  //     expect(wrapper.html()).toContain('Customer Has Sent A Payment');
  // });
  //
  // it('Should render the text Waiting On Customer Approval & Payment', () => {
  //     expect(wrapper.html()).toContain('Waiting On Customer Approval &amp; Payment');
  // });
  //
  // it('Should render the text Job 1', () => {
  //     expect(wrapper.html()).toContain('Job 1');
  // });
  //
  // it('Should render the text Job 2', () => {
  //     expect(wrapper.html()).toContain('Job 2');
  // });
  //
  // it('Should render the text $100', () => {
  //     expect(wrapper.html()).toContain('$100');
  // });
  //
  // it('Should render the Total Task Sub Price section - contractor', () => {
  //     expect(wrapper.html()).toContain('Total Task Sub Price');
  // });
  //
  // it('Should render the Quantity: section - contractor', () => {
  //     expect(wrapper.html()).toContain('Quantity:');
  // });
  //
  // it('The quantity input should be disabled - contractor', () => {
  //     const qty = wrapper.find({
  //         ref: 'quantity',
  //     })
  //
  //     expect(qty.attributes().disabled).toBe('disabled');
  // });
  //
  // it('The price input should be disabled - contractor', () => {
  //     const qty = wrapper.find({
  //         ref: 'price',
  //     })
  //
  //     expect(qty.attributes().disabled).toBe('disabled');
  // });
  //
  // it('Should render the text Change Task Location', () => {
  //     expect(wrapper.html()).toContain('Change Task Location');
  // });
  //
  // it('Should render the text Task Start Date - contractor', () => {
  //     expect(wrapper.html()).toContain('Task Start Date');
  // });
  //
  // it('Should render 2 task-images component, one for each task', () => {
  //     const taskImages = wrapper.findAll('task-images-stub');
  //     expect(taskImages.length).toBe(2);
  // });
  //
  // it('Messages should not be disabled', () => {
  //     expect(wrapper.vm.disableMessages).toBe(false);
  // });
  //
  // it('Should not render the subs panel', () => {
  //     expect(wrapper.vm.showSubsPanel).toBe(false);
  // });
  //
  // it('Should render footer slot', () => {
  //     expect(false).toBe(true);
  // });

})