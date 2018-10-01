import {
  shallowMount, mount, createLocalVue
} from '@vue/test-utils'
import CompletedTasks from '../../resources/assets/js/pages/FurtherInfo'
import moxios from 'moxios'
import Vuex from 'vuex'

const localVue = createLocalVue()

localVue.use(Vuex)

require('./bootstrap')

describe('Further Info', () => {
  let getters
  let store
  let mutations

  moxios.install()

  getters = {
    getMobileValidResponse: () => ['phone', 'mobile', 'land'],
  }
  mutations = {
    setTheMobileResponse: () => 'hell',
  }
  store = new Vuex.Store({
    state: {},
    getters,
    mutations
  })

  const wrapper = mount(CompletedTasks, {
    store,
    localVue,
    methods: {
      initAutocomplete() {
        return true
      }
    },
    propsData: {
      user: {
        name: 'John',
        email: 'john@john.com',
        contractor: null
      }
    }
  })

  it('it should render something', () => {
    expect(wrapper.isEmpty()).toBe(false)
  })

})