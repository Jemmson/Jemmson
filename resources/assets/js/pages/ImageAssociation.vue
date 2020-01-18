<template>
    <div
            v-if="isACustomer !== 'customer'"
    >

        <div
                v-for="image in images" :key="image.id"
        >
            <image-task
                    :image="image"
                    :tasks="tasks"
                    @clearTask="clearTask(image.id, $event)"
                    @clearAllTasks="clearAllTasks(image.id)"
                    @associate="associateImageToTask(image.id, $event)"
            ></image-task>
        </div>

        <v-card class="margins">
            <v-card-title>Associate Images</v-card-title>
            <v-card-actions>
                <v-btn
                        :disabled="!imagesHaveBeenAssociated()"
                        label="Submit"
                        color="primary"
                        @click="submitImageTasks()"
                >Submit
                </v-btn>
            </v-card-actions>
        </v-card>

    </div>
</template>

<script>

  import ImageTask from '../components/task/ImageTask'

  export default {
    name: 'ImageAssociation',
    components: {
      ImageTask
    },
    data() {
      return {
        isACustomer: Spark.state.user.usertype,
        images: [],
        tasks: [],
        imageTasks: []
      }
    },
    props: {
      isCustomer: {
        type: Boolean
      },
      jobId: {
        type: Number
      }
    },
    methods: {

      clearTask(imageId, jobTaskId){
        for (let i = 0; i < this.imageTasks.length; i++) {
          if (this.imageTasks[i].image_id === imageId && this.imageTasks[i].jobTaskId === jobTaskId) {
            this.imageTasks.splice(i, 1);
          }
        }
      },

      clearAllTasks(imageId){
        while(this.clearTaskImage(imageId)){}
      },

      clearTaskImage(imageId){
        for (let i = 0; i < this.imageTasks.length; i++) {
          if (this.imageTasks[i].image_id === imageId) {
            this.imageTasks.splice(i, 1);
            return true
          }
        }
        return false
      },

      imagesHaveBeenAssociated(){
        return this.imageTasks.length > 0;
      },

      associateImageToTask(imageId, jobTaskId) {

        let exists = false;

        for (let i = 0; i < this.imageTasks.length; i++) {
          if(this.imageTasks[i].image_id === imageId){
            this.imageTasks[i].jobTaskId = jobTaskId
            exists = true;
          }
        }

        if (!exists) {
          this.addImageTask(imageId, jobTaskId)
        }


        // if (this.thereAreNoImageTasks()) {
        //   this.addImageTask(imageId, jobTaskId)
        // } else if (!this.duplicateExists(imageId, jobTaskId)) {
        //   this.addImageTask(imageId, jobTaskId)
        // }
      },

      duplicateExists(imageId, jobTaskId) {
        for (let i = 0; i < this.imageTasks.length; i++) {
          if (this.imageTasks[i].image_id === imageId && this.imageTasks[i].jobTaskId === jobTaskId) {
            return true
          }
        }
        return false
      },

      thereAreNoImageTasks() {
        return this.imageTasks.length === 0
      },

      addImageTask(imageId, jobTaskId) {
        this.imageTasks.push({image_id: imageId, jobTaskId: jobTaskId})
      },

      async submitImageTasks() {
        try {
          const data = await axios.post('/associateImagesToTasks', {
            imageTasks: this.imageTasks
          })
          this.$router.push('/bid/' + this.jobId)
        } catch (error) {
          console.log(error)
        }
      },

      async getImagesNotAssociatedToATask() {
        try {
          const data = await axios.get('/getImagesNotAssociatedToATask/' + this.jobId)
          this.images = data.data
          console.log('getImagesNotAssociatedToATask', data.data)
        } catch (error) {
          console.log(error)
        }
      },

      async getAllTaskIdsForJob() {
        try {
          const data = await axios.get('/getAllTaskIdsForJob/' + this.jobId)
          this.tasks = data.data
          console.log('getAllTaskIdsForJob', data.data)
        } catch (error) {
          console.log(error)
        }
      }
    },
    mounted() {
      if (this.isACustomer === 'customer') {
        this.$router.push('/home')
      } else if (!this.jobId) {
        this.$router.push('/bids')
      } else {
        this.$store.commit('setCurrentPage', this.$router.history.current.path)
        this.getAllTaskIdsForJob()
        this.getImagesNotAssociatedToATask()
      }
    }
  }
</script>

<style scoped>

</style>