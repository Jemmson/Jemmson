<template>
    <div id="add-task" class="panel panel-default" v-if="show">
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
                    <!--<pre>{{ addNewTaskForm.start_date }}</pre>-->
                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                        <label for="taskName">Task Name</label>
                        <input type="text" class="form-control" id="taskName" name="taskName" v-model="addNewTaskForm.taskName" v-on:keyup="getExistingTask">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('taskName')">
                            {{ addNewTaskForm.errors.get('taskName') }}
                        </span>
                        <div class="panel-footer" v-if="taskResults.length">
                            <ul class="list-group">
                                <button class="list-group-item" v-for="result in taskResults" v-bind:key="result.id" @click.prevent="fillTaskValues(result)">
                                    {{ result.name }}
                                </button>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('start_when_accepted')}">
                        <label for="start_when_accepted">Start When Job Is Accepted</label>
                        <input type="checkbox" class="form-control" id="start_when_accepted" name="start_when_accepted" required v-model="addNewTaskForm.start_when_accepted">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('start_when_accepted')">
                            {{ addNewTaskForm.errors.get('start_when_accepted') }}
                        </span>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('start_date')}" v-if="!addNewTaskForm.start_when_accepted">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required v-model="addNewTaskForm.start_date">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">
                            {{ addNewTaskForm.errors.get('start_date') }}
                        </span>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                        <label for="custTaskPrice">Customer Task Price</label>
                        <input type="text" class="form-control" id="custTaskPrice" name="taskPrice" v-model="addNewTaskForm.taskPrice" @blur="formatPrice('taskPrice')">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                            {{ addNewTaskForm.errors.get('taskPrice') }}
                        </span>
                    </div>

                    <div class="form-group col-md-6" :class="{'has-error': addNewTaskForm.errors.has('subTaskPrice')}">
                        <label for="subTaskPrice">Sub Task Price</label>
                        <input type="text" class="form-control" id="subTaskPrice" name="subTaskPrice" v-model="addNewTaskForm.subTaskPrice" @blur="formatPrice('subTaskPrice')">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                            {{ addNewTaskForm.errors.get('subTaskPrice') }}
                        </span>
                    </div>

                    <div class="form-group col-md-12" :class="{'has-error': addNewTaskForm.errors.has('details')}">
                        <label for="details">Details</label>
                        <input type="text" class="form-control" id="details" name="details" v-model="addNewTaskForm.details">
                        <span class="help-block" v-show="addNewTaskForm.errors.has('details')">
                            {{ addNewTaskForm.errors.get('details') }}
                        </span>
                    </div>

                    <div class=" col-md-12">
                        <button
                                id="addTaskToInvoice"
                                name="addTaskToInvoice"
                                class="btn btn-default btn-success"
                                @click.prevent="addNewTaskToBid()">
                            Add Task
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
                    jobId: this.bid.id,
                    subTaskPrice: '',
                    taskPrice: '',
                    taskName: '',
                    contractorId: '',
                    area: this.bid.city,
                    start_date: '',
                    start_when_accepted: true,
                    details: '',
                }),
                taskResults: [],
            }
        },
        methods: {
            formatPrice(price) {
                Format.addDollarSign(this.addNewTaskForm, price);
            },
            getExistingTask() {
                this.taskResults = [];
                if (this.addNewTaskForm.taskName.length > 2) {
                    axios.post('/api/search/task', {
                        taskname: this.addNewTaskForm.taskName,
                        jobId: this.addNewTaskForm.jobId
                    }).then(response => {
                        console.log(response.data)
                        this.taskResults = response.data
                    })
                }
            },
            filterReturnedTasks(responseData, allTasks) {
                let responseDataLength = responseData.length
                let allTasksDataLength = allTasks.length
                let newTasks = []

                for (let i = 0; i < responseDataLength; i++) {
                    let flag = false
                    for (let j = 0; j < allTasksDataLength; j++) {
                        if (responseData[i].id === allTasks[j].id) {
                            flag = true
                        }
                    }
                    // debugger
                    if (flag === false) {
                        newTasks.push(responseData[i])
                    }
                }
                return newTasks
            },
            fillTaskValues(result) {
                console.log(result)
                this.taskExists = true
                this.addNewTaskForm.taskName = result.name;
                this.addNewTaskForm.taskPrice = result.proposed_cust_price;
                this.addNewTaskForm.subTaskPrice = result.proposed_sub_price;
                this.formatPrice('taskPrice');
                this.formatPrice('subTaskPrice');
                this.clearTaskResults()
            },
            clearTaskResults() {
                this.taskResults = [];
            },
            addNewTaskToBid() {
                GeneralContractor.addNewTaskToBid(this.bid, this.addNewTaskForm);
            }
        },
        mounted: function () {}
    }
</script>