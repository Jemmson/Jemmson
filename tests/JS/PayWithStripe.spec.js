import {
    shallowMount
} from "@vue/test-utils";
import PayWithStripe from '../../resources/assets/js/components/stripe/PayWithStripe';

require('./bootstrap');


let Stripe = () => {
    return {
        elements: () => {
            return {
                create: () => {
                    return {
                        mount () {

                        },
                        addEventListener () {

                        }
                    }
                }
            }
        }
    }
}

global.Stripe = Stripe;

describe('PayWithStripe', () => {
    const wrapper = shallowMount(PayWithStripe, {
        methods: {},
        stubs: [
        ],
        propsData: {
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('Should render the text Sign Up', () => {
        expect(wrapper.html()).toContain("Sign Up");
    });

});