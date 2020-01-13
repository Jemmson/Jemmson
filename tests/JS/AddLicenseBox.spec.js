import { shallowMount, createLocalVue } from '@vue/test-utils'
import AddLicenseBox from '../../resources/assets/js/components/user/AddLicenseBox'
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'

require('./setup')

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuetify, {})

describe('AddJobTask', () => {
  let vuetify = new Vuetify()
  let wrapper
  test('when I delete a license then it should be removed from the license array', () => {
    wrapper = shallowMount(AddLicenseBox, {
      vuetify,
      localVue
    })

    wrapper.setData({
      licenses: [
        {
          name: 'swimming pool',
          number: '1',
          state: 'arizona',
          type: 'A-4',
        },
        {
          name: 'swimming pool',
          number: '2',
          state: 'alabama',
          type: 'A-7',
        },
        {
          name: 'swimming pool',
          number: '3',
          state: 'colorado',
          type: 'A-5',
        }
      ],
      license: {
        name: 'swimming pool',
        number: '2',
        state: 'alabama',
        type: 'A-7',
      },
    })

    wrapper.vm.removeLicense(wrapper.vm.license)

    expect(wrapper.vm.licenses.length).toBe(2)

  })
})