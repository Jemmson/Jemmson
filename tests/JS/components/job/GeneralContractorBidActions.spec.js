import {
    shallowMount
} from "@vue/test-utils";
import sinon from "sinon";
import GeneralContractorBidActions from '../../../../resources/assets/js/components/job/GeneralContractorBidActions';

require('../../bootstrap');

describe('GeneralContractorBidActions', () => {
    const openModal = sinon.spy();
    const openAddTask = sinon.spy();
    const wrapper = shallowMount(GeneralContractorBidActions, {
        methods: {
            openModal,
            openAddTask
        },
        stubs: [
            'modal'
        ],
        propsData: {
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: '2018-08-01 10:58:37',
                status: 'bid.initiated',
                contractor_id: 1, //
                bid_price: 99.00,
                declined_message: null,
                location: null,
                job_tasks: [
                    {},
                    {}
                ]
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('Should render preapproved actions - contractor', () => {
        expect(wrapper.vm.showPreApprovedActions).toBe(true);
    });

    it('Should try and open a modal when clicking the cancel button - contractor', () => {
        const cancel = wrapper.find({
            ref: 'cancelBtn'
        });
        cancel.trigger('click');
        expect(openModal.calledOnce).toBe(true);
    });

    it('Should try and open a modal when clicking the add task button - contractor', () => {
        const addTaskBtn = wrapper.find("#addTaskToBid");
        addTaskBtn.trigger('click');
        expect(openAddTask.calledOnce).toBe(true);
    });

    it('Should try and open a modal when clicking the submit bid button - contractor', () => {
        const submitBidBtn = wrapper.find({
            ref: 'submitBidBtn'
        });
        submitBidBtn.trigger('click');
        expect(openModal.calledTwice).toBe(true);
    });

    it('Should render the modal component', () => {
        expect(wrapper.html()).toContain("modal-stub");
    });


});