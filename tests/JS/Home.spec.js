import {createLocalVue, shallowMount} from '@vue/test-utils'
import Home from "../../resources/assets/js/pages/Home";
import Vuetify from 'vuetify'
import VueMask from 'v-mask'
import Format from "../../resources/assets/js/classes/Format";
import StripeMixin from "../../resources/assets/js/components/mixins/StripeMixin";

global.Format = Format;

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('Home', () => {

    let wrapper;

    beforeEach(() => {
        global.axios = require('axios');
        global.Spark = {
            state: {
                user: {
                    id: 1,
                    customer_stripe_id: 'cust_1234',
                    contractor: {
                        accounting_software: '',
                        stripe_express: {
                            stripe_user_id: 'test_user_id'
                        },
                        location: {
                            address_line_1: '',
                            address_line_2: '',
                            city: '',
                            state: '',
                            zip: '',
                        }
                    },
                    usertype: 'contractor'
                }
            }
        }

        /**
         * We'll load the axios HTTP library which allows us to easily issue requests
         * to our Laravel back-end. This library automatically handles sending the
         * CSRF token as a header based on the value of the "XSRF" token cookie.
         */
        axios.defaults.headers.common = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': Spark.csrfToken
        };

        wrapper = shallowMount(Home, {
            localVue,
            mocks: {
                $router: {
                    push: jest.fn(),
                    history: {
                        current: {
                            path: ''
                        }
                    }
                },
                $store: {
                    commit: jest.fn()
                }
            },
            directives: {
                mask() {
                }
            },
            propsData: {
                user: {
                    user: {}
                }
            }
        })

    });

    test('is a Vue instance', () => {
        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('that the stripe express dashboard shows up if the user is a contractor and ' +
        'the contractor has stripe', async () => {

        wrapper.setData({
            show: {
                details: false,
                jobs: false,
                subs: false,
                contractors: false,
                customers: false,
                invoices: false,
                stripe: true,
            }
        });

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'stripeExpressDashboard'}).exists()).toBe(true);
    })
    
    test('contractor navigation bar shows up if the user is a contractor', async () => {
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'contractorNavigationBar'}).exists()).toBe(true);
        
    })

    test('should show stripe nav button if the contractor is signed up for stripe', async () => {
        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'subsNavButton'}).exists()).toBe(true);
    })

})