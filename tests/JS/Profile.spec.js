import {createLocalVue, shallowMount, mount, config} from '@vue/test-utils'
import Profile from "../../resources/assets/js/pages/Profile";
import Vuetify from "vuetify"
import Vuex from "vuex";
import VueRouter from 'vue-router'
// const Stripe = require('./stripe')
import Vue from 'vue'
import {fn} from "moment";

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuetify)
localVue.use(Vuex)
global.Bus = new Vue()


// require('./stripeSetup')


global.Spark = {
    stripeKey: 'sdksdklsd',
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: '',
                stripe_express: {
                    stripe_user_id: 'test_user_id'
                }
            },
            usertype: 'contractor'
        }
    }
}

require('./setup')

describe('Profile', function () {

    let wrapper
    let vuetify
    let storeOptions
    let store


    beforeEach(() => {

        vuetify = new Vuetify()
        storeOptions = {
            commit: jest.fn(),
            actions: {
                // checkMobileNumber: jest.fn(() => Promise.resolve())
            },
            mutations: {
                setUser: jest.fn()
            },
            getters: {
                getMobileValidResponse: jest.fn()
            },
            mocks: {
                Stripe: {
                    elements: jest.fn()
                }
            }
        }

        store = new Vuex.Store(storeOptions)

        wrapper = mount(Profile, {
            vuetify,
            localVue,
            propsData: {
                user: {
                    name: '',
                    email: '',
                    first_name: '',
                    last_name: '',
                    phone: '',
                    contractor: {
                        location: {
                            address_line_1: '',
                            address_line_2: '',
                            city: '',
                            state: '',
                            zip: '',
                        }
                    },
                    photo_url: 'https://res.cloudinary.com/jemmson-inc/image/upload/v1586043293/dslksdlkdslksdlk.jpg'
                }
            }
        });

    })

    test('that the component is set up correctly', async () => {

        await wrapper.vm.$nextTick()
        expect(true).toBe(true)

    })

    test.skip('test that if I hit the rotate btn that the degree will change to the next array entry', async () => {

        wrapper.setData({
            degree: 4,
            show: {
                photo: false
            }
        })

        const currentPicture = 'https://res.cloudinary.com/jemmson-inc/image/upload/a_90/v1586043293/dslksdlkdslksdlk.jpg';

        const btn = wrapper.find({ref: 'showPhotoSection'});

        btn.trigger('click');

        await wrapper.vm.$nextTick();

        expect(wrapper.find({ref: 'photoSection'}).exists()).toBe(true);

        console.log(wrapper.find({ref: 'userProfilePhoto'}).attributes('style'));
        console.log(wrapper.find({ref: 'userProfilePhoto'}).vnode.children[1].elm.style._values);

        debugger;

        expect(wrapper.find({ref: 'userProfilePhoto'}).attributes('style')).toBe(currentPicture);
        // expect(wrapper.find('.v-image').vnode.children[1].elm.style._values).toBe(currentPicture);

    })

    test.skip('that if photo url is clicked that the photo will rotate 90 degrees', async () => {

        wrapper.setData({
            degree: 4,
            show: {
                photo: true,
                contactInformation: false,
            },
            photoUrl: 'https://res.cloudinary.com/jemmson-inc/image/upload/v1586043293/dslksdlkdslksdlk.jpg'
        })

        const newPhotoUrl = 'https://res.cloudinary.com/jemmson-inc/image/upload/a_90/v1586043293/dslksdlkdslksdlk.jpg'

        await wrapper.vm.$nextTick()
        const btn = wrapper.find({ref: 'rotateBtn'});
        btn.trigger('click')
        expect(wrapper.vm.photoUrl).toBe(newPhotoUrl);

    })



})