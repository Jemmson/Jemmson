import {
  shallowMount, mount, createLocalVue
} from '@vue/test-utils'
import sinon from "sinon";
import CompletedTasks from '../../resources/assets/js/pages/FurtherInfo'
import moxios from 'moxios'
import Vuex from 'vuex'

const localVue = createLocalVue()

localVue.use(Vuex)

require('./bootstrap')

describe('FurtherInfo', () => {
  const submitFurtherInfo = sinon.spy();
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
    data: () => {
      return {
      }
    },  
    methods: {
      submitFurtherInfo,
      checkValidData () {
        return false;
      },
      initAutocomplete() {
        return true;
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

  it('Should render itself', () => {
    expect(wrapper.isEmpty()).toBe(false);
  });

  it('Should render the contractor heading - contractor', () => {
    expect(wrapper.html()).toContain("Register Your Company");
  });

  it('Should not render customer heading - contractor', () => {
    expect(wrapper.html()).not.toContain("Please Add Additional Information");
  });

  it('Should show errors when there are errors', () => {
    wrapper.setData({
      form: {
        errors: {
          email: '',
          name: '',
          company_name: '',
          phone_number: '',
          address_line_1: '',
          city: '',
          state: '',
          zip: '',
          password: '',
          password_confirmation: '',
        }
      }
    });
    const errors = wrapper.findAll('.help-block');
    expect(errors.length).toBe(10);
  });
  
  it('Should try and submit further info', () => {
    const submit = wrapper.find('[type=submit]');
    submit.trigger('click');
    expect(submitFurtherInfo.calledOnce).toBe(true);
  });

});