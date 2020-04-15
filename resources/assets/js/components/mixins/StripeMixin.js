export default {
  methods: {
    // connectWithStripe() {
    //   let connectLink = "https://connect.stripe.com/express/oauth/authorize?client_id="
    //   + Spark.stripeClientId +"&state="+ this.$route.path
    //   + "&stripe_user[email]=" + Spark.state.user.email
    //   + "&stripe_user[country]=US"
    //   + "&stripe_user[phone_number]=" + Spark.state.user.phone;
    //
    //   window.location = connectLink;
    // }

    async connectWithStripe(path) {
      const formattedPath = this.formatPathVariable(path)
      const data = await axios.get('/getStripeOauthUrl/' + formattedPath)
      window.location = data.data
    },

    needsStripe() {
      if (this.theUser) {
        return this.theUser.customer_stripe_id !== null
      }
    },

    needsStripeForCreditCardPayments() {
      if (
          Spark.state.user
          && Spark.state.user.contractor
      ) {
        return Spark.state.user.contractor.stripe_express === null;
      }
    },

    formatPathVariable(path) {
      const pathArray = path.split('/')

      const pathArrayWithOutSpaces = [];

      for (let i = 0; i < pathArray.length; i++) {
        if (pathArray[i] !== '') {
          pathArrayWithOutSpaces.push(pathArray[i])
        }
      }

      let pathString = ''
      if (pathArrayWithOutSpaces.length > 1) {
        for (let i = 0; i < pathArrayWithOutSpaces.length; i++) {
          pathString = pathString + '_' + pathArrayWithOutSpaces[i]
        }
      } else {
        pathString = pathArrayWithOutSpaces[0]
      }
      return pathString
    }
  }
}