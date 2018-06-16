/**
 * Created by shawnpike on 3/2/17.
 */

export const actCustomerName = ({commit}, payload) => {
  axios.post ('/bid/customer/getName', {
    id: payload
  }).then ((response) => {
    console.log (response)
    console.log (response.data)
    console.log (response.data.name)
    // return response.data.name
    commit ('setCustomerName', response.data)
  }).catch ((error) => {
    console.log (error);
  })
}


