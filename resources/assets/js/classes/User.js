export default class User {
  async chargeCustomer () {
    // charge customer
    const {
      charge,
      error
    } = await axios.post ('/stripe/customer/charge', {
      amount: 1
    });

    if (error) {
      Vue.toasted.error (error.message);
    } else {
      console.log (charge);
      Vue.toasted.success ('Payment Sent!');
    }
  }

  // /NOTICE: need to fix the error handling since it doesn't work
  async createToken () {
    // create stripe token
    const {
      token,
      error
    } = await this.stripe.createToken (this.card);

    if (error) {
      // Inform the customer that there was an error
      const errorElement = document.getElementById ('card-errors');
      errorElement.textContent = error.message;
      return false;
    } else {
      return token;
    }
  }

  emitChange (emit) {
    switch (emit) {
      case 'bidUpdated':
        Bus.$emit ('bidUpdated');
        break;
      case 'taskUpdated':
        Bus.$emit ('taskUpdated');
        break;
    }
  }

  findTaskBid (id, bids) {
    return bids.filter (function (bid) {
      return id === bid.id;
    });
  }

  getBidIndex (id, bid) {
    for (let index = 0; index < bid.length; index++) {
      if (bid[index].id == id) {
        return index;
      }
    }
    return null;
  }

  getId () {
    return this.user.id;
  }

  getParameterByName (name, url) {
    if (!url) url = window.location.href;
    name = name.replace (/[\[\]]/g, '\\$&');
    var regex = new RegExp ('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec (url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent (results[2].replace (/\+/g, ' '));
  }

  hasStripeId () {
    return this.user.stripe_id !== null && this.user.stripe_id !== undefined;
  }

  // is the task assigned to the currently logged in user
  isAssignedToMe (jobTask) {
    return this.user.id === jobTask.contractor_id;
  }

  isContractor () {
    return this.user.usertype === 'contractor';
  }

  isCustomer () {
    return this.user.usertype === 'customer';
  }

  isGeneral (bid) {
    if (bid !== null)
      return bid.contractor_id === this.user.id;
  }

  isSignedUpWithStripe () {
    return this.user.stripe_id !== undefined && this.user.stripe_id !== null;
  }

  /**
   * Check all job tasks to see if any have
   * stripe as the payment option active
   * @param {Job} bid 
   */
  jobNeedsStripe(bid) {
    let stripeNeeded = false;
    for (const jobTask of bid.job_tasks) {
      if (jobTask.stripe && jobTask.contractor_id === this.user.id) {
        stripeNeeded = true;
      }
    }
    if (stripeNeeded && !this.stripeExpressConnected()) {
      Bus.$emit('needsStripe');
      return true;
    }
    return false;
  }

  /**
   * 
   * @param {JobTask or BidContractorJobTask} jobTask 
   */
  needsStripe (jobTask) {
    if (this.recievePaymentsWithStripe(jobTask)) {
      if (!this.stripeExpressConnected()) {
        console.log ('No Stripe Express');
        Bus.$emit ('needsStripe');
        return true;
      }
      return false;
    }
    return false;
  }

  payWithStripe () {
    return true;
  }

  /**
   * 
   * @param {JobTask or BidContractorJobTask} bid
   */
  recievePaymentsWithStripe (bid) {
    if (bid.job_task !== undefined) {
      return bid.job_task.stripe;
    } else {
      return bid.stripe;
    }
  }

  async saveCustomer (token) {
    if (!token) {
      return false;
    }
    // create stripe customer with token
    const {
      customer,
      error
    } = await axios.post ('/stripe/customer', token);

    if (error) {
      Vue.toasted.error (error.message);
      return false;
    } else {
      this.stripe_id = customer.id;
      return true;
    }
  }

  // /stripe functions
  // /NOTICE: not used just incase we need them later as functions need to fix the error

  status (status, bid) {
    status = Language.lang ()[status];
    if (status === undefined) {
      return '';
    }
    if (this.isContractor ()) {
      if (bid !== null && this.isGeneral (bid))
        return status.general;

      return status.sub;
    }

    return status.customer;
  }

  /**
   * User has a stripe express account connected
   */
  stripeExpressConnected() {
    if (!this.isContractor()) {
      return false;
    }

    // if stripe_express is anything other that undefined or null then return true
    return this.user.contractor.stripe_express !== undefined && this.user.contractor.stripe_express !== null;

  }

  async submitFeedback (comment, disabled) {
    disabled.submit = true;
    let page_url = window.location.href;
    let user_id = this.user.id;
    try {
      const data = await
        axios.post ('/api/feedback', {
          user_id: user_id,
          page_url: page_url,
          comment: comment,
        });
      Vue.toasted.success ('Feedback Submitted, Thank You!');
      disabled.submit = false;
      $ ('#feedback-modal').modal ('hide');
    } catch (error) {
      console.log (error);
      Vue.toasted.error (error.message);
      disabled.submit = false;
    }
  }

  async submitFurtherInfo (form, disabled) {
    disabled.submit = true;
    form.phone_number = Format.numbersOnly (form.phone_number);
    try {
      const data = await
        Spark.post ('/home', form);
      Vue.toasted.success ('info updated');
      Bus.$emit ('updateUser');
      disabled.submit = false;
      location.href = data;
    } catch (error) {
      console.log (error);
      form.errors.errors = error.errors;
      Vue.toasted.error (error.message);
      disabled.submit = false;
    }
  }

  constructor (user) {
    this.user = user;
  }
}