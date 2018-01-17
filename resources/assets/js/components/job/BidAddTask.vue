<template>
    <div class="panel panel-default" v-if="show">
        <!-- <div class="panel-heading">Dashboard</div> -->
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-12">
                    Add Task
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            task: Object,
            show: Boolean,
        },
        data() {
            return {
                addtaskForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
                userType: '',
            }
        },
        methods: {
            addTaskToBid() {
                this.$emit('addTaskToBid');
            }
        },
        mounted: function () {
            this.userType = Spark.state.user.usertype;
        }
    }
</script>