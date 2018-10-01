import {
    shallowMount
} from "@vue/test-utils";
import sinon from 'sinon';
import DenyTaskModal from '../../../../resources/assets/js/components/task/DenyTaskModal';

require('../../bootstrap');


describe('Deny Task Modal', () => {
    const denyTask = sinon.spy();
    const wrapper = shallowMount(DenyTaskModal, {
        propsData: {
            jobTask: {
                task: {
                    name: "Clean Pool"
                }
            }
        },
        methods: {
            denyTask
        }
    });

    it('Should render itself', () => {
        expect(wrapper.exists()).toBe(true);
    });

    it('Should call denyTask when deny button is clicked', () => {
        const denyTaskBtn = wrapper.find({
            ref: 'denyTaskBtn'
        })
        denyTaskBtn.trigger('click');
        expect(denyTask.calledOnce).toBe(true);
    });
});