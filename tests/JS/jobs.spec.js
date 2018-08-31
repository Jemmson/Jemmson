import {
    mount,
    createLocalVue
} from 'vue-test-utils'
import VuePaginate from 'vue-paginate'

require('./bootstrap');

const localVue = createLocalVue()
localVue.use(VuePaginate)


import Jobs from '../../resources/assets/js/pages/Jobs.vue'

const $on = {
    bidUpdated: () => {
        console.log('get bids');
        
    }
}

describe('Jobs', () => {
  const wrapper = mount (Jobs, {
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
          }
        ],
        showBid: false,
        bidIndex: 0,
        searchTerm: '',
        paginate: ['sBids']
      }
    }
  });

  it ('should ', function () {

  });
})