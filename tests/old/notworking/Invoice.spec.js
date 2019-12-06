import {
  mount,
  shallowMount,
  createLocalVue
}  from '@vue/test-utils';
import User from '../../resources/assets/js/classes/User';
import sinon from 'sinon';

const $route = {
  path: '/#/invoice/1',
  query: {
    success: 1,
    error: 0
  },
  params: {
    id: 1
  }
}

require('./bootstrap')

import Invoice from '../../resources/assets/js/pages/Invoice.vue'

describe('Invoice', () => {
  const wrapper = shallowMount(Invoice, {
    mocks: {
      $route
    },
    stubs: [
      'card',
    ],
    propsData: {
      user: User,
    }
  });

  it('Should render itself', () => {
    expect(wrapper.isEmpty()).toBe(false);
  });
  
  it('Should not render invoice details', () => {
    const invoiceDetails = wrapper.find({
      ref: 'invoice-details'
    })
    expect(invoiceDetails.exists()).toBe(false);
  });

  it('Should render invoice details', () => {
    wrapper.setData({
      invoice: {
        location: null,
        job_tasks: [{
          task: {
            name: "Clean Pool"
          }
        }],
      }
    });
    const invoiceDetails = wrapper.find({
      ref: 'invoice-details'
    })
    expect(invoiceDetails.exists()).toBe(true);
  });

  it('Should render all contractor sections - contractor', () => {
    User.setUser({
      usertype: 'contractor'
    });
    const wrapper = shallowMount(Invoice, {
      mocks: {
        $route
      },
      stubs: [
        'card',
      ],
      propsData: {
        user: User.user,
      }
    });
    expect(wrapper.vm.isContractor).toBe(true);
  });

  it('Should not render any contractor sections - customer', () => {
    User.setUser({
      usertype: 'customer'
    });
    const wrapper = shallowMount(Invoice, {
          mocks: {
            $route
          },
          stubs: [
            'card',
          ],
          propsData: {
            user: User.user,
          }});
    expect(wrapper.vm.isContractor).toBe(false);
  });

});