/**
 * Created by shawnpike on 3/2/17.
 */

export default {

  setCustomerName (state, payload) {
    state.customer.name = payload.name
  },

  setMobileResponse (state, payload) {
    // debugger;
    state.mobile.response = payload;
  },

  setTheMobileResponse (state, payload) {
    // debugger;
    state.mobile.response = payload;
  },

  loadFeatures (state, payload) {

    for (let i = 0; i < payload.length; i++) {
      if (payload[i].name === 'quickbooks') {
        state.features.quickbooks = payload[i].on;
      }
    }
  },

  setCurrentPage (state, payload) {
    state.page = payload;
  }
}