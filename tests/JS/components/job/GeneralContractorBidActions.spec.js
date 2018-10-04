import {
    shallowMount
} from "@vue/test-utils";
import GeneralContractorBidActions from '../../../../resources/assets/js/components/job/GeneralContractorBidActions';

require('../../bootstrap');

describe('GeneralContractorBidActions', () => {
    const wrapper = shallowMount(GeneralContractorBidActions, {
        methods: {
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

});