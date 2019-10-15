Making dynamic spinners based upon an unknown number of tasks. I dont know how 
many tasks are going to come back from getBid.

1. I create a method that sets an index in the data object. 
    disabled: {
        spinner: []
    }

2. I mount the spinnerIndexes method
mounted: function() {
      this.setSpinnerIndexes()
    }
    
3. I fill in the spinner array with the objects that is the size of the task array
setSpinnerIndexes() {
        let spinner = []
        for (let i = 0; i < this.jobTasks.length; i++) {
          spinner.push({disabled: false})
        }
        this.disabled.spinner = spinner
        // Vue.set(this.disabled.spinner, spinner, false)
        // Vue.set(this.disabled, 'disable_' + i, false)
      }
      
4. the object in the data object looks like this
disabled: spinner: [
    {disabled: false},
    {disabled: false},
    {disabled: false}
    ]

the index of the object is the same as the index of the task in the tasks that are pulled down

5. I put the spinner html element in the HTML
<i v-if="checkSpinner(index)" class="fa fa-btn fa-spinner fa-spin"></i>

6. As the html is rendered the v-if is evaluated to true or false in the 
checkSpinner method
checkSpinner(index) {
        if (this.disabled.spinner[index]) {
          return this.disabled.spinner[index].disabled
        }
      }
      
7. when a user selects delete on the task a modal window shows up using the 
showDeleteTaskModal method

 showDeleteTaskModal(job_task, index) {
        this.disabled.spinner[index].disabled = true
        this.deleteTask.id = job_task.id
        this.jobTask = job_task
        $('#delete-task-modal').modal('show')
      },
      
  this changes the spinner to true and shows the spinner with the modal window up
  
8. if the user hits delete on the modal window then the deleteTheTask event is emitted
and called on the jobTasks componet.
     
