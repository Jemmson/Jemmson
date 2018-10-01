import {
    shallowMount
} from "@vue/test-utils";
import SubinviteModal from '../../../../resources/assets/js/components/task/SubinviteModal';

require('../../bootstrap');

describe('Subinvite Modal', () => {
    const wrapper = shallowMount(SubinviteModal, {
        propsData: {
            jobTask: {
                task: {
                    name: "Clean Pool"
                }
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.exists()).toBe(true);
    });

    it('Should show errors when submitting an empty form', () => {
        const submit = wrapper.find({
            ref: 'submit',
        });
        submit.trigger('click');
        const name = wrapper.find({
            ref: 'name'
        });
        expect(name.isVisible()).toBe(true);
    });
});