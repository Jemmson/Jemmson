window.Echo.private(`user.job.${Spark.state.user.id}`)
  .listen('BidInitiated', (e) => {
    console.log(e);
  });

window.Echo.private('App.User.' + Spark.state.user.id)
  .notification((notification) => {
    console.log(notification);
    switch (notification.type) {
      case "App\\Notifications\\BidInitiated":
        console.log('bid initiated');
        Vue.toasted.info('A Contractor Initiated A Bid With You!');
        Bus.$emit('bidUpdated');
        break;
      default:
        break;
    }
  });