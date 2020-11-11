import {shallowMount, createLocalVue} from '@vue/test-utils'
import UploadJobImages from '../../resources/assets/js/components/task/UploadJobImages'

const localVue = createLocalVue()
import Vuetify from 'vuetify'
import Vue from 'vue'

global.Bus = new Vue()

global.Spark = {
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: ''
            },
            usertype: 'customer'
        }
    }
}

require('./setup');
localVue.use(Vuetify, {})

describe('UploadJobImages', () => {
    let wrapper;
    let vuetify = new Vuetify()

    beforeEach(() => {
        wrapper = shallowMount(UploadJobImages, {
            vuetify,
            mocks: {
                $router: {
                    push: jest.fn()
                }
            }
        })
    })

    test('is a Vue instance', () => {
        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('that that I can see a help icon', async () => {

        const helpIcon = wrapper.find("#helpIcon")
        await wrapper.vm.$nextTick()
        expect(helpIcon.exists()).toBe(true)

    })

    test('that the help dialog will open when the help button is clicked', async () => {

        wrapper.setData({
            openHelp: false
        })

        expect(wrapper.find("#helpDialog").exists()).toBe(false)
        const helpIcon = wrapper.find("#helpIcon")
        helpIcon.trigger('click')
        await wrapper.vm.$nextTick()
        expect(wrapper.find("#helpDialog").text()).toBe('Image Help')

    })

})