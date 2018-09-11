import {
    mount,
    shallowMount,
    createLocalVue
}
from '@vue/test-utils';
import sinon from 'sinon';

const $route = {
    path: '/#/bid/1',
    query: {
        success: 1,
        error: 0
    },
    params: {
        id: 1
    }
};

require('./bootstrap');

import Job from '../../resources/assets/js/pages/Job.vue';



describe('Job', () => {
    const wrapper = shallowMount(Job, {
        mocks: {
            $route
        },
        stubs: [
            'bid-details',
            'approve-bid',
            'general-contractor-bid-actions',
            'completed-tasks',
            'bid-tasks',
            'bid-add-task',
            'stripe',
            'card',
        ],
        methods: {
            getBid (id) {
                console.log('get bid ' + id);
                
            }
        },
        propsData: {
            user: {
                usertype: 'contractor',
                contractor: {
                    company_name: 'KPS Pools'
                }
            }
        }
    });

    it('Should Render bid-details', () => {
        expect(wrapper.find('bid-details-stub').exists()).toBe(true);
    });

});