import {
    shallowMount
} from "@vue/test-utils";
import BidDetails from '../../../../resources/assets/js/components/job/BidDetails';

require('../../bootstrap');

describe('BidDetails', () => {

    // const wrapper = shallowMount(BidDetails, {
    //     propsData: {
    //         isCustomer: false,
    //         customerName: 'Jane Doe',
    //         bid: {
    //             job_name: 'Pool Job',
    //             agreed_start_date: '2018-08-01 10:58:37',
    //             status: 'bid.initiated',
    //             contractor_id: 1,
    //             bid_price: 99.00,
    //             declined_message: null,
    //             location: null
    //         }
    //     }
    // });
    //
    // it('Should render Bid Initiated', () => {
    //     expect(wrapper.html()).toContain('Bid Initiated');
    // });
    //
    // it('Should render Pool Job', () => {
    //     expect(wrapper.html()).toContain('Pool Job');
    // });
    //
    // it('Should render customer name', () => {
    //     expect(wrapper.html()).toContain('Jane Doe');
    // });
    //
    // it('Should render agreed date', () => {
    //     expect(wrapper.html()).toContain('08/01/2018');
    // });
    //
    // it('Should not render location', () => {
    //     expect(wrapper.html()).toContain('No Address is Set Yet');
    // });
    //
    // it('Should render location', () => {
    //     wrapper.setProps({
    //         bid: {
    //             job_name: 'Pool Job',
    //             agreed_start_date: '2018-08-01 10:58:37',
    //             status: 'bid.initiated',
    //             contractor_id: 1,
    //             bid_price: 99.00,
    //             declined_message: null,
    //             location_id: 1,
    //             location: {}
    //
    //         }
    //     });
    //     expect(wrapper.vm.showAddress).toBe(true);
    // });
    //
    // it('Should render bid price', () => {
    //     expect(wrapper.html()).toContain('$99');
    // });
    //
    // it('Should not render declined message', () => {
    //     expect(wrapper.vm.showDeclinedMessage).toBe(false);
    // });
    //
    // it('Should render declined message', () => {
    //     wrapper.setProps({
    //         bid: {
    //             job_name: 'Pool Job',
    //             agreed_start_date: '2018-08-01 10:58:37',
    //             status: 'bid.declined',
    //             contractor_id: 1,
    //             bid_price: 99.00,
    //             declined_message: "",
    //             location: null
    //         }
    //     });
    //     expect(wrapper.vm.showDeclinedMessage).toBe(true);
    // });

    
});