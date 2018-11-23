if (Spark.state.user !== null) {
  window.Echo.private(`user.job.${Spark.state.user.id}`)
    .listen('BidInitiated', (e) => {
      console.log(e);
    });
  window.Echo.private(`user.job.${Spark.state.user.id}`)
    .listen('BidInitiated', (e) => {
      console.log(e);
    });

  /**
   * Capture all notification events
   */
  window.Echo.private('App.User.' + Spark.state.user.id)
    .notification((notification) => {
      console.log(notification);
      switch (notification.type) {
        case "App\\Notifications\\BidInitiated":
          console.log('bid initiated');
          Vue.toasted.info('A Contractor Initiated A Bid With You!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifyCustomerThatBidIsFinished":
          Vue.toasted.info('A Contractor Submitted A Bid!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifyJobHasBeenApproved":
          Vue.toasted.info('A Customer Accepted One of Your Bids!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskFinished":
          Vue.toasted.info('A Task Has Been Finished!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskApproved":
          Vue.toasted.info('A Task Has Been Approved!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifyJobHasBeenApproved":
          console.log('bid initiated');
          Vue.toasted.info('A Job Has Been Approved!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifyContractorOfSubBid":
          Vue.toasted.info('A Sub Has Submitted A Bid!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\JobBidDeclined":
          Vue.toasted.info('A Bid Has Been Declined!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\CustomerUnableToSendPaymentWithStripe":
          Vue.toasted.info('A Customer Failed To Pay You Through Stripe!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\CustomerPaidForTask":
          Vue.toasted.info('A Customer Paid For A Task!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifySubOfAcceptedBid":
          Vue.toasted.info('One of Your Bids Has Been Accepted!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifySubOfTaskToBid":
          Vue.toasted.info('You Were Sent A Task!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskWasNotApproved":
          Vue.toasted.info('A Task Was Not Approved!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskWasNotApproved":
          Vue.toasted.info('A Task Was Approved!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskReopened":
          Vue.toasted.info('A Task Was Reopened!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskDeleted":
          Vue.toasted.info('A Task Was Deleted!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\JobCanceled":
          Vue.toasted.info('A Job Was Canceled!');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\UploadedTaskImage":
          Vue.toasted.info('An Image Was Uploaded.');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\TaskImageDeleted":
          Vue.toasted.info('An Image Was Deleted.');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifyCustomerOfUpdatedMessage":
          console.log(Spark.state.user.id);
          Vue.toasted.info('Your Contractor Has Sent an Updated Message.');
          Bus.$emit('bidUpdated');
          break;
        case "App\\Notifications\\NotifySubOfUpdatedMessage":
          console.log(Spark.state.user.id);
          Vue.toasted.info('sdlsdlkdslksdlksdlksdlksdlk');
          Bus.$emit('bidUpdated');
          break;
        default:
          break;
      }
    });
}
