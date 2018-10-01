import {
    shallowMount
} from "@vue/test-utils";
import UpdateTaskLocationModal from '../../../../resources/assets/js/components/task/UpdateTaskLocationModal';

require('../../bootstrap');

describe('Update Task Location Modal', () => {
    const wrapper = shallowMount(UpdateTaskLocationModal, {
    });

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});