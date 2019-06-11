import actions from './actions'
import * as getters from './getters'
import * as mutations from './mutations'

export default {
  namespaced: false,
  state: {
    user: ''
  },
  getters,
  mutations,
  actions
}
