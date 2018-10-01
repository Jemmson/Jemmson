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

    it('Should be true', () => {
        expect(true).toBe(true);
    });
});