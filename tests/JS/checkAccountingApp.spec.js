import {mount} from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon';
import CheckAccountingApp from '../../resources/assets/js/pages/CheckAccountingApp'

require('./bootstrap');

// console.log('CheckAccountingApp.spec')

describe ('CheckAccountingApp', () => {
  let wrapper;

  wrapper = mount(CheckAccountingApp, {
    data () {
      return {
        isContractor: false,
        quickbooks: {
          auth_url: ''
        }
      }
    }
  });

  // beforeEach (() => {
  // });

  it('should show Customer button', function() {
    expect(wrapper.find({ref: 'customer'}).text()).toBe('Customer')
  });

  it('should should not show accounting buttons if isContractor is false', function() {
    wrapper.setData({
      isContractor: false
    })

    expect(wrapper.find({ref: 'quickbooks'}).isVisible()).toBe(false);

  })

  it('should call the getauthurl method when the contractor button is clicked', function() {

    const getAuthURLstub = sinon.stub()

    let contractorButton = wrapper.find({
      ref: 'contractor'
    });

    contractorButton.trigger('click');

    wrapper.setMethods({
      getAuthURL: getAuthURLstub
    })

    expect(getAuthURLstub.called).toBe(true)


  })

  it('should show quickbooks button after contractor button is clicked', function() {

    wrapper.setData({
      isContractor: false
    })

    let contractorButton = wrapper.find({
      ref: 'contractor'
    });

    contractorButton.trigger('click');

    let qbs = wrapper.find({ref: 'quickbooks'});

    expect(qbs.isVisible()).toBe(true);

  })

  it.skip('should only get auth url once. each successive clicks will only show and hide the contractor buttons', function() {
    //manually verified

  })

  it.skip('should check whether the quickbooks feature is turned on', function() {
    //manually verified

  })

  it.skip('should be routed to the registration page if the quickbooks feature is turned off', function() {
    // manually verified

  })

  it.skip('should should show and hide accounting buttons when the contractor button is clicked', function() {
    // manually verified

  })

  it.skip('should always show accounting page for registration if if quickbooks feature is turned on. ' +
    'refreshing the page should not show registration page if quickbooks feature is turned on', function() {

    // manually verified

  })

  it.skip('should route to the register page if "dont use" is clicked', function() {
    //visually  verified
  })

})
