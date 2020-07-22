import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'

global.Spark = {
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: ''
            },
            usertype: 'contractor'
        }
    }
}

require('./setup')

// window.Vue = Vue
import Vuetify from 'vuetify'
import vuex_mutations from "../../resources/assets/js/vuex/mutations";

Vue.use(Vuetify)

const localVue = createLocalVue()
localVue.use(Vuetify, {})
localVue.use(VueRouter)
localVue.use(Vuex)

const router = new VueRouter()

describe('InitiateBid', () => {
    let wrapper
    let vuetify
    let store
    let state
    let getters
    let mutations
    let spy

    vuetify = new Vuetify()

    beforeEach(() => {

        spy = jest.spyOn(console, 'log');

        getters = {
            getMobileValidResponse: () => ["success", "mobile", "mobile"]
        }

        state = {
            busy: true
        }

        mutations = {
            setPhoneLoadingValue: jest.fn(),
            setCurrentPage: jest.fn()
            //setPhoneLoadingValue: vuex_mutations.setPhoneLoadingValue(state)
        }

        store = new Vuex.Store({
            state,
            getters,
            mutations
        })

        wrapper = mount(InitiateBid, {
            localVue,
            store,
            vuetify,
            router,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                }
            }
        })

        wrapper.setData({
            form: {
                phone: ''
            }
        });
    })

    afterEach(() => {
    })

    window.Vue = {
        toasted: {
            success: jest.fn(),
            error: jest.fn()
        }
    }

    test('is a Vue instance', () => {
        wrapper = shallowMount(InitiateBid, {
            localVue,
            vuetify,
            store,
            router,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                    state: {
                        busy: false
                    }
                },
            },
            data() {
                return {}
            }
        })
        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test.skip('must have a title saying "Add New Job"', () => {
        wrapper = mount(InitiateBid, {
            localVue,
            vuetify,
            router,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                }
            },
            data() {
                return {}
            }
        })
        expect(wrapper.find('#title').text()).toBe('Add New Job')

    })

    test.skip('test that the first name validation works', async () => {
        wrapper = mount(InitiateBid, {
            localVue,
            vuetify,
            router,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                }
            },
            data() {
                return {}
            }
        })
        let fname = wrapper.find({ref: 'firstName'})
        wrapper.setData({
            fname: 'asdasdasdasdasdasd'
        })
        fname.trigger('keyup')
        await Vue.nextTick(() => {
            console.log('fname', fname.html())
            expect(wrapper.text()).toContain('Name must be less than 16 characters')
        })
    })

    test('that I am able to transalte the returned data into format that the combobox is looking for', () => {
        wrapper = mount(InitiateBid, {
            localVue,
            vuetify,
            store,
            router,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                }
            },
            data() {
                return {
                    comboResults: []
                }
            }
        })

        const data = [
            {
                'id': 3,
                'name': 'Shawn Pike',
                'first_name': 'Shawn',
                'last_name': 'Pike',
                'phone': '4807034902',
                'email': 'pike.shawn@gmail.com',
                'tax_rate': 0,
                'quickbooks_id': null
            },
            {
                'id': 2,
                'name': 'Jack Ripper',
                'first_name': 'Shawn',
                'last_name': 'Pike',
                'phone': '4807034902',
                'email': 'pike.shawn@gmail.com',
                'tax_rate': 0,
                'quickbooks_id': null
            }
        ]

        const transformedData = wrapper.vm.transformDataForComboBox(data)

        expect(transformedData).toEqual([
            {
                text: 'Shawn Pike',
                value: 3
            },
            {
                text: 'Jack Ripper',
                value: 2
            }
        ])

    })

    test('test that I can filter out the correct computed result from the selected result', () => {
        wrapper = mount(InitiateBid, {
            localVue,
            vuetify,
            router,
            store,
            directives: {
                mask() {
                }
            },
            mocks: {
                $store: {
                    commit: jest.fn(),
                }
            },
            data() {
                return {
                    results: [
                        {
                            'id': 3,
                            'name': 'Shawn Pike',
                            'first_name': 'Shawn',
                            'last_name': 'Pike',
                            'phone': '4807034902',
                            'email': 'pike.shawn@gmail.com',
                            'tax_rate': 0,
                            'quickbooks_id': null
                        },
                        {
                            'id': 2,
                            'name': 'Jack Ripper',
                            'first_name': 'Shawn',
                            'last_name': 'Pike',
                            'phone': '4807034902',
                            'email': 'pike.shawn@gmail.com',
                            'tax_rate': 0,
                            'quickbooks_id': null
                        }
                    ]
                }
            }
        })

        const selected = {
            text: 'Shawn Pike',
            value: 3
        }

        const filteredResult = wrapper.vm.getComboResult(selected)

        expect(filteredResult).toEqual(
            {
                'id': 3,
                'name': 'Shawn Pike',
                'first_name': 'Shawn',
                'last_name': 'Pike',
                'phone': '4807034902',
                'email': 'pike.shawn@gmail.com',
                'tax_rate': 0,
                'quickbooks_id': null
            },
        )
    })

    test('that if customer name is by itself then it will still allow the submit button to be enabled', async () => {
        const btn = wrapper.find({ref: 'submit'});
        // console.log('submit attributes', btn.attributes());
        // console.log('dataMustBeValid', wrapper.vm.dataMustBeValid())

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'submit'}).attributes().disabled).toBe('disabled')
    })

    test('that when the radio button is selected that the isMobile value is true', async () => {
        wrapper.setData({
            form: {
                isMobile: false
            }
        })
        const btn = wrapper.find('#isMobile')
        await btn.setChecked();
        expect(wrapper.vm.$data.form.isMobile).toBe(true);
    })

    test.skip('test that phone is saved in local storage after it has successfully typed', async () => {

        localStorage.setItem('mobile', '')
        wrapper.setData({
            form: {
                phone: ''
            }
        })

        let phone = wrapper.find('#phone')
        phone.setValue('444-444-4444')
        phone.trigger('keydown.tab');

        let cbx = wrapper.find('#customerName')
        cbx.setValue('Shawn Pike');
        cbx.trigger('keydown.enter');

        await wrapper.vm.$nextTick()
        expect(localStorage.getItem('mobile')).toBe('444-444-4444')
    })

    test.skip('the submit button is disabled if the isMobile checkbox is not checked', async () => {

        wrapper.setData({
            form: {
                customerName: '',
                phone: '',
                isMobile: false,
                firstName: '',
                jobName: '',
            },
            // selected: ['Shawn Pike'],
            // search: null,
            // results: [{"text":"Shawn  Pike","value":2},{"text":"jack back1234","value":5}]
        })

        // wrapper.setMethods({
        //     // transformDataForComboBox: () => {},
        //     // autoComplete: () => {
        //     //     this.results = [{"text":"Shawn  Pike","value":2},{"text":"jack back1234","value":5}]
        //     // }
        // })

        let cbx = wrapper.find('#customerName')
        cbx.setValue('Shawn Pike');
        cbx.trigger('keydown.tab');
        debugger;
        let mobile = wrapper.find('#phone')
        mobile.setValue('(5');
        // mobile.trigger('keydown', {
        //     key: 'a'
        // });
        await wrapper.vm.$nextTick()

        expect(wrapper.vm.form.phone).toBe('(555)-555-5555')
        // console.log('this.form.phone.length', wrapper.vm.form.phone.length)
        // console.log('this.form.selected', wrapper.vm.form.selected)
        // console.log('dataMustBeValid', wrapper.vm.dataMustBeValid())
        // console.log('allRequiredFieldsHaveAValue', wrapper.vm.allRequiredFieldsHaveAValue())
        // console.log('phoneNumberIsMobile', wrapper.vm.phoneNumberIsMobile())
        // console.log('isMobile', wrapper.vm.isMobile())
        // console.log('phoneNumberIsValid', wrapper.vm.phoneNumberIsValid())
        // console.log('phoneNumberIsValid', wrapper.vm.phoneDataVariableExists())
        // console.log('phoneNumberIsValid', wrapper.vm.phoneNumberHasProperLength())
        // console.log('cbx', cbx.html())
        // console.log('cbx', cbx.text())
        // console.log('this.search', wrapper.vm.search)
        // console.log('this.form.firstName', wrapper.vm.form.firstName)
        // console.log('this.form.jobName', wrapper.vm.form.jobName)
        // expect(wrapper.find({ref: 'submit'}).attributes().disabled).toBe('disabled')

        // expect(wrapper.vm.form.phone).toBe('')
        // expect(wrapper.vm.form.phone).toBe('(555) 555-5555')
        // expect(wrapper.vm.form.customerName).toBe('Shawn Pike')
        // expect(wrapper.vm.allRequiredFieldsHaveAValue()).toBe(true)
        // expect(wrapper.vm.phoneNumberIsMobile()).toBe(true)
        // expect(wrapper.vm.dataMustBeValid()).toBe(true)
        // expect(wrapper.find({ref: 'submit'}).attributes().disabled).toBe(undefined)

    })

})