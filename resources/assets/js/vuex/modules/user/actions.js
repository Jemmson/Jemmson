export const login = ({commit}, payload) => {
  payload.error = null
  payload.busy = true
  axios.post('login', payload)
    .then((response) => {
      console.log(response.data)
      commit('setUser', response.data)
      Bus.$emit('updateUser')
      location.href = '/#/home'
      payload.busy = false
    })
    .catch((error) => {
      console.log(error)
      Vue.toasted.error('The user name or password is incorrect')
      payload.error = 'The user name or password is incorrect'
      payload.busy = false
    })
}
