import {createLocalVue, shallowMount} from '@vue/test-utils'
import AssociatedContractors from "../../resources/assets/js/pages/AssociatedContractors";
import Vuetify from 'vuetify'

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('AssociatedContractors', () => {

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

        wrapper = shallowMount(AssociatedContractors, {
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

    test('that the data gets transformed correctly', async () => {

        const dataFromBackend = [{"id":3,"name":"Kristen Battafarano","contractor":{"company_name":"Garden Bud"},"tax_rate":0},{"id":4,"name":"Susan Franchuk","contractor":{"company_name":"Sue the tax lady"},"tax_rate":0}];

        const result = wrapper.vm.dataTransformed(dataFromBackend);

        await wrapper.vm.$nextTick()
        expect(result).toStrictEqual([{"id":3,"name":"Kristen Battafarano","company_name":"Garden Bud"},{"id":4,"name":"Susan Franchuk","company_name":"Sue the tax lady"}])

    })

})