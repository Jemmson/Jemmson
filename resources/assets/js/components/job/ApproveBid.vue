<template>
    <!-- /customer approve bid form -->
    <form role="form">
        <div class="form-group col-sm-12 col-md-6" :class="{'has-error': bidForm.errors.has('agreed_start_date')}">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" v-model="bidForm.agreed_start_date">
            <span class="help-block" v-show="bidForm.errors.has('agreed_start_date')">
                {{ bidForm.errors.get('agreed_start_date') }}
            </span>
        </div>
        <div class="form-group col-md-12">
            <button class="btn btn-success" @click.prevent="approve" :disabled="disabled.approve">
                <span v-if="disabled.approve">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Approve
            </button>
            <button class="btn btn-danger" @click.prevent="declineBid" :disabled="disabled.declineBid">
                <span v-if="disabled.declineBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Decline
            </button>
        </div>
    </form>
</template>

<script>
    export default {
        props: {
            bid: Object,
        },
        data() {
            return {
                taskIndex: 0,
                bidForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
                user: '',
                disabled: {
                    approve: false,
                    declineBid: false
                },
            }
        },
        methods: {
            approve() {
                Customer.approveBid(this.bidForm, this.disabled);
            },
        },
        mounted: function () {
            // set up init data
            this.bidForm.id = this.bid.id;
            this.bidForm.status = this.bid.status;
            this.user = Spark.state.user;
        }
    }
</script>