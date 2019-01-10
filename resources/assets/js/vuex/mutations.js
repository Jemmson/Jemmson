/**
 * Created by shawnpike on 3/2/17.
 */

export const setCustomerName = (state, payload) => {
  state.customer.name = payload.name
}

export const setMobileResponse = (state, payload) => {
  // debugger;
  state.mobile.response = payload;
}

export const setTheMobileResponse = (state, payload) => {
  // debugger;
  state.mobile.response = payload;
}

export const loadFeatures = (state, payload) => {

  for (let i = 0; i < payload.length; i++) {
    if(payload[i].name === 'quickbooks'){
      state.features.quickbooks = payload[i].on;
    }
  }

}