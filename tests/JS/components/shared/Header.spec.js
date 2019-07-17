import { shallowMount, createLocalVue } from '@vue/test-utils'
import Header from '../../../../resources/assets/js/components/shared/Header'
import sinon from 'sinon'
import expect from 'expect'

import Vuex from 'vuex'

const localVue = createLocalVue()
localVue.use(Vuex)

require('../../bootstrap')

describe('Header', () => {

  it('should show contractor jobs when contractor link is clicked', function() {

  })

  it('should show first name and last name if page does not = / or it is not undefined and the current page is the home page', function() {

    let store = new Vuex.Store({
      state: {
        page: '/home',
        bidsContractorSectionPicked: '',
        user: {
          user: {
            usertype: '',
            contractor: {
              companyName: ''
            },
            first_name: 'Joe',
            last_name: 'Jakson'
          }
        }
      }
    })

    const wrapper = shallowMount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'customer',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    //
    expect(wrapper.find({ref: 'biographical_information'}).isVisible()).toBe(true)

  })

  it('should show jobs for contractors and subs if the page is bids and the user type is not a customer', function() {

    let store = new Vuex.Store({
      state: {
        page: '/bids',
        bidsContractorSectionPicked: '',
        user: {
          user: {
            usertype: 'contractor',
            contractor: {
              companyName: ''
            },
            first_name: 'Joe',
            last_name: 'Jakson'
          }
        }
      }
    })

    const wrapper = shallowMount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'contractor',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    //
    expect(wrapper.find({ref: 'job_toggle'}).isVisible()).toBe(true)

  })

  it('should not show jobs for contractors and subs if the page is bids and the user type is a customer', function() {

    let store = new Vuex.Store({
      state: {
        page: '/bids',
        bidsContractorSectionPicked: '',
        user: {
          user: {
            usertype: 'customer',
            contractor: {
              companyName: ''
            },
            first_name: 'Joe',
            last_name: 'Jakson'
          }
        }
      }
    })

    const wrapper = shallowMount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'customer',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    //
    expect(wrapper.find({ref: 'job_toggle'}).exists()).toBe(false)

  })

  it('should shown user name only on the home page', function() {

    let store = new Vuex.Store({
      state: {
        page: '/home',
        bidsContractorSectionPicked: '',
        user: {
          user: {
            usertype: 'customer',
            contractor: {
              companyName: ''
            },
            first_name: 'Joe',
            last_name: 'Jakson'
          }
        }
      }
    })

    const wrapper = shallowMount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'customer',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    //
    expect(wrapper.find({ref: 'user_name'}).text()).toBe('Joe Jakson')

  })

  it('should show the job name if the page is a bid', function() {

    let store = new Vuex.Store({
      state: {
        page: '/bid/9',
        bidsContractorSectionPicked: '',
        user: {
          user: {
            usertype: 'customer',
            contractor: {
              companyName: ''
            },
            first_name: 'Joe',
            last_name: 'Jakson'
          }
        },
        job: {
          model: {
            job_name: 'pool work'
          }
        }
      }
    })

    const wrapper = shallowMount(Header, {
      store,
      localVue,
      propsData: {
        user: {
          usertype: 'customer',
          contractor: {
            companyName: ''
          },
          first_name: 'Joe',
          last_name: 'Jakson'
        }
      }
    })

    //
    expect(wrapper.find({ref: 'job_name'}).text()).toBe('pool work')

  })

})