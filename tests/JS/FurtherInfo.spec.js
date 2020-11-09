import { createLocalVue, shallowMount, mount, config } from '@vue/test-utils'
import FurtherInfo from "../../resources/assets/js/pages/FurtherInfo";
import Vuetify from 'vuetify'
import Vuex from "vuex";
import Vue from 'vue'

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
global.Bus = new Vue();
require('./setup')


Vue.use(Vuetify)
const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(Vuex)

describe('FurtherInfo', () => {
    let wrapper
    let vuetify = new Vuetify()
    let storeOptions
    let store

    beforeEach(() => {
        storeOptions = {
            commit: jest.fn(),
            actions: {
                // checkMobileNumber: jest.fn(() => Promise.resolve())
            },
            mutations: {
                setPhoneLoadingValue: jest.fn(),
                setCurrentPage: jest.fn()
            },
            getters: {
                getMobileValidResponse: () => {
                    return [
                        '',
                        '',
                        ''
                    ]
                }
            }
        };
        store = new Vuex.Store(storeOptions);
        wrapper = shallowMount(FurtherInfo, {
            localVue,
            vuetify,
            store,
            directives: {
                mask() {
                }
            },
            methods: {
                initAutocomplete: jest.fn()
            },
            mocks: {

            },
            propsData: {
                user: {
                    phone: '',
                    email: '',
                    name: '',
                    contractor: {
                        location: {
                            address_line_1: '',
                            address_line_2: ''
                        }
                    }
                }
            }
        })
    });

    test.skip ('Should see Add Job button whern the user is a contractor', () => {

        Spark.state.user.usertype = 'contractor'

        const addbtn = wrapper.find('#addbtn')

        Vue.nextTick(() => {
            expect(addbtn.exists()).toBe(true);
        })

    })

    test('expect that zip should have the right format', async () => {
        wrapper.setData({
            form: {
                zip: '12342-'
            }
        })
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.formatZip()).toBe('12342')

        wrapper.setData({
            form: {
                zip: '12342'
            }
        })
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.formatZip()).toBe('12342')

        wrapper.setData({
            form: {
                zip: '12342-1234'
            }
        })
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.formatZip()).toBe('12342-1234')
    })

    test('that zip must have atleast 5 characters', async () => {
        wrapper.setData({
            form: {
                zip: '12342'
            }
        })
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.zipMustHaveAtleast5characters()).toBe(true)

        wrapper.setData({
            form: {
                zip: '1234'
            }
        })
        await wrapper.vm.$nextTick()
        expect(wrapper.vm.zipMustHaveAtleast5characters()).toBe('Zip Code Must Be At Least 5 Characters')

    })

})