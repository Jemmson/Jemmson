##User.js

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
  
initAutocomplete(id) {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  window.autocomplete = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */
    (document.getElementById(id)), {
      types: ['address']
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


##FurtherInfo.vue

```<template>

<input type="hidden" name="street_number" id="street_number">
<input type="hidden" name="country" id="country">

<input type="text" class="border input" name="address_line_1" id="route"
v-model="form.address_line_1">

 <input type="text" class="form-control" name="city" id="administrative_area_level_1"
v-model="form.city">

<input type="text" class="border input" name="state" id="locality" v-model="form.state">

<input type="text" class="border input" name="zip" id="postal_code" v-model="form.zip">


</template>

methods: () {
initAutocomplete() {
        User.initAutocomplete('route')
      },      
updateFormLocation(location) {
      this.form.address_line_1 = location.route
      this.form.city = location.locality
      this.form.state = location.administrative_area_level_1
      this.form.zip = location.postal_code
    },
}
mounted(){
this.initAutocomplete()
Bus.$on('updateFormLocation', (payload) => {
        this.updateFormLocation(payload)
      })
}``