<template>
    <div class="panel panel-default" v-if="show">
        <div class="panel-body">
            <form>
                <div class="row">
                    <!-- /form feedback, commented out since found good mobile first notification option -->
                    <!-- <div class="form-group col-md-12">
                      <transition name="slide-fade">
                        <div class="alert alert-success" v-if="addNewTaskForm.successful">
                          New Task Added!
                        </div>
                        <div class="alert alert-danger" v-if="addNewTaskForm.errors.has('error')">
                          {{ addNewTaskForm.errors.get('error') }}
                        </div>
                      </transition>
                    </div> -->
                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                        <label for="taskName">Task Name</label>
                        <input type="text" class="form-control" id="taskName" name="taskName" v-model="addNewTaskForm.taskName" v-on:keyup="getExistingTask">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('taskName')">
                            {{ addNewTaskForm.errors.get('taskName') }}
                        </span>
                        <div class="panel-footer" v-if="taskResults.length">
                            <ul class="list-group">
                                <button class="list-group-item" v-for="result in taskResults" :name="result.phone" @click="fillTaskPrice(result)">
                                    {{ result.name }}
                                </button>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="area">Locality</label>
                        <input type="text" class="form-control" id="area" name="area" required v-model="addNewTaskForm.area">
                    </div>

                    <div class="form-group col-md-12" :class="{'has-error': addNewTaskForm.errors.has('start_date')}">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required v-model="addNewTaskForm.start_date">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">
                            {{ addNewTaskForm.errors.get('start_date') }}
                        </span>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                        <label for="custTaskPrice">Customer Task Price</label>
                        <input type="text" class="form-control" id="custTaskPrice" name="taskPrice" v-model="addNewTaskForm.taskPrice">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                            {{ addNewTaskForm.errors.get('taskPrice') }}
                        </span>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('subTaskPrice')}">
                        <label for="subTaskPrice">Sub Task Price</label>
                        <input type="text" class="form-control" id="subTaskPrice" name="subTaskPrice" v-model="addNewTaskForm.subTaskPrice">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                            {{ addNewTaskForm.errors.get('subTaskPrice') }}
                        </span>
                    </div>
                    <div class=" col-md-12">
                        <button id="submitTask" class="btn btn-default btn-success" @click.prevent="addNewTaskToBid()">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            bid: Object,
            show: Boolean,
        },
        data() {
            return {
                addNewTaskForm: new SparkForm({
                    taskId: '',
                    taskExists: '',
                    jobId: '',
                    subTaskPrice: '',
                    taskPrice: '',
                    taskName: '',
                    contractorId: '',
                    area: '',
                    start_date: '',
                }),
                taskResults: [],
            }
        },
        methods: {
            getExistingTask() {

            },
            addNewTaskToBid() {
                GeneralContractor.addNewTaskToBid(this.bid, this.addNewTaskForm);
            }
        },
        mounted: function () {
        }
    }
</script>