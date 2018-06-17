import {mount} from 'vue-test-utils'
import expect from 'expect'
import axios from 'axios'
import Spark from '../../spark/resources/assets/js/spark-bootstrap'
import Jobs from '../../resources/assets/js/pages/Jobs.vue'

describe('Jobs', () => {
  const wrapper = mount (Jobs, {
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