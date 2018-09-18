import {
    shallowMount
} from "@vue/test-utils";
import BidAddTask from '../../resources/assets/js/components/task/BidAddTask';

require('./bootstrap');

describe('Completed Tasks', () => {
    const wrapper = shallowMount(BidAddTask, {
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

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});