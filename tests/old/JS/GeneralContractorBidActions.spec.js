import { shallowMount, mount } from "@vue/test-utils";
import sinon from "sinon";
import GeneralContractorBidActions from '../../resources/assets/js/components/job/GeneralContractorBidActions';
import GeneralContractor from '../../resources/assets/js/classes/GeneralContractor';

// require('../../bootstrap');

describe('GeneralContractorBidActions', () => {
    const openModal = sinon.spy();
    const openAddTask = sinon.spy();
    const notifyCustomerOfFinishedBidStub = sinon.stub();
    const wrapper = shallowMount(GeneralContractorBidActions, {
        methods: {
            // openModal,
            // openAddTask
        },
        stubs: [
            // 'modal'
        ],
        mocks: {
          GeneralContractor: {
              notifyCustomerOfFinishedBid: notifyCustomerOfFinishedBidStub
          }
        },
        propsData: {
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: '2018-08-01 10:58:37',
                status: 'bid.initiated',
                contractor_id: 1, //
                bid_price: 99.00,
                declined_message: null,
                location: null,
                job_tasks: [],
                contractor: {
                    stripe_id: null
                }
            }
        }
    });

    it('Should render itself', () => {

        const wrapper = shallowMount(GeneralContractorBidActions)

        expect(wrapper.isEmpty()).toBe(false);
    });

    it.skip('the submit button should emit an event so that a card section will be displayed', function() {
        const wrapper = shallowMount(GeneralContractorBidActions)
        const openBidSubmissionDialogStub = sinon.stub();

        wrapper.setMethods({
            openBidSubmissionDialog: openBidSubmissionDialogStub
        })

        let btn = wrapper.find({ref: 'submitBid'})

        btn.trigger('click');

        expect(openBidSubmissionDialogStub.called).toBe(true)
    })

    it('should show warning when sub price is higher than the generals price', function() {

        const wrapper = shallowMount(GeneralContractorBidActions)

        wrapper.setData({
            subTaskWarning: false
        })

        wrapper.setProps({
            bid: {
                job_tasks: [
                    {
                        sub_final_price: 100,
                        cust_final_price: 90
                    }
                ]
            }
        })



        let btn = wrapper.find({ref: 'submitBid'})

        btn.trigger('click');

        expect(wrapper.find({ref:'subTaskWarning'}).exists()).toBe(true)

    })

    it('should call notifyCustomerOfFinishedBid method when the submit button is hit', function() {

        const wrapper = shallowMount(GeneralContractorBidActions)
        const openBidSubmissionDialogStub = sinon.stub();
        const submitBidStub = sinon.stub();

        wrapper.setData({
            subTaskWarning: false
        })

        wrapper.setProps({
            bid: {
                job_tasks: [
                    {
                        sub_final_price: 100,
                        cust_final_price: 90
                    }
                ]
            }
        })

        wrapper.setMethods({
            openBidSubmissionDialog: openBidSubmissionDialogStub,
            submitBid: submitBidStub
        })


        let btn = wrapper.find({ref: 'submitBid'})

        btn.trigger('click');


        // expect(openBidSubmissionDialogStub.called).toBe(true)
        expect(submitBidStub.called).toBe(true)

    })

    it('the submit button should be disabled if there are no job tasks', function() {

        wrapper.setProps({
            bid: {
                job_tasks: [],
                status: 'bid.initiated'
            }
        })

        expect(wrapper.find({ref: "submitBid"}).attributes().disabled).toBe("disabled")

    })

    it('the submit button should be disabled if there are no job tasks and the status is bid.sent', function() {

        wrapper.setProps({
            bid: {
                job_tasks: [],
                status: 'bid.sent'
            }
        })

        expect(wrapper.find({ref: "submitBid"}).attributes().disabled).toBe("disabled")

    })


    it('the submit button should be disabled if there are job tasks and the status is bid.sent', function() {

        wrapper.setProps({
            bid: {
                job_tasks: [
                    {}, {}
                ],
                status: 'bid.sent'
            }
        })

        expect(wrapper.find({ref: "submitBid"}).attributes().disabled).toBe("disabled")

    })

    it('the submit button should be disabled if there are job tasks and the status is bid.sent', function() {

        wrapper.setProps({
            bid: {
                job_tasks: [
                    {}, {}
                ],
                status: 'bid.initiated'
            }
        })

        expect(wrapper.find({ref: "submitBid"}).attributes().disabled).toBe(undefined)

    })


    it('should call the setupStripe method if the submit button is hit and the contractor has not setup stripe', function() {

        const checkCreditCardSetupStub = sinon.stub();
        const notifyCustomerOfFinishedBidStub = sinon.stub();

        wrapper.setProps({
            bid: {
                contractor: {
                    stripe_id: null
                },
                job_tasks: [
                    {}, {}
                ],
                status: 'bid.initiated'
            }
        })

        wrapper.setMethods({
            checkCreditCardSetup: checkCreditCardSetupStub,
            notifyCustomerOfFinishedBid: notifyCustomerOfFinishedBidStub
        })

        let ccSetup = wrapper.find({ref: "submitBid"});

        ccSetup.trigger('click')

        expect(checkCreditCardSetupStub.called).toBe(true)

    })

    it('should not show the stripe button if stripe has not setup by the contractor', function() {

        wrapper.setProps({
            bid: {
                contractor: {
                    stripe_id: 1234
                }
            }
        })

        expect(wrapper.find({ref: "stripeButton"}).exists()).toBe(false)

    })

    it('the stripe button should trigger the stripe modal if it is clicked', function() {
        wrapper.setProps({
            bid: {
                contractor: {
                    stripe_id: 1234
                }
            }
        })

        let stripeBtn = wrapper.find({ref: "stripeButton"})

        stripeBtn.trigger("click")



    })

    // it('Should render preapproved actions - contractor', () => {
    //     expect(wrapper.vm.showPreApprovedActions).toBe(true);
    // });
    //
    // it('Should try and open a modal when clicking the cancel button - contractor', () => {
    //     const cancel = wrapper.find({
    //         ref: 'cancelBtn'
    //     });
    //     cancel.trigger('click');
    //     expect(openModal.calledOnce).toBe(true);
    // });
    //
    // it('Should try and open a modal when clicking the add task button - contractor', () => {
    //     const addTaskBtn = wrapper.find("#addTaskToBid");
    //     addTaskBtn.trigger('click');
    //     expect(openAddTask.calledOnce).toBe(true);
    // });
    //
    // it('Should try and open a modal when clicking the submit bid button - contractor', () => {
    //     const submitBidBtn = wrapper.find({
    //         ref: 'submitBidBtn'
    //     });
    //     submitBidBtn.trigger('click');
    //     expect(openModal.calledTwice).toBe(true);
    // });
    //
    // it('Should render the modal component', () => {
    //     expect(wrapper.html()).toContain("modal-stub");
    // });


});