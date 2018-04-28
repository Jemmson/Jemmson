import {mount} from 'vue-test-utils'
import expect from 'expect'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'

describe ('InitiateBid', () => {
  let wrapper;

  beforeEach (() => {
    wrapper = mount(InitiateBid);
  })

  // it('show an error if the page is submitted without the phone field', () => {
  //   expect(wrapper.html()).toContain(`LOGIN`)
  // })

  // it ('Page contains button to register', () => {
  //   expect(wrapper.html()).toContain(`REGISTER`)
  // });

  it ('should have a button called submit', () => {
      expect(wrapper.html()).toContain(`Please Initiate a Bid With A Customer`)
    // const phone = wrapper.findAll('phone')
    // phone.setData({ foo: 'bar' })
    // expect(phone.vm.foo).toBe('bar')
    // wrapper.find('#submit').trigger('click')
  });

})