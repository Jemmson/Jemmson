/**
 * Created by shawnpike on 3/2/17.
 */

export const loadJobStore = (state, job) => {
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
