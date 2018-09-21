import { shallowMount } from "@vue/test-utils";
import CompletedTasks from '../../../resources/assets/js/components/job/CompletedTasks';
import sinon from 'sinon';

require('../bootstrap');

describe('Completed Tasks', () => {
    const reopenTask = sinon.spy();
    const wrapper = shallowMount(CompletedTasks, {
        methods: {
            reopenTask
        },
        stubs: [
            'card',
            'deny-task-modal'
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
                location: null,
            }
        }
    });

    it('Should not render itself', () => {
        expect(wrapper.html()).toBe(undefined);
    });

    it('Should render itself', () => {
        wrapper.setProps({
            bid: {
                job_tasks: [{
                    "id": 1,
                    "job_id": 2,
                    "task_id": 2,
                    "bid_id": null,
                    "location_id": null,
                    "contractor_id": 2,
                    "status": "bid_task.approved_by_general",
                    "cust_final_price": 100,
                    "sub_final_price": 90,
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
                    "location": null
                }, {
                    "id": 2,
                    "job_id": 2,
                    "task_id": 2,
                    "bid_id": null,
                    "location_id": null,
                    "contractor_id": 2,
                    "status": "bid_task.finished_by_general",
                    "cust_final_price": 40,
                    "sub_final_price": 20,
                    "start_when_accepted": 0,
                    "stripe": 0,
                    "start_date": "2018-08-29 11:35:38",
                    "deleted_at": null,
                    "created_at": "2018-08-29 11:35:38",
                    "updated_at": "2018-08-29 12:48:56",
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
                    "location": null
                }]
            }
        });
        expect(wrapper.html()).not.toBe(undefined);
    });

    it('Should not render exclude from payment - contractor', () => {
        expect(wrapper.html()).not.toContain('Exclude From Payment');
    });

    it('Should have two payable jobs', () => {
        expect(wrapper.vm.payableTasks.length).toBe(2);
    });

    it('Should render Job 1', () => {
        expect(wrapper.html()).toContain('Job 1');
    });

    it('Should render Job 2', () => {
        expect(wrapper.html()).toContain('Job 2');
    });

    it('Should not render exclude payment option 1 - contractor', () => {
        const exclude1 = wrapper.find('#exclude-1');
        expect(exclude1.exists()).toBe(false);
    });

    it('Should not render exclude payment option 2 - contractor', () => {
        const exclude2 = wrapper.find('#exclude-2');
        expect(exclude2.exists()).toBe(false);
    });

    it('Should render reopen buttons - contractor', () => {
        expect(wrapper.html()).toContain('Reopen');
    });

    it('Should not render deny buttons when tasks are finished - contractor', () => {
        expect(wrapper.html()).not.toContain('Deny');
    });

    it('Should not render stripe cash button - contractor', () => {
        expect(wrapper.html()).not.toContain('Paid With Cash');
    });

    it('Should not render stripe button - contractor', () => {
        expect(wrapper.html()).not.toContain('Pay With Stripe');
    });

    it('Should not render deny-task-modal - contractor', () => {
        const denyModal = wrapper.find('deny-task-modal-stub');
        expect(denyModal.exists()).toBe(false);
    });

    it('Should have fired the reopen fn when the reopen btn was clicked', () => {
        const reopenBtn = wrapper.find('button');
        reopenBtn.trigger('click');
        expect(reopenTask.calledOnce).toBe(true);
    });

    it('Should not render the deny-task-modal - contractor', () => {
        expect(wrapper.html()).not.toContain('deny-task-modal-stub');
    });

    it('Should render slot content', () => {
        // TODO: we can mock slot content, but I haven't found a way to render the 
        // default content
        expect(false).toBe(true);
    });
});