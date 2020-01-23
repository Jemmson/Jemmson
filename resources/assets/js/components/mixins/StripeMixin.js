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

    formatPathVariable(path) {
      const pathArray = path.split('/')
      let pathString = ''
      if (pathArray.length > 1) {
        for (let i = 0; i < pathArray.length; i++) {
          pathString = pathString + '_' + pathArray[i]
        }
      }
      return pathString
    }
  }
}