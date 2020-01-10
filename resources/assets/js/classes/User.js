import { router } from '../router.js'
import Language from './Language'

export default class User {

  async chargeCustomer() {
    // charge customer
    const {
      charge,
      error
    } = await axios.post('/stripe/customer/charge', {
      amount: 1
    })

    if (error) {
      Vue.toasted.error(error.message)
    } else {
      console.log(charge)
      Vue.toasted.success('Payment Sent!')
    }
  }

  emitChange(emit) {
    switch (emit) {
      case 'bidUpdated':
        Bus.$emit('bidUpdated')
        break
      case 'taskUpdated':
        Bus.$emit('taskUpdated')
        break
    }
  }

  fillInAddress() {
    // Get the place details from the autocomplete object.
    let place = window.autocomplete.getPlace()
    let componentForm = {
      street_number: 'short_name',
      route: 'long_name',
      locality: 'long_name',
      administrative_area_level_1: 'short_name',
      country: 'long_name',
      postal_code: 'short_name'
    }
    let location = {}

    if (place !== undefined) {
      // Get each component of the address from the place details
      // and fill the corresponding field on the form.
      for (let i = 0; i < place.address_components.length; i++) {
        let addressType = place.address_components[i].types[0]
        if (componentForm[addressType]) {
          let val = place.address_components[i][componentForm[addressType]]
          location[addressType] = val
          if (addressType == 'route') {
            location[addressType] = place.address_components[i - 1][componentForm['street_number']] + ' ' + place.address_components[i][componentForm['route']]
          }
        }
      }
      Bus.$emit('updateFormLocation', location)
    }
  }

  findTaskBid(id, bids) {
    return bids.filter(function(bid) {
      return id === bid.id
    })
  }

  getAllPayableTasks(jobTasks) {
    if (jobTasks !== undefined) {
      return jobTasks.filter((jobTask) => {
        return jobTask.status === 'bid_task.approved_by_general' || jobTask.status === 'bid_task.finished_by_general'
      })
    }
    return []
  }

  getAllUnpaidTasks(jobTasks) {
    if (jobTasks !== undefined) {
      return jobTasks.filter((jobTask) => {
        return jobTask.status !== 'bid_task.approved_by_general' && jobTask.status !== 'bid_task.finished_by_general'
      })
    }
    return []
  }

  getBidIndex(id, bid) {
    for (let index = 0; index < bid.length; index++) {
      if (bid[index].id == id) {
        return index
      }
    }
    return null
  }

  getId() {
    return this.user.id
  }

  getParameterByName(name, url) {
    if (!url) url = window.location.href
    name = name.replace(/[\[\]]/g, '\\$&')
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url)
    if (!results) return null
    if (!results[2]) return ''
    return decodeURIComponent(results[2].replace(/\+/g, ' '))
  }

  hasStripeId() {
    return this.user.stripe_id !== undefined && this.user.stripe_id !== null
  }

  hello() {
    return 'world'
  }

  initAutocomplete(id) {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    window.autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */
      (document.getElementById(id)), {
        types: ['geocode']
      })

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.

    window.autocomplete.addListener('place_changed', this.fillInAddress)

    // make the dropdown list of addresses inside modals appear ontop of modals
    setTimeout(function() {
      let elem = document.getElementsByClassName('pac-container')
      for (let i = 0; i < elem.length; i++) {
        elem[i].style.zIndex = '10000000000'
      }
    }, 400)
  }

  // is the task assigned to the currently logged in user
  isAssignedToMe(jobTask, userId) {
    return jobTask ? userId === jobTask.contractor_id : null
  }

  isContractor() {
    return this.user.usertype === 'contractor'
  }

  isCustomer() {
    return this.user.usertype === 'customer'
  }

  /**
   *
   * @param {JobTask} bid //
   */
  isGeneral(bid, userId) {
    if (bid !== null) {
      return bid.contractor_id === userId
    }

    return false
  }

  isSignedUpWithStripe() {
    return this.user.stripe_id !== undefined && this.user.stripe_id !== null
  }

  isSub(bid, usertype, userId = null) {

    if (bid.task) {
      return bid !== null &&
        (usertype === 'contractor' &&
          bid.task.contractor_id !== userId)
    } else {
      return bid !== null &&
        (usertype === 'contractor' &&
          bid.contractor_id !== userId)
    }
  }

  /**
   * Check all job tasks to see if any have
   * stripe as the payment option active
   *
   * @param {Job} bid
   */
  jobNeedsStripe(bid) {
    let stripeNeeded = false
    for (let jobTask of bid.job_tasks) {
      if (jobTask.stripe) {
        stripeNeeded = true
        break
      }
    }
    if (stripeNeeded && !this.stripeExpressConnected()) {
      Bus.$emit('needsStripe')
      return true
    }
    return false
  }

  /**
   * Has this user not connected withs tripe yet?
   */
  needsStripe() {
    if (!this.stripeExpressConnected()) {
      console.log('No Stripe Express')
      Bus.$emit('needsStripe')
      return true
    }
    return false
  }

  payWithStripe() {
    return true
  }

  /**
   *
   * @param {JobTask or BidContractorJobTask.job_task} bid
   */
  recievePaymentsWithStripe(bid) {
    if (bid.job_task !== undefined) {
      return bid.job_task.stripe
    } else {
      return bid.stripe
    }
  }

  async registerContractor(form, softwareType, updateAccountingCompanyInfoAPI = false) {
    form.phone_number = Format.numbersOnly(form.phone_number)

    // this is always false becuase the update company feature is not yet available
    updateAccountingCompanyInfoAPI = false

    try {
      const data = await axios.post('/register/contractor', {
        form: form,
        softwareType: softwareType,
        updateAccountingCompanyInfoAPI: updateAccountingCompanyInfoAPI
      })
      Vue.toasted.success('info updated')
      // debugger;
      console.log(JSON.stringify(data.data))
      router.push(data.data)
      Bus.$emit('updateUser')
      // location.href = data
    } catch (error) {
      let data = error.response.data
      let message = ''

      if (data.errors['form.email'] !== undefined && data.errors['form.email'] !== null) {
        message += data.errors['form.email'][0]
      }

      form.errors.errors = error.errors
      Vue.toasted.error(message)
    }
  }

  async saveCustomer(token) {
    if (!token) {
      return false
    }
    // create stripe customer with token
    const {
      customer,
      error
    } = await axios.post('/stripe/customer', token)

    if (error) {
      Vue.toasted.error(error.message)
      return false
    } else {
      this.stripe_id = customer.id
      return true
    }
  }

  setUser(user) {
    this.user = user
  }

  status(status, bid, user) {

    if (!user) {
      user = Spark.state.user
    }

    if (status === null) {
      status = 'bid_task.initiated'
    }

    status = Language.lang()[status]

    if (status === undefined) {
      return ''
    }

    if (user.usertype === 'contractor') {
      if (
        this.isSub(bid, user.usertype, user.id) !== undefined &&
        this.isSub(bid, user.usertype, user.id)
      ) {
        return status.sub
      }

      if (this.isGeneral(bid, user.id)) {return status.general}
    }

    return status.customer
  }

  /**
   * User has a stripe express account connected
   */
  stripeExpressConnected() {
    if (!this.user.isContractor) {
      return false
    }

    // if stripe_express is anything other that undefined or null then return true
    return this.user.contractor.stripe_express !== undefined && this.user.contractor.stripe_express !== null

  }

  stripePaymentRequested(jobTasks) {
    for (let jobTask of jobTasks) {
      if (jobTask.stripe) {
        return true
      }
    }
    return false
  }

  async submitFeedback(comment, disabled) {
    disabled.submit = true
    let page_url = window.location.href
    let user_id = this.user.id
    try {
      await
        axios.post('/api/feedback', {
          user_id: user_id,
          page_url: page_url,
          comment: comment
        })
      Vue.toasted.success('Feedback Submitted, Thank You!')
      disabled.submit = false
      $('#feedback-modal').modal('hide')
    } catch (error) {
      console.log(error)
      Vue.toasted.error(error.message)
      disabled.submit = false
    }
  }

  // /stripe functions
  // /NOTICE: not used just incase we need them later as functions need to fix the error

  async submitFurtherInfo(form, disabled, updateAccountingCompanyInfoAPI = false) {
    disabled.submit = true
    form.phone_number = Format.numbersOnly(form.phone_number)
    try {
      const data = await Spark.post('/home', form)
      Vue.toasted.success('info updated')
      Bus.$emit('updateUser')
      disabled.submit = false
      location.href = data
    } catch (error) {
      console.log(error)
      form.errors.errors = error.errors
      Vue.toasted.error(error.message)
      disabled.submit = false
    }
  }

  unformatNumber(number) {
    let unformattedNumber = ''
    for (let i = 0; i < number.length; i++) {
      if (!isNaN(parseInt(number[i]))) {
        unformattedNumber = unformattedNumber + number[i]
      }
    }
    return unformattedNumber
  }

  async updateTaskLocation(form, disabled) {
    disabled.update = true
    try {
      await axios.post('/api/location', form)
      Bus.$emit('bidUpdated')
      Vue.toasted.success('Location Updated')
      disabled.update = false
      $('#update-task-location-modal').modal('hide')
    } catch (error) {
      console.log(error)
      disabled.update = false
      Vue.toasted.error(error.message)
    }
  }

  async uploadTaskImage(form, disabled) {
    disabled.uploadTaskImageBtn = true
    try {
      const {data} = await axios.post('/task/image', form)
      console.log(data)
      Bus.$emit('bidUpdated')
      disabled.uploadTaskImageBtn = false
      return false;
    } catch (error) {
      Vue.toasted.error(error.message)
      disabled.uploadTaskImageBtn = false
      return false;
    }
  }

  // async validateMobileNumber (number) {
  //   let unformattedNumber = this.unformatNumber (number);
  //   try {
  //     const data = await axios.post ('/api/user/validatePhoneNumber', {
  //       num: unformattedNumber,
  //     });
  //     if (data.data[0] === 'success') {
  //       Vue.toasted.success (Language.lang ().phone.success + ". This is a " + data.data[1] + " number");
  //       return 'mobile';
  //     } else if (data.data[0] === 'failure') {
  //       Vue.toasted.error (Language.lang ().phone.failure + ". This is a " + data.data[1] + " number");
  //       return 'landline';
  //     } else {
  //       Vue.toasted.info (Language.lang ().phone.error + " " + data.data[1]);
  //       return 'error';
  //     }
  //   } catch (error) {
  //     Vue.toasted.error ('Error: ' + error.message);
  //   }
  // }

  constructor(user) {
    this.user = user
  }
}

export var __useDefault = true
