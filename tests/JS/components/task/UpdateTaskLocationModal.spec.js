import {
    shallowMount
} from "@vue/test-utils";
import UpdateTaskLocationModal from '../../../../resources/assets/js/components/task/UpdateTaskLocationModal';

require('../../bootstrap');

describe('Update Task Location Modal', () => {
    const wrapper = shallowMount(UpdateTaskLocationModal, {
        propsData: {
            jobTask: {
                location: null
            }
        },
        methods: {
            initAutocomplete () {
                return;
            }
        }
    });

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});