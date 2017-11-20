/**
 * Created by shawnpike on 3/2/17.
 */

export const getId = state => {
  return state.id
}

export const getCustomerId = state => {
  return state.customer_id
}

export const getContractorId = state => {
  return state.contractor_id
}

export const getAddressLine1 = state => {
  return state.address_line_1
}

export const getAddressLine2 = state => {
  return state.address_line_2
}

export const getCity = state => {
  return state.city
}

export const getState = state => {
  return state.state
}

export const getZip = state => {
  return state.zip
}

export const geCompletedBidDate = state => {
  return state.completed_bid_date
}

export const getBidPrice = state => {
  return state.bid_price
}

export const getAgreedStartDate = state => {
  return state.agreed_start_date
}

export const getAgreedEndDate = state => {
  return state.agreed_end_date
}

export const getJobName = state => {
  return state.job_name
}

export const getCreatedAt = state => {
  return state.created_at
}

export const getUpdatedAt = state => {
  return state.updated_at
}
