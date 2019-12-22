import Language from '../classes/Language'

/**
 * Created by shawnpike on 3/2/17.
 */

export const actCustomerName = ({commit}, payload) => {
  axios.post('/bid/customer/getName', {
    id: payload
  }).then((response) => {
    console.log(response)
    console.log(response.data)
    console.log(response.data.name)
    // return response.data.name
    commit('setCustomerName', response.data)
  }).catch((error) => {
    console.log(error)
  })
}

function unformatNumber(number) {
  let unformattedNumber = ''
  for (let i = 0; i < number.length; i++) {
    if (!isNaN(parseInt(number[i]))) {
      unformattedNumber = unformattedNumber + number[i]
    }
  }
  return unformattedNumber
}

export const checkMobileNumber = ({commit}, phone) => {

  commit('busy')

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



