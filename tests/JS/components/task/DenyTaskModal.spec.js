import {
    shallowMount
} from "@vue/test-utils";
import DenyTaskModal from '../../../../resources/assets/js/components/task/DenyTaskModal';

require('../../bootstrap');

describe('Deny Task Modal', () => {
    const wrapper = shallowMount(DenyTaskModal, {
        propsData: {
            jobTask: {
                task: {
                    name: "Clean Pool"
                }
            }
        }
    });

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});