import {
    shallowMount
} from "@vue/test-utils";
import Stripe from '../../../../resources/assets/js/components/stripe/Stripe';
import User from '../../../../resources/assets/js/classes/User';

require('../../bootstrap');

describe('Stripe', () => {
    
    const wrapper = shallowMount(Stripe, {
        stubs: [
            'connect-with-stripe',
            'signup-with-stripe'
        ],
        propsData: {
            user: User.user
        }
    });

    it('Should render the stripe express section', () => {
        expect(wrapper.html()).toContain('Before you can continue');
    });

    it('Should not render the stripe express section', () => {
        expect(wrapper.html()).not.toContain('Before You can pay');
    });

    it('Should render the connnect-with-stripe component', () => {
        expect(wrapper.html()).toContain('connect-with-stripe-stub');
    });

    it('Should not render the signup-with-stripe component', () => {
        expect(wrapper.html()).not.toContain('signup-with-stripe');
    });

    it('Should render the signup-with-stripe component', () => {
        User.setUser({
            id: 1,
            usertype: 'customer',
            contractor: null,
            customer: {}
        });
        const wrapper = shallowMount(Stripe, {
            stubs: [
                'connect-with-stripe',
                'signup-with-stripe'
            ],
            propsData: {
                user: User.user
            }
        });
        expect(wrapper.html()).toContain('signup-with-stripe');
    });
    

});