<template>
    <!-- Modal -->
    <div class="modal h-100 modal-background-gray" :id="'deny-task-modal_'+ getJobTaskId()" tabindex="-1" role="dialog"
         aria-labelledby="deny-task-modal"
         aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header flex flex-col">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4
                            class="modal-title">
                        Deny Approval - {{ jobTask.task === undefined ? '' : jobTask.task.name.toUpperCase() }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" class="form-control" rows="4" placeholder="Optional"
                                      v-model="denyForm.message">
                                </textarea>
                        </div>
                    </form>
                    <!-- /end col-md-6 -->
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <v-btn class="w-full"
                               color="primary"
                               text
                               @click.prevent="denyTask"
                               :loading="disabled.deny"
                               ref="denyTaskBtn">
                            Deny Approval
                        </v-btn>
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
            id: Number
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
            getJobTaskId(){
              if (this.jobTask) {
                  return this.jobTask.id
              }
            },
            denyTask() {
                this.denyForm.user_id = User.getId()
                this.denyForm.job_task_id = this.jobTask.id
                Customer.denyTask(this.denyForm, this.disabled)
            },
        },
        mounted: function () {
            this.user = Spark.state.user
        }
    }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>