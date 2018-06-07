<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-1">
                    <div class="panel-body text-center">
                        <h2>Task Images</h2>
                        <label for="task-name" v-if="jobTask.task !== undefined">{{ jobTask.task.name }}</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-1">
                    <div class="panel-body">
                        <div class="col-xs-12 col-sm-4" v-for="image of jobTask.images" :key="image.id" v-show="jobTask !== undefined && jobTask !== null">
                                <div class="image-ct">
                                <a class="lightbox" :href="'#image' + image.id">
                                <img :src="image.url" alt="">
                                </a>
                                <button class="btn btn-danger image-btn" @click="deleteImage(image.id)"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            <!-- lightbox container hidden with CSS -->
                            <a class="lightbox-target" :id="'image' + image.id">
                                <img :src="image.url">
                                <a class="lightbox-close" :href="'#/task/' + jobTask.id + '/images'"></a>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
        },
        data() {
            return {
                jobTask: {},
            }
        },
        watch: {
            '$route' (to, from) {
                // get the bid
                const jobTaskId = this.$route.params.id;
                this.getJobTask(jobTaskId);
            }
        },
        computed: {},
        methods: {
            async getJobTask(id) {
                try {
                    const {
                        data
                    } = await axios.get('/jobtask/' + id);
                    this.jobTask = data;
                } catch (error) {
                    error = error.response.data;
                    Vue.toasted.error(error.message);
                }
            }
        },
        created: function () {

            // get the bid
            const jobTaskId = this.$route.params.id;
            this.getJobTask(jobTaskId);
        },
        mounted: function () {},
    }
</script>