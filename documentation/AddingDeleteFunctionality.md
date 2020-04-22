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
          const data = await axios.post('/jobTask/delete', {
            id: id
          })
          this.getBid(this.job_task.job.id)
        } catch (error) {
          console.log(error)
        }
      },
      
7. add getBid to the methods

      async getBid(id) {
        try {
          const {
            data
          } = await axios.get('/job/' + id)
          if (data[0]) {
            this.bid = data[0]
            this.$store.commit('setJob', data[0])
          } else {
            this.bid = data
            this.$store.commit('setJob', data)
          }
          this.$store.commit('setJob', data)
        } catch (error) {
          console.log(error)
          if (
            error.message === 'Not Authorized to access this resource/api' ||
            error.response !== undefined && error.response.status === 403
          ) {
            this.$router.push('/bids')
          }
          Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
        }
      },

    