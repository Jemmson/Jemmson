/**
 * Created by shawnpike on 3/2/17.
 */

export const getCustomerName = state => {
  return state.customer.name;
}

export const getMobileValidResponse = state => {
  return state.mobile.response;
}

export const getQuickBooksState = state => {
  return state.features.quickbooks;
}

export const getRegisterInfo = state => {
  return state.registerInfo;
}

export const getLicenses = state => {
  return state.registerInfo.licenses;
}
