import {
  mount,
  shallowMount,
  createLocalVue
}
from '@vue/test-utils';
import VuePaginate from 'vue-paginate';
import sinon from 'sinon';

require('./bootstrap');

const localVue = createLocalVue();
localVue.use(VuePaginate);

import Invoices from '../../resources/assets/js/pages/Invoices.vue';

const $on = {
  bidUpdated: () => {
  }
}


describe('Invoices', () => {
  const search = sinon.stub();
  const wrapper = mount(Invoices, {
    localVue,
    stubs: [
      'search-bar',
      'card',
      'router-link'
    ],
    mocks: {
      $on,
      search
    },
    propsData: {
      user: {
        usertype: 'contractor',
        contractor: {
          company_name: 'KPS Pools'
        }
      }
    },
    data() {
      return {
        invoices: [
        {
          bid_price: 245,
          customer: {
            name: 'Laurel Ailie'
          },
          job_name: 'Clear up Green Pool',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'John Doe' //
          },
          job_name: 'Fix Pool',
          status: 'job.completed'
        }],
        sInvoices: [
        {
          bid_price: 245,
          customer: {
            name: 'Laurel Ailie'
          },
          job_name: 'Clear up Green Pool',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'Jane Doe'
          },
          job_name: 'Fix Sink',
          status: 'bid.sent'
        }, {
          bid_price: 245,
          customer: {
            name: 'John Doe' //
          },
          job_name: 'Fix Pool',
          status: 'job.completed'
        }, {
          bid_price: 245,
          customer: {
            name: 'John Doe' //
          },
          job_name: 'Fix Pool',
          status: 'job.completed'
        }
        ],
        searchTerm: '',
        paginate: ['sInvoices']
      }
    }
  });

  it('Should contain the name Clear up Green Pool', () => {
    expect(wrapper.text()).toContain('Clear up Green Pool');
  });

  it('Should have rendered 8 invoices', () => {
    expect(wrapper.findAll('card-stub')).toHaveLength(8);
  });

  it('Should have 2 paginate links', () => {
    expect(wrapper.html()).toContain('<a>1</a>');
    expect(wrapper.html()).toContain('<a>2</a>');
  });

  it('Clicking the next arrow should show the next list of invoices', () => {
    wrapper.find('.right-arrow > a').trigger('click');
    expect(wrapper.findAll('card-stub')).toHaveLength(1);
  });

  it('Clicking the previous arrow should show the previous list of invoices', () => {
    wrapper.find('.left-arrow > a').trigger('click');
    expect(wrapper.findAll('card-stub')).toHaveLength(8);
  });

  it('Search bar should update with input', () => {
    const input = wrapper.find('input');
    input.setValue('Clear up Green Pool');
    // input.trigger('keyup');
    expect(wrapper.vm.searchTerm).toBe('Clear up Green Pool');
    // expect(wrapper.findAll('card-stub')).toHaveLength(1);
  });

});