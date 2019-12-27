/**
 * Created by shawnpike on 3/2/17.
 */

export default {

  setCustomerName(state, payload) {
    state.customer.name = payload.name
  },

  setMobileResponse(state, payload) {
    // debugger;
    state.mobile.response = payload
  },

  setTheMobileResponse(state, payload) {
    // debugger;
    state.mobile.response = payload
  },

  loadFeatures(state, payload) {

    for (let i = 0; i < payload.length; i++) {
      if (payload[i].name === 'quickbooks') {
        state.features.quickbooks = payload[i].on
      }
    }
  },

  setPhoneLoadingValue(state) {
    state.busy = false
  },

  setCurrentPage(state, payload) {
    state.page = payload
  },

  toggleBidsContractor(state, payload) {
    state.bidsContractorSectionPicked = payload
  },

  // setUser (state, payload) {
  //   state.user = payload;
  // }

  busy(state) {
    state.busy = !state.busy
  },

  clearMobileResponse(state) {
    state.mobile.response = []
  },

  setAuth(state, payload) {
    state.auth = payload
  }

}