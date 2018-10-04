import {
    mount,
    createLocalVue
} from "@vue/test-utils";
import VuePaginate from 'vue-paginate';
import BidTasks from '../../../../resources/assets/js/components/job/BidTasks';

require('../../bootstrap');

const localVue = createLocalVue();
localVue.use(VuePaginate);

describe('BidTasks', () => {
    const wrapper = mount(BidTasks, {
        localVue,
        stubs: [
            'card',
            'sub-invite-modal',
            'deny-task-modal',
            'update-task-location-modal',
            'task-images'
        ],
        propsData: {
            bid: {
                job_name: 'Pool Job',
                agreed_start_date: '2018-08-01 10:58:37',
                status: 'job.approved',
                contractor_id: 1,
                bid_price: 99.00,
                declined_message: null,
                location: null,
                job_tasks: [{
                    "id": 2,
                    "job_id": 2,
                    "task_id": 2,
                    "bid_id": null,
                    "location_id": null,
                    contractor_id: 1,
                    "status": "bid_task.customer_sent_payment",
                    "cust_final_price": 100,
                    "sub_final_price": 29,
                    "start_when_accepted": 0,
                    "stripe": 0,
                    "start_date": "2018-08-29 10:47:59",
                    "deleted_at": null,
                    "created_at": "2018-08-29 10:47:59",
                    "updated_at": "2018-08-29 12:57:37",
                    "stripe_transfer_id": "job.2.2.cash",
                    "customer_message": "wef",
                    "sub_message": null,
                    "qty": 1,
                    "sub_sets_own_price_for_job": 1,
                    "declined_message": null,
                    "unit_price": 0,
                    "task": {
                        "id": 2,
                        "name": "Job 1",
                        "standard_task_id": 3,
                        "contractor_id": 2,
                        "proposed_cust_price": 61,
                        "average_cust_price": 55,
                        "proposed_sub_price": 73,
                        "average_sub_price": 19,
                        "created_at": "2018-08-29 10:47:59",
                        "updated_at": "2018-08-29 10:47:59",
                        "qtyUnit": null,
                        "sub_instructions": null,
                        "customer_instructions": null
                    },
                    "bid_contractor_job_tasks": [{
                        "id": 2,
                        "contractor_id": 1,
                        "job_task_id": 2,
                        "bid_price": 64,
                        "created_at": null,
                        "updated_at": null,
                        "contractor": {
                            "id": 1,
                            "location_id": null,
                            "name": "Shawn Pike",
                            "email": "shawn@example.com",
                            "usertype": "contractor",
                            "password_updated": 1,
                            "photo_url": "https://www.gravatar.com/avatar/0e9ed09e0f0c825f87e73c596ec5032b.jpg?s=200&d=mm",
                            "logo_url": null,
                            "uses_two_factor_auth": false,
                            "phone": "4901112222",
                            "two_factor_reset_code": null,
                            "current_team_id": null,
                            "stripe_id": null,
                            "current_billing_plan": null,
                            "billing_state": null,
                            "trial_ends_at": null,
                            "last_read_announcements_at": null,
                            "created_at": "2018-08-29 10:47:58",
                            "updated_at": "2018-08-29 10:47:58",
                            "tax_rate": 0
                        }
                    }],
                    "location": null,
                    "images": [{
                        "id": 1,
                        "job_id": 2,
                        "job_task_id": 2,
                        "url": "http://127.0.0.1:8000/storage/tasks/KTIpqlJHtxRdZJggHy1OUAhWKjYaaItOkjLiDwqL.jpeg",
                        "created_at": "2018-08-29 11:01:42",
                        "updated_at": "2018-08-29 11:01:42"
                    }, {
                        "id": 2,
                        "job_id": 2,
                        "job_task_id": 2,
                        "url": "http://127.0.0.1:8000/storage/tasks/9q82Lbk14tocjHPPz8oS7xyy30jRFgIIrjPsHZiA.jpeg",
                        "created_at": "2018-08-29 11:01:48",
                        "updated_at": "2018-08-29 11:01:48"
                    }, {
                        "id": 3,
                        "job_id": 2,
                        "job_task_id": 2,
                        "url": "http://127.0.0.1:8000/storage/tasks/EcM95wNWPnaC6HiANopyJHsGB0wbWzugn9EWAsBG.jpeg",
                        "created_at": "2018-08-29 11:01:54",
                        "updated_at": "2018-08-29 11:01:54"
                    }]
                }, {
                    "id": 3,
                    "job_id": 2,
                    "task_id": 2,
                    "bid_id": null,
                    "location_id": null,
                    contractor_id: 1,
                    "status": "bid_task.finished_by_general",
                    "cust_final_price": 40,
                    "sub_final_price": 0,
                    "start_when_accepted": 0,
                    "stripe": 0,
                    "start_date": "2018-08-29 11:35:38",
                    "deleted_at": null,
                    "created_at": "2018-08-29 11:35:38",
                    "updated_at": "2018-09-17 12:12:25",
                    "stripe_transfer_id": null,
                    "customer_message": "",
                    "sub_message": "",
                    "qty": 1,
                    "sub_sets_own_price_for_job": 1,
                    "declined_message": null,
                    "unit_price": 40,
                    "task": {
                        "id": 2,
                        "name": "Job 2",
                        "standard_task_id": 3,
                        "contractor_id": 2,
                        "proposed_cust_price": 61,
                        "average_cust_price": 55,
                        "proposed_sub_price": 73,
                        "average_sub_price": 19,
                        "created_at": "2018-08-29 10:47:59",
                        "updated_at": "2018-08-29 10:47:59",
                        "qtyUnit": null,
                        "sub_instructions": null,
                        "customer_instructions": null
                    },
                    "bid_contractor_job_tasks": [],
                    "location": null,
                    "images": []
                }]
            }
        }
    });

    it('Should render itself ', () => {
        expect(wrapper.vm.show).toBe(true);
    });

    it('User should be a contractor ', () => {
        expect(wrapper.vm.isContractor).toBe(true);
    });

    it('Should render 2 cards', () => {
        const cards = wrapper.findAll('card-stub');
        expect(cards.length).toBe(2);
    });

    it('Should have a status-grey class', () => {
        const status = wrapper.findAll('.status-grey');
        expect(status.length).toBe(1);
    });

    it('Should have a status-green class', () => {
        const status = wrapper.findAll('.status-green');
        expect(status.length).toBe(1);
    });

    it('Should render the text Customer Has Sent A Payment', () => {
        expect(wrapper.html()).toContain('Customer Has Sent A Payment');
    });

    it('Should render the text Waiting On Customer Approval & Payment', () => {
        expect(wrapper.html()).toContain('Waiting On Customer Approval &amp; Payment');
    });

    it('Should render the text Job 1', () => {
        expect(wrapper.html()).toContain('Job 1');
    });

    it('Should render the text Job 2', () => {
        expect(wrapper.html()).toContain('Job 2');
    });

    it('Should render the text $100', () => {
        expect(wrapper.html()).toContain('$100');
    });

    it('Should render the Total Task Sub Price section - contractor', () => {
        expect(wrapper.html()).toContain('Total Task Sub Price');
    });

    it('Should render the Quantity: section - contractor', () => {
        expect(wrapper.html()).toContain('Quantity:');
    });

    it('The quantity input should be disabled - contractor', () => {
        const qty = wrapper.find({
            ref: 'quantity',
        })
        
        expect(qty.attributes().disabled).toBe('disabled');
    });

    it('The price input should be disabled - contractor', () => {
        const qty = wrapper.find({
            ref: 'price',
        })

        expect(qty.attributes().disabled).toBe('disabled');
    });

    it('Should render the text Change Task Location', () => {
        expect(wrapper.html()).toContain('Change Task Location');
    });

    it('Should render the text Task Start Date - contractor', () => {
        expect(wrapper.html()).toContain('Task Start Date');
    });

    it('Should render 2 task-images component, one for each task', () => {
        const taskImages = wrapper.findAll('task-images-stub');
        expect(taskImages.length).toBe(2);
    });

    it('Messages should not be disabled', () => {
        expect(wrapper.vm.disableMessages).toBe(false);
    });

    it('Should not render the subs panel', () => {
        expect(wrapper.vm.showSubsPanel).toBe(false);
    });

    it('Should render footer slot', () => {
        expect(false).toBe(true);
    });

});