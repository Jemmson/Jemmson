import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import Feedback from '../../resources/assets/js/components/shared/Feedback'
import Vuetify from 'vuetify'
import Vue from 'vue'

require('./setup')


Vue.use(Vuetify)
const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('Feedback', () => {
  let wrapper
  let vuetify = new Vuetify()

  beforeEach(() => {
    wrapper = shallowMount(Feedback, {
      localVue,
      vuetify
    })
  })

  test.skip ('Should see Add Job button whern the user is a contractor', () => {

    Spark.state.user.usertype = 'contractor'

    const addbtn = wrapper.find('#addbtn')

    Vue.nextTick(() => {
      expect(addbtn.exists()).toBe(true);
    })

  })


  test ('calling printHello will return hello string', () => {
    expect(wrapper.vm.printHello()).toBe('Hello');
  })


})