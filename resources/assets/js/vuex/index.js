import Vue from 'vue'
import Vuex from 'vuex'
import job from './modules/job'
import * as actions from './actions'
import * as getters from './getters'
import * as mutations from './mutations'

Vue.use(Vuex);

export const store = new Vuex.Store({
  modules: {
    job
  },
  state: {
    customer: {
      name: ''
    },
    mobile: {
      number: '',
      response: ''
    },
    features: {
      quickbooks: 0
    }
  },
  actions: actions,
  getters: getters,
  mutations: mutations
})
