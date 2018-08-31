import {
    mount,
    shallowMount,
    createLocalVue
} from 'vue-test-utils'
import VuePaginate from 'vue-paginate'

require('./bootstrap');

const localVue = createLocalVue();
localVue.use(VuePaginate);

import Jobs from '../../resources/assets/js/pages/Jobs.vue';

const $on = {
    bidUpdated: () => {
        console.log('get bids');
    }
}

describe('Jobs', () => {
  const wrapper = mount(Jobs, {
    localVue,
    mocks: {
        $on
    },
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
        bids: [],
        sBids: [
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
          }
        ],
        showBid: false,
        bidIndex: 0,
        searchTerm: '',
        paginate: ['sBids']
      }
    }
  });

  it('Should contain the name Fix Sink', () => {
    expect(wrapper.text()).toContain('Fix Sink');
  });

  it('Should have rendered 6 job items', () => {
      expect(wrapper.findAll('section')).toHaveLength(6);
  });
  
  it('Should have 2 paginate links', () => {
      expect(wrapper.html()).toContain('<a>1</a>');
      expect(wrapper.html()).toContain('<a>2</a>');
  });

  it('Should not have 3th paginate link', () => {
  });

});