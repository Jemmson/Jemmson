import {mount} from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon';
import CheckAccountingApp from '../../resources/assets/js/pages/CheckAccountingApp'

console.log('CheckAccountingApp.spec')

describe ('CheckAccountingApp', () => {
  let wrapper;
  const getAuthURL = sinon.spy();

  wrapper = mount(CheckAccountingApp, {
    methods: {
      getAuthURL
    },
    data: {
      isContractor: false,
      quickbooks: {
        auth_url: ''
      }
    }
  });

  // beforeEach (() => {
  // });

  it.only('should show Customer button', function() {
    expect(wrapper.find({ref: 'customer'}).text()).toBe('Customer')
  });

  it.only('should should not show accounting buttons if isContractor is false', function() {
    wrapper.setData({
      isContractor: false
    })

    expect(wrapper.find({ref: 'quickbooks'}).isVisible()).toBe(false);

  })

  it.only('should show quickbooks button after contractor button is clicked', function() {

    wrapper.setData({
      isContractor: false
    })

    let contractorButton = wrapper.find({
      ref: 'contractor'
    });

    // let qb = wrapper.find({ref: 'quickbooks'});

    // expect(qb.isVisible()).toBe(false);

    contractorButton.trigger('click');
    //
    let qbs = wrapper.find({ref: 'xero'});
    //
    console.log(qbs);
    //
    expect(qbs.isVisible()).toBe(true);

  })

  it.only('should trigger the auth URL on click', function() {

    let btn = wrapper.find({
      ref: 'contractor'
    })

    btn.trigger('click');

    expect(getAuthURL.calledOnce).toBe(true);

  })

  it('should only get auth url once. each successive clicks will only show and hide the contractor buttons', function() {
    //manually verified

  })

  it('should check whether the quickbooks feature is turned on', function() {
    //manually verified

  })

  it('should be routed to the registration page if the quickbooks feature is turned off', function() {
    // manually verified

  })

  it('should should show and hide accounting buttons when the contractor button is clicked', function() {
    // manually verified

  })

  it('should always show accounting page for registration if if quickbooks feature is turned on. ' +
    'refreshing the page should not show registration page if quickbooks feature is turned on', function() {

    // manually verified

  })

  it('should route to the register page if "dont use" is clicked', function() {
    //visually  verified
  })

})
