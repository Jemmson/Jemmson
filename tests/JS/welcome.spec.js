import {mount} from 'vue-test-utils'
import expect from 'expect'
import Welcome from '../../resources/assets/js/pages/PublicHome'

describe ('Welcome', () => {
  let wrapper;

  beforeEach (() => {
    wrapper = mount(Welcome);
  })

  it('Page contains button for Logging into Website', () => {
    expect(wrapper.html()).toContain(`LOGIN`)
  })

  it ('Page contains button to register', () => {
      expect(wrapper.html()).toContain(`REGISTER`)
  });

  it ('Page contains title subcontractor individual tasks', () => {
      expect(wrapper.html()).toContain(`Subcontract Individual Tasks`)
  });

  it ('Page contains initiate bids/invoices', () => {
      expect(wrapper.html()).toContain(`Initiate Bids/Invoices`)
  });

  it ('Page contains Bid on Tasks', () => {
      expect(wrapper.html()).toContain(`Bid on Tasks`)
  });

  it ('Page contains Powered By:', () => {
      expect(wrapper.html()).toContain(`Powered By:`)
  });

  // it ('Clicking Login button takes me to the login page', () => {
  //     wrapper.find('button.login').trigger('click')
      // wrapper.pause(3000);
      // expect(wrapper.html()).toContain(`E-Mail Address/Phone`)
  // });

})