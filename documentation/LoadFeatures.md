# loadfeatures

###Purpose
Meant to load state when the page is being refreshed 
and also turn on features that are in developmemnt but
have not been released to the public yet

loads on the app.js file when ever the page is being 
refreshed

ar app = new Vue({
  mixins: [require('spark')],
  router,
  store,
  data: {
    user: window.User
  },
  mounted() {

    let location = {
      hash: window.location.hash,
      host: window.location.host,
      hostname: window.location.hostname,
      href: window.location.href,
      origin: window.location.origin,
      pathname: window.location.pathname,
      port: window.location.port,
      protocol: window.location.protocol
    }

    axios.post('/loadFeatures', {
      hello: 'world',
      location: location
    }).then((response) => {

      if (response.data.redirect) {
        if (response.data.redirect !== window.location.pathname) {
          window.location = response.data.redirect
        }
      } else {
        if (response.data.state[0]) {
          this.$store.commit('loadFeatures', response.data.state[0])
        }
        if (this.$store.state.user.user === '') {
          this.$store.commit('setUser', response.data.state[1])
        }
      }

    }).catch(function(error) {
      console.log(JSON.stringify(error))
    })
  }
})

it is loaded on the view instance as the view instance is being created

### Server
It is loaded on the server at LoadController load method.

If the User is logged in then we should be returning the User. If the User is not 
logged in then you get rerouted to the non logged in page.

if the user is logged in then the method should return the specific user