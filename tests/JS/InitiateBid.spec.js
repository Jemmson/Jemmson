import {
  mount,
  createLocalVue
} from 'vue-test-utils'
import Vuex from 'vuex'

const localVue = createLocalVue()

localVue.use(Vuex)

import InitiateBid from '../../resources/assets/js/pages/InitiateBid'

describe ('InitiateBid', () => {
    let actions;
    let getters;
    let mutations;
    let store;

    beforeEach(() => {
      actions = {
      };
      mutations = {
        setTheMobileResponse: () => 'hell',
      }
      getters = {
        getMobileValidResponse: () => ['phone', 'mobile', 'land'],
      };
      store = new Vuex.Store({
        state: {},
        actions,
        getters,
        mutations
      });
    })

  it ('should display The Phone field is required. if name is input but not phone number', function () {
    const wrapper = mount(InitiateBid, {
      store,
      localVue,
      data () {
        return {
          query: '',
          results: [],
          form: new SparkForm ({
            phone: '',
            customerName: ''
          }),
          disabled: {
            submit: false
          }
        }
      }
    })
  });
})