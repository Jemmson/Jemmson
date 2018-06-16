<template>
    <div>
        <div class="col-xs-4" v-if="showTaskImage1(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 1].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="'image' + jobTask.images[jobTask.images.length - 1].id">
                <img :src="jobTask.images[jobTask.images.length - 1].url" :id="'image-img' + jobTask.images[jobTask.images.length - 1].id">
                <a class="lightbox-close" :id="'image-close' + jobTask.images[jobTask.images.length - 1].id" @click.prevent="closeImage(jobTask.images[jobTask.images.length - 1].id)"></a>
            </a>
        </div>

        <div class="col-xs-4" v-if="showTaskImage2(jobTask)">
            <a class="lightbox" @click.prevent="openImage(jobTask.images[jobTask.images.length - 1].id)">
                <img :src="jobTask.images[jobTask.images.length - 2].url" alt="">
            </a>
            <!-- lightbox container hidden with CSS -->
            <a class="lightbox-target" :id="'image' + jobTask.images[jobTask.images.length - 2].id">
                <img :src="jobTask.images[jobTask.images.length - 2].url" :id="'image-img' + jobTask.images[jobTask.images.length - 2].id">
                <a class="lightbox-close" :id="'image-close' + jobTask.images[jobTask.images.length - 2].id" @click.prevent="closeImage(jobTask.images[jobTask.images.length - 2].id)"></a>
            </a>
        </div>

        <div class="col-xs-4" v-if="showMoreImagesBtn(jobTask)">
            <a class="lightbox" :href="'/#/task/' + jobTask.id + '/images'">
                <img :src="'/img/more.png'" alt="">
            </a>
        </div>
        <!-- / end task images preview -->

        <div class="col-xs-12">
            <!-- upload images -->
            <div class="form-group">
                <label class="col-md-6 control-label">&nbsp;</label>
                <div class="col-md-6 text-right">
                    <label type="button" class="btn btn-primary btn-upload">
                        <span>Attach Images</span>

                        <input :ref="'task_photo_' + jobTask.id" type="file" class="form-control" @change="uploadTaskImage(jobTask.id)">
                    </label>
                </div>
            </div>
        </div>
        <!-- / end upload task images-->
    </div>
</template>

<script>
    export default {
        props: {
            jobTask: Object,
            type: String
        },
        computed: {
            closeLink() {
                if (this.type === 'sub') {
                    return '#/tasks';
                } 

                return '#/bid/' + this.jobTask.job_id;
            }
        },
        methods: {
            openImage(imageId) {
                console.log(imageId);
                $('#image' + imageId).addClass('lightbox-open');
                $('#image-img' + imageId).addClass('lightbox-open-image');
                $('#image-close' + imageId).addClass('lightbox-open-close');

            },
            closeImage(imageId) {
                console.log(imageId);
                $('#image' + imageId).removeClass('lightbox-open');
                $('#image-img' + imageId).removeClass('lightbox-open-image');
                $('#image-close' + imageId).removeClass('lightbox-open-close');

            },
            showTaskImage1(jobTask) {
                // first most recent
                const length = jobTask.images.length;
                return length > 0 && jobTask.images[length - 1] !== undefined;
            },
            showTaskImage2(jobTask) {
                // second most recent
                const length = jobTask.images.length;
                return length > 1 && jobTask.images[length - 2] !== undefined;
            },
            showMoreImagesBtn(jobTask) {
                return jobTask.images.length > 0;
            },
            uploadTaskImage(jobTaskId) {
                const data = new FormData();
                console.log(this.$refs['task_photo_' + jobTaskId]);

                data.append('photo', this.$refs['task_photo_' + jobTaskId].files[0]);
                data.append('jobTaskId', jobTaskId);
                data.append('jobId', this.jobTask.job_id);
                User.uploadTaskImage(data);
            },
        }
    }
</script>