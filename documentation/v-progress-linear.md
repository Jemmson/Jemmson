###Adding progress bar using vuex

1. need the thing being triggered to be disabled while the loading occurs
:disabled="loading"


2. need the progress bar to be put n the template in the right location
<v-progress-linear
        :active="loading"
        :indeterminate="loading"
        absolute
        bottom
        color="deep-purple accent-4"
></v-progress-linear>


3. need the loading state from vuex to be set retrievable
...mapState({
    loading: state => state.busy,
}),

4. need to initialize loading to false in the state

-Component
mounted: function() {
  this.$store.commit('setPhoneLoadingValue')
  this.user = Spark.state.user
}

Mutations
setPhoneLoadingValue(state){
    state.busy = false
  },
  
5. need triggering event in the dom element
@blur="validateMobileNumber($event.target.value)"


6. triggering event calls an action method
validateMobileNumber(phone) {
    if (this.unformatNumber(this.initiateBidForSubForm.phone) === 10) {
      **this.checkMobileNumber(phone)**
      this.checkValidData()
    } else {
      this.phoneFormatError = true
    }
  },
  
7. the action method starts by triggering the state to true which causes the progress bar to be viewable
and disables the triggering DOM element

``export const checkMobileNumber = ({commit}, phone) => {

  **commit('busy')**

.
.
.
``

8. once the ajax call finishes processing then the state is turned back to false which hides the progress element
and enables the triggering event

let unformattedNumber = unformatNumber(phone)

  axios.post('/api/user/validatePhoneNumber', {
    num: unformattedNumber,
  }).then((response) => {
    commit('busy')
    console.log(response)
    console.log(response.data)
    commit('setMobileResponse', response.data)
    return false
  }).catch((error) => {
    console.log(error)
    commit('busy')
    return false
    Vue.toasted.error('Error: ' + error.message)
    return false
  })

}



