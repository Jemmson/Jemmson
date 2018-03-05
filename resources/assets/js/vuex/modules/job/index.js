import * as actions from './actions'
import * as getters from './getters'
import * as mutations from './mutations'

export default {
  namespaced: true,
  state: {
    id: '',
    customer_id: '',
    contractor_id: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    zip: '',
    completed_bid_date: '',
    bid_price: '',
    agreed_start_date: '',
    agreed_end_date: '',
    actual_end_date: '',
    job_name: '',
    created_at: '',
    updated_at: '',
  },
  getters,
  mutations,
  actions
}
