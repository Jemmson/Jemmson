import {
    shallowMount
} from "@vue/test-utils";
import sinon from 'sinon';
import SubInviteModal from '../../../../resources/assets/js/components/task/SubInviteModal';

require('../../bootstrap');

describe('SubInviteModal', () => {
    const sendSubInviteToBidOnTask = sinon.spy();
    const autoComplete = sinon.spy();
    const wrapper = shallowMount(SubInviteModal, {
        methods: {
            sendSubInviteToBidOnTask,
            autoComplete
        },
        propsData: {
            jobTask: {
                bid_contractor_job_tasks: [

                ],
                task: {
                    name: "Clean Pool"
                }
            }
        },
        data: () => {
            return {
                results: [
                    {
                        id: 1,
                        name: 'Jane',
                        email: 'jane@email.com',
                        phone: '4903477834',
                        contractor: {
                            ccompany_name: 'ha'
                        }
                    }
                ]
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('Should have tried to send a sub invite', () => {
        const submit = wrapper.find({
            ref: 'submit',
        });
        submit.trigger('click');

        expect(sendSubInviteToBidOnTask.calledOnce).toBe(true);
    });

    it('Should have tried to get existing subcontractors with the name in the name field', () => {
        const submit = wrapper.find("#contractorName");
        submit.trigger('keyup');

        expect(autoComplete.calledOnce).toBe(true);
    });

    it('Should fill input fields when a result is clicked', () => {
        const subButton = wrapper.find('.list-group-item');
        subButton.trigger('click');
        
        expect(wrapper.vm.initiateBidForSubForm.name).toBe("Jane");
        expect(wrapper.vm.initiateBidForSubForm.email).toBe("jane@email.com");
        expect(wrapper.vm.initiateBidForSubForm.phone).toBe("4903477834");
    });
});