import {
    shallowMount
} from "@vue/test-utils";
import Stripe from '../../resources/assets/js/components/stripe/Stripe';

require('./bootstrap');

describe('Completed Tasks', () => {
    const wrapper = shallowMount(Stripe, {
        propsData: {
            user: global.User.user
        }
    });

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});