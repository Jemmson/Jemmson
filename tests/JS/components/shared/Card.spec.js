import { shallowMount } from "@vue/test-utils";
import Card from '../../../../resources/assets/js/components/shared/Card';

describe('Card', () => {

    const wrapper = shallowMount(Card, {
    });

    it('Should not render card-header', () => {
        expect(wrapper.find('.card-header').exists()).toBe(false);
    });

    it('Should render card-body', () => {
        expect(wrapper.find('.card-body').exists()).toBe(true);
    });

    it('Should not render card-footer', () => {
        expect(wrapper.find('.card-footer').exists()).toBe(false);
    });

    it('Should render card-header', () => {
        wrapper.setProps({
            header: 'true',
        });
        expect(wrapper.find('.card-header').exists()).toBe(true);
    });

    it('Should render card-footer', () => {
        wrapper.setProps({
            footer: 'true',
        });
        expect(wrapper.find('.card-footer').exists()).toBe(true);
    });

});