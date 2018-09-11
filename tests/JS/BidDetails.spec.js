import {
    shallowMount
} from "@vue/test-utils";
import BidDetails from '../../resources/assets/js/components/job/BidDetails';

require('./bootstrap');

describe('Card', () => {

    const wrapper = shallowMount(BidDetails, {
        propsData: {
            isCustomer: false,
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: null,
                status: 'bid.initiated',
                contractor_id: 1,
            }
        }
    });

    it('Should render Bid Initiated', () => {
        expect(wrapper.html()).toContain('Bid Initiated');
    });

    it('Should render Pool Job', () => {
        expect(wrapper.html()).toContain('Pool Job');
    });

    it('Should render customer name', () => {
        expect(true).toBe(false);
    });

    it('Should render agreed date', () => {
        expect(true).toBe(false);
    });

    it('Should not render location', () => {
        expect(true).toBe(false);
    });

    it('Should render bid price', () => {
        expect(true).toBe(false);
    });

    it('Should not render declined message', () => {
        expect(true).toBe(false);
    });
});