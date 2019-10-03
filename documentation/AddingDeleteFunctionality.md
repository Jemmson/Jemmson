###Adding Delete Button

Client Side

1. Put this where the button goes
    <button class="btn btn-normal btn-sm w-full mr-1rem" @click="showDeleteTaskModal(jTask)">DELETE
    </button>

2. Add the modal at the bottom of the page

    <delete-task-modal
                    @action="deleteTheTask($event)"
            >
   </delete-task-modal>

3. Import the modal

      import DeleteTaskModal from '../components/job/DeleteTaskModal'

4. Add the modal to the components
    
   components: {
         BidTask,
         DeleteTaskModal
       },
       
5. add the data fields
    
       disabled: {
         deleteTask: false
       },
       deleteTask: {
         id: ''
       }

6. Add the methods

    showDeleteTaskModal(job_task) {
        this.deleteTask.id = job_task.id
        this.jobTask = job_task
        $('#delete-task-modal').modal('show')
      },
      deleteTheTask(action) {
        if (action === 'delete') {
          this.deleteTheActualTask(this.deleteTask.id)
        }
        $('#delete-task-modal').modal('hide')
      },
      async deleteTheActualTask(id) {
        try {
          const data = await axios.post('/jobTask/delete/', {
            id: id
          })
          this.getBid(this.job_task.job.id)
        } catch (error) {
          console.log('error')
        }
      },
    