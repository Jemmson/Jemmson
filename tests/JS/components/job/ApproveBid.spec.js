import {
    shallowMount
} from "@vue/test-utils";
import sinon from 'sinon';
import ApproveBid from '../../../../resources/assets/js/components/job/ApproveBid';

require('../../bootstrap');

describe('ApproveBid', () => {
    const declineBid = sinon.spy();
    const openModal = sinon.spy();
    const wrapper = shallowMount(ApproveBid, {
        methods: {
            initAutocomplete() {
                return true;
            },
            openModal,
            declineBid
        },
        stubs: [
            'general-contractor-bid-actions',
            'modal'
        ],
        propsData: {
            isCustomer: false,
            customerName: 'Jane Doe',
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: '2018-08-01 10:58:37',
                status: 'bid.initiated',
                contractor_id: 1,
                bid_price: 99.00,
                declined_message: null,
                location: null
            }
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });

    it('Should not show the address section by default', () => {
        const addressSection = wrapper.find({
            ref: 'address-section'
        });
        expect(addressSection.isVisible()).toBe(false);
    });

    it('Should show the address section after clicking the checkbox job_location_same_as_home', () => {
        const checkbox = wrapper.find('#job_location_same_as_home');
        checkbox.trigger('click');
        const addressSection = wrapper.find({
            ref: 'address-section'
        });
        expect(addressSection.isVisible()).toBe(true);
    });

    it('Should not render the decline form', () => {
        const declineForm = wrapper.find({
            ref: 'decline-form'
        });
        expect(declineForm.exists()).toBe(false);
    });

    it('Should render the decline form', () => {
        const decline = wrapper.find({
            ref: 'decline'
        });
        decline.trigger('click');
        const declineForm = wrapper.find({
            ref: 'decline-form'
        });
            
        expect(declineForm.exists()).toBe(true);
    });

    it('Should have tried to open the cancel modal form', () => {
        const cancel = wrapper.find({
            ref: 'cancel'
        });
        cancel.trigger('click');
        expect(openModal.calledOnce).toBe(true);
    });

    it.only('Should have tried to open the approve modal', () => {
        const approve = wrapper.find({
            ref: 'approve'
        });
        approve.trigger('click');
        expect(openModal.calledTwice).toBe(true);
    });

    it('Should try and send a decline bid action', () => {
        const decline = wrapper.find({
            ref: 'declineBid'
        });
        decline.trigger('click');

        expect(declineBid.calledOnce).toBe(true);
    });

});