import {mount} from 'vue-test-utils'
import expect from 'expect'
import Job from '../../resources/assets/js/components/job/Job.vue'

describe ('Job', () => {
  // let wrapper;
  //
  // beforeEach (() => {
  //   wrapper = mount (Job);
  // })
  //
  // it ('Contractor recieves alert that the customer has not registered to the site yet', () => {
  //   wrapper.setProps({customer: '[]'})
  //   // expect(wrapper.find('.customerRegisteredAlert').text()).toContain('Customer Has Not Registered Yet')
  //   expect (wrapper.find ('.customerRegisteredAlert').hasStyle ('display', 'none')).toBe(false)
  // });
  //
  // it ('if the Customer already exists then I should not see "Customer Has Not Registered Yet"', () => {
  //   wrapper.setProps({customer: '[{"id":1,"user_id":4,"email_method_of_contact":"on","phone_method_of_contact":"on","sms_method_of_contact":"on","address_line_1":"1234 main st","address_line_2":null,"city":"mesa","state":"az","zip":"85283","notes":"sjkdfha","phone_number":"1234567","created_at":"2017-11-27 04:04:55","updated_at":"2017-11-27 04:04:55"}]'})
  //   expect (wrapper.find ('.customerRegisteredAlert').hasStyle ('display', 'none')).toBe(true)
  // });

  it.only ('the customer name shows up on the screen', () => {
    let wrapper = mount (Job);
    wrapper.setProps({customeruserdata: '[{"id":5,"name":"asd","email":"asd@salk.com","photo_url":"https:\\/\\/www.gravatar.com\\/avatar\\/3e3930655e847a822a4a3ed0297ab040.jpg?s=200&d=mm","uses_two_factor_auth":false,"phone":"4807034902","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"password_updated":0,"created_at":"2017-12-02 10:10:08","updated_at":"2017-12-02 10:10:08","tax_rate":0}]'})
    expect (wrapper.find ('.customerName')).text().not.toBe('')
  });

  it ('if I am a contractor and the bid is not complete then the customer will not see the bid details ' +
    'until it is submitted to the customer from the contractor', () => {
    expect ()
  });

  it ('I am a customer and the bid is not complete I will see only "Bid In Progress" message', () => {
    expect ()
  });

  it ('I am a contractor and I can select a bid good till date', () => {
    expect ()
  });

  it ('I am a contractor and I can specify job length instead of start date and end date', () => {
    expect ()
  });

  it ('There are three buttons for job completion Approve, Message, Cancel', () => {
    expect ()
  });

  it ('I am a contractor and I can view the message stream from a customer about the bid that is being completed', () => {
    expect ()
  });

})
