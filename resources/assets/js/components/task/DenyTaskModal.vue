<template>
    <!-- Modal -->
    <div class="modal fade" id="deny-task-modal" tabindex="-1" role="dialog" aria-labelledby="deny-task-modal" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Deny Approval - {{ jobTask.task === undefined ? '' : jobTask.task.name.toUpperCase() }}</h4>
                </div>
                <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" class="form-control" rows="4" placeholder="Optional" v-model="denyForm.message">
                                </textarea>
                            </div>
                        </form>
                    <!-- /end col-md-6 -->
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button class="btn btn-danger" @click.prevent="denyTask" :disabled="disabled.deny">
                                        <span v-if="disabled.deny">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                          Deny Approval
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            jobTask: Object,
        },
        data() {
            return {
                denyForm: {
                    job_task_id: 0,
                    message: '',
                },
                user: '',
                disabled: {
                    deny: false,
                }
            }
        },
        methods: {
            denyTask() {
                this.denyForm.user_id = User.getId();
                this.denyForm.job_task_id = this.jobTask.id;
                Customer.denyTask(this.denyForm, this.disabled);
            },
        },
        mounted: function () {
            this.user = Spark.state.user;
        }
    }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>