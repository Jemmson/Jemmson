/**
 * Created by shawnpike on 3/2/17.
 */
import * as support from './utilities'

export const loadStore = (state, job) => {
  // support.loadJobStore(state, job)
  job = JSON.parse(job)
  setId(state, job.id)
  setCustomerId(state, job.customer_id)
  setContractorId(state, job.contractor_id)
  setAddressLine1(state, job.address_line_1)
  setAddressLine2(state, job.address_line_2)
  setCity(state, job.city)
  setState(state, job.state)
  setZip(state, job.zip)
  setCompletedBidDate(state, job.completed_bid_date)
  setBidPrice(state, job.price)
  setAgreedStartDate(state, job.agreed_start_date)
  setAgreedEndDate(state, job.agreed_end_date)
  setJobName(state, job.job_name)
  setCreatedAt(state, job.created_at)
  setUpdatedAt(state, job.updated_at)
}

export const setId = (state, id) => {
  state.id = id
}

export const setCustomerId = (state, id) => {
  state.customer_id = id
}

export const setContractorId = (state, id) => {
  state.contractor_id = id
}

export const setAddressLine1 = (state, address) => {
  state.address_line_1 = address
}

export const setAddressLine2 = (state, address) => {
  state.address_line_2 = address
}

export const setCity = (state, city) => {
  state.city = city
}

export const setState = (state, st) => {
  state.state = st
}

export const setZip = (state, zip) => {
  state.zip = zip
}

export const setCompletedBidDate = (state, date) => {
  state.completed_bid_date = date
}

export const setBidPrice = (state, price) => {
  state.bid_price = price
}

export const setAgreedStartDate = (state, date) => {
  state.agreed_start_date = date
}

export const setAgreedEndDate = (state, date) => {
  state.agreed_end_date = date
}

export const setJobName = (state, name) => {
  state.job_name = name
}

export const setCreatedAt = (state, date) => {
  state.created_at = date
}

export const setUpdatedAt = (state, date) => {
  state.updated_at = date
}

export const setDate = (state, type) => {
  // debugger
  type.date = type.date + " 00:00:00"
  if (type.type === 'agreed_start_date') {
    this.setAgreedStartDate(state, type.date)
  } else if (type.type === 'agreed_end_date') {
    this.setAgreedEndDate(state, type.date)
  }
}
