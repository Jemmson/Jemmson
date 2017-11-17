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

  },
  actions: actions,
  getters: getters,
  mutations: mutations
})