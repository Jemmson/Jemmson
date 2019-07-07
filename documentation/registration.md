#Registration / Authentication / Authorization

###Bugs
1. I am logged out and I try to access a page that requires a log in
    1. ####steps
       1. Log out -> http://localhost:9500/logout
       2. go to -> http://localhost:9500/#/home
       3. home page loads but I should be redirected to http://localhost:9500/
    

Different ways to register
- Customer
    1. From a passwordless link initiated by a contractor
    2. Register Directly through to the site itself

- Contractor
    1. Register Directly
        1. Through Quickbooks
            1. Component -> RegisterQuickBooks.vue
            2. Register button flow
                1. User.registerContractor
                1. register/contractor -> web.php
                2. RegisterController -> registerContractor
            3. Uses Bus.$on('updateUser')
            4. Vuex Store is being update with the user in that method -> spark.js
        2. Just with the site 
            
    2. Passwordless link from as a sub from a contractor
    

Maintaining state
1. Must maintain state upon a page refresh
2. Trying to handle the page refresh situation
    1. App.js
        1. axios.post('/loadFeatures', {
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
        2. This is meant to load the page with user state if the page is refreshed it goes to the
        Load Controller load method.
        3. This is in the mounted method because I am assuming this method is going to be loaded on
            each page refresh


Page Access
- Pages that dont require authorization
    1. if logged in can go to these pages but must maintain state
    2. if not logged in then can go to these pages but will not have state
- Pages that require authorization
    1. if logged in then a user can go to these pages but also must have state
    2. if not logged in then there is no state and the user is unable to go to these pages
- Page access can happen in two ways
    1. client side routing
        1. this must account for the authorization but through javascript
    2. on a page load
        1. this must account for the authorization but by the server

Logged In
    - Server
        1. Laravel uses sessions and cookies
    - Client
        2. Can check laravels sessions and cookies or check that there is state in vuex?

