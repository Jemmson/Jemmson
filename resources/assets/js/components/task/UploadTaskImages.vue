<template>
    <div class="row">
        <div class="col-4" v-if="showTaskImage1(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 1].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="jobTask ? 'image' + jobTask.images[jobTask.images.length - 1].id : ''">
                <img :src="jobTask.images[jobTask.images.length - 1].url"
                     :id="'image-img' + jobTask.images[jobTask.images.length - 1].id">
                <a class="lightbox-close" :id="jobTask ? 'image-close' + jobTask.images[jobTask.images.length - 1].id : ''"
                   @click.prevent="closeImage(jobTask.images[jobTask.images.length - 1].id)"></a>
            </a>
        </div>

        <div class="col-4" v-if="showTaskImage2(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 2].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="jobTask ? 'image' + jobTask.images[jobTask.images.length - 2].id : ''">
                <img :src="jobTask.images[jobTask.images.length - 2].url"
                     :id="jobTask ? 'image-img' + jobTask.images[jobTask.images.length - 2].id : ''">
                <a class="lightbox-close" :id="jobTask ? 'image-close' + jobTask.images[jobTask.images.length - 2].id : ''"
                   @click.prevent="closeImage(jobTask.images[jobTask.images.length - 2].id)"></a>
            </a>
        </div>

        <div class="col-4" v-if="showMoreImagesBtn(jobTask)">
            <a class="lightbox" :href="'/#/task/' + jobTask.id + '/images'">
                <img :src="'/img/more.png'" alt="">
            </a>
        </div>
        <input :ref="jobTask ? 'task_photo_' + jobTask.id : ''"
               :disabled="loading ? 'disabled' : false"
               class="btn btn-normal ml-2 mt-4"
               style="width: 95%"
               :id="jobTask ? 'task_photo_' + jobTask.id : ''"
               type="file" @change="uploadTaskImage(jobTask.id)">
        <v-progress-linear
                :active="loading"
                :indeterminate="loading"
                absolute
                bottom
                color="deep-purple accent-4"
        ></v-progress-linear>

    </div>
</template>

<script>
    import { mapState } from 'vuex'
  export default {
    name: 'UploadTaskImages',
    props: {
      jobTask: Object,
      type: String
    },
    data() {
      return {
        disabled: {
          uploadTaskImageBtn: false
        },
        loading: false
      }
    },
    computed: {
      ...mapState({

      }),
      closeLink() {
        if (this.type === 'sub') {
          return '#/tasks'
        }

        return '#/bid/' + this.jobTask.job_id
      }
    },
    methods: {
      triggerFileInput(id) {
        console.log('trigger file', id)

        $('#task_photo_' + id).click()
      },
      openImage(imageId) {
        console.log(imageId)
        $('#image' + imageId).addClass('lightbox-open')
        $('#image-img' + imageId).addClass('lightbox-open-image')
        $('#image-close' + imageId).addClass('lightbox-open-close')

      },
      closeImage(imageId) {
        console.log(imageId)
        $('#image' + imageId).removeClass('lightbox-open')
        $('#image-img' + imageId).removeClass('lightbox-open-image')
        $('#image-close' + imageId).removeClass('lightbox-open-close')

      },
      showTaskImage1(jobTask) {
        // first most recent
        if (jobTask) {
          const length = jobTask.images.length
          return length > 0 && jobTask.images[length - 1] !== undefined
        }
      },
      showTaskImage2(jobTask) {
        // second most recent
        if (jobTask) {
          const length = jobTask.images.length
          return length > 1 && jobTask.images[length - 2] !== undefined
        }

      },
      showMoreImagesBtn(jobTask) {
        if (jobTask) {
          return jobTask.images.length > 0
        }
      },
      async uploadTaskImage(jobTaskId) {
        if (this.jobTask && this.jobTask.job) {
          this.loading = true
          const data = new FormData()
          console.log(this.$refs['task_photo_' + jobTaskId])
          data.append('photo', this.$refs['task_photo_' + jobTaskId].files[0])
          data.append('jobTaskId', jobTaskId)

          this.jobTask.job_id ? data.append('jobId', this.jobTask.job_id) : data.append('jobId', this.jobTask.job.id)
          const disabled = await User.uploadTaskImage(data, this.disabled)
          this.loading = disabled;
        }
      },
    }
  }
</script>

<style lang="less" scoped>
    .file {
        position: fixed;
        right: 100%;
        bottom: 100%;
    }
</style>
