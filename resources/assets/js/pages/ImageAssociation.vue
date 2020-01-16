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
                @associate="associateImageToTask(image.id, $event)"
            ></image-task>
        </div>

    </div>
</template>

<script>

    import ImageTask from "../components/task/ImageTask"

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

      associateImageToTask (imageId, jobTaskId) {

        for (let i = 0; i < this.imageTasks.length; i++) {
          this.imageTasks.push({image_id: imageId, jobTaskId: jobTaskId})
        }

      },

      async submitImageTasks () {
          try {
              const data = await axios.get ('/associateImageToTask' + this.imageTasks);
          } catch (error) {
              console.log(error);
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