import { mount } from 'vue-test-utils'
import expect from 'expect'
import JemmDate from '../../resources/assets/js/components/job/JemmDate.vue'

// import { shallow, createLocalVue } from 'vue-test-utils'
// import Vuex from 'vuex'
// // import Actions from '../../resources/assets/js/vuex/modules/job/actions.js'
// const localVue = createLocalVue() // this is a local vue constructor so as not to affect the global vue constructor

localVue.use(Vuex)

describe ('JemmDate', () => {
  let wrapper;
  let actions
  let store

  beforeEach (() => {
    wrapper = mount (JemmDate);

    // these are action stubs of the actions that are being stubbed out.
    // the point of the jest.fn() are to let us know if the methods are being called or not
    actions = {
      actionClick: jest.fn(),
      actionInput: jest.fn()
    }

    // I am creating a store in the before Each method so that the store is clean with each test
    store = new Vuex.Store({
      state: {},
      actions
    })
  })


  afterEach (() => {

  })

  it ('shows the start date error when the start date is set after the end date', () => {

  });

})