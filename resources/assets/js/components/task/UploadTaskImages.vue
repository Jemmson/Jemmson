<template>
    <div class="row">
        <div class="col-4" v-if="showTaskImage1(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 1].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="'image' + jobTask.images[jobTask.images.length - 1].id">
                <img :src="jobTask.images[jobTask.images.length - 1].url"
                     :id="'image-img' + jobTask.images[jobTask.images.length - 1].id">
                <a class="lightbox-close" :id="'image-close' + jobTask.images[jobTask.images.length - 1].id"
                   @click.prevent="closeImage(jobTask.images[jobTask.images.length - 1].id)"></a>
            </a>
        </div>

        <div class="col-4" v-if="showTaskImage2(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 2].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="'image' + jobTask.images[jobTask.images.length - 2].id">
                <img :src="jobTask.images[jobTask.images.length - 2].url"
                     :id="'image-img' + jobTask.images[jobTask.images.length - 2].id">
                <a class="lightbox-close" :id="'image-close' + jobTask.images[jobTask.images.length - 2].id"
                   @click.prevent="closeImage(jobTask.images[jobTask.images.length - 2].id)"></a>
            </a>
        </div>

        <div class="col-4" v-if="showMoreImagesBtn(jobTask)">
            <a class="lightbox" :href="'/#/task/' + jobTask.id + '/images'">
                <img :src="'/img/more.png'" alt="">
            </a>
        </div>
        <!-- / end task images preview -->

        <!-- upload images -->
        <!-- <button type="button" 
        :class="disabled.uploadTaskImageBtn ? 'btn btn-blue btn-upload disabled' : 'btn btn-blue btn-upload'">
            <span>Attach Images</span>
            <span v-show="disabled.uploadTaskImageBtn">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
            </span>
            <span v-if="!disabled.uploadTaskImageBtn"> -->
        <input :ref="'task_photo_' + jobTask.id" class="btn btn-normal ml-2 mt-4 w-inherit" :id="'task_photo_' + jobTask.id"
               type="file" @change="uploadTaskImage(jobTask.id)">
        <!-- </span>
    </button> -->
        <!-- / end upload task images-->
    </div>
</template>

<script>
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
        }
      }
    },
    computed: {
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
        if (jobTask.images) {
          const length = jobTask.images.length
          return length > 0 && jobTask.images[length - 1] !== undefined
        }
      },
      showTaskImage2(jobTask) {
        // second most recent
        if (jobTask.images) {
          const length = jobTask.images.length
          return length > 1 && jobTask.images[length - 2] !== undefined
        }

      },
      showMoreImagesBtn(jobTask) {
        if (jobTask.images) {
          return jobTask.images.length > 0
        }
      },
      uploadTaskImage(jobTaskId) {
        const data = new FormData()
        console.log(this.$refs['task_photo_' + jobTaskId])

        data.append('photo', this.$refs['task_photo_' + jobTaskId].files[0])
        data.append('jobTaskId', jobTaskId)
        data.append('jobId', this.jobTask.job_id)
        User.uploadTaskImage(data, this.disabled)
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
