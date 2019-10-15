import { createLocalVue, shallowMount, mount } from '@vue/test-utils'
import JobTasks from '../../resources/assets/js/pages/JobTasks'
import sinon from 'sinon'
import expect from 'expect'
import Vuex from 'vuex'

import VueRouter from 'vue-router'

require('./bootstrap')

const localVue = createLocalVue()
localVue.use(Vuex)
localVue.use(VueRouter)

describe('JobTasks', () => {
  const router = new VueRouter()
  let getters
  let store

  getters = {}

  store = new Vuex.Store({
    getters
  })

  beforeEach(() => {

  })

  afterEach(() => {

  })

})