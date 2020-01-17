<template>
    <div class="row"
         style="margin-top: -1.25rem"
    >

        <div
            v-if="unassociatedImagesExist"
            style="margin: 0 auto 1rem auto"
        >
            <div
                    v-if="isGeneral()"
                    @click="goToImagesPage()"
            >
                <v-banner single-line>
                    <v-icon
                            slot="icon"
                            color="red"
                            size="36"
                    >
                        mdi-google-photos
                    </v-icon>
                    Associate Images With Tasks
                </v-banner>
            </div>
        </div>

        <div style="display:none">
            {{ this.getUnassociatedImagesExist() }}
        </div>

        <div class="col-4" v-if="jobImageExists(job, 1)">
            <a class="lightbox" @click.prevent="openImage(job, 1)">
                <img :src="getJobImage(job, 1).url" alt="">
            </a>

            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="job ? 'image' + getJobImage(job,1).id : ''">
                <img :src="getJobImage(job,1).url"
                     :id="'image-img' + getJobImage(job,1).id">
                <a class="lightbox-close" :id="job ? 'image-close' + getJobImage(job,1).id : ''"
                   @click.prevent="closeImage(getJobImage(job,1).id)"></a>
            </a>

        </div>

        <div class="col-4" v-if="jobImageExists(job, 2)">
            <a class="lightbox" @click.prevent="openImage(job, 2)">
                <img :src="getJobImage(job, 2).url" alt="">
            </a>

            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="job ? 'image' + getJobImage(job,2).id : ''">
                <img :src="getJobImage(job,2).url"
                     :id="'image-img' + getJobImage(job,2).id">
                <a class="lightbox-close" :id="job ? 'image-close' + getJobImage(job,2).id : ''"
                   @click.prevent="closeImage(getJobImage(job,2).id)"></a>
            </a>
        </div>

        <div class="col-4" v-if="showMoreImagesBtn(job)">
            <a class="lightbox" :href="'/#/job/' + job.id + '/images'">
                <img :src="'/img/more.png'" alt="">
            </a>
        </div>
        <input
                :ref="job ? 'task_photo_' + job.id : ''"
                :disabled="loading ? 'disabled' : false"
                class="btn btn-normal ml-2 mt-4"
                style="width: 95%"
                :id="job ? 'task_photo_' + job.id : ''"
                type="file" @change="uploadTaskImage(job.id)">
    </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'UploadJobImages',
    props: {
      job: Object,
      type: String,
      isCustomer: Boolean
    },
    data() {
      return {
        unassociatedImagesExist: false,
        disabled: {
          uploadJobImageBtn: false
        },
        user: Spark.state.user.usertype,
        loading: false
      }
    },
    computed: {
      ...mapState({}),
      closeLink() {
        if (this.type === 'sub') {
          return '#/tasks'
        }

        return '#/bid/' + this.jobTask.job_id
      }
    },
    mounted () {
      this.getUnassociatedImagesExist()
    },
    methods: {

      async getUnassociatedImagesExist() {
        if (this.job && this.job.id) {
          try {
            const data = await axios.get('/getImagesNotAssociatedToATask/' + this.job.id)
            this.unassociatedImagesExist = data.data.length > 0
          } catch (error) {
            console.log(error)
          }
        }
      },

      goToImagesPage() {
        this.$router.push({name: 'image-association', params: {jobId: this.job.id}})
      },

      isGeneral() {
        return this.contractorIdIsSameAsJobContractorId && this.userIsAContractor()
      },

      contractorIdIsSameAsJobContractorId() {
        return this.job.contractor.id === Spark.state.user.id
      },

      userIsAContractor() {
        return this.user === 'contractor'
      },

      triggerFileInput(id) {
        console.log('trigger file', id)

        $('#task_photo_' + id).click()
      },

      getJobImage(job, imageNumber) {
        return job.images[job.images.length - imageNumber]
      },

      openImage(job, imageNumber) {
        const imageId = this.getJobImage(job, imageNumber).id

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
      jobImageExists(job, imageNumber) {
        // first most recent
        if (job && job.images) {
          const length = job.images.length
          return length > 0 && this.getJobImage(job, imageNumber) !== undefined
        }
      },

      isLoading(bool) {
        this.loading = bool
      },

      jobExists() {
        return this.job
      },

      showMoreImagesBtn(job) {
        if (job && job.images) {
          return job.images.length > 0
        }
      },

      async uploadTaskImage(jobId) {
        if (this.jobExists()) {
          this.isLoading(true)
          const data = new FormData()

          data.append('photo', this.$refs['task_photo_' + jobId].files[0])
          data.append('jobTaskId', null)

          this.job.id ? data.append('jobId', this.job.id) : data.append('jobId', this.job.id)
          const disabled = await User.uploadTaskImage(data, this.disabled)
          this.isLoading(disabled)
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
