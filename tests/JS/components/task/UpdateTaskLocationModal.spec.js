import {
    shallowMount
} from "@vue/test-utils";
import sinon from 'sinon';
import UpdateTaskLocationModal from '../../../../resources/assets/js/components/task/UpdateTaskLocationModal';

// require('../../bootstrap');

describe('UpdateTaskLocationModal', () => {
    const update = sinon.spy();
    const wrapper = shallowMount(UpdateTaskLocationModal, {
        propsData: {
            jobTask: {
                location: null
            }
        },
        methods: {
            update,
            initAutocomplete () {
                return;
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('Should try and update task location', () => {
        const submit = wrapper.find('[type=submit]');
        submit.trigger('click');
        expect(update.calledOnce).toBe(true);
    });
});