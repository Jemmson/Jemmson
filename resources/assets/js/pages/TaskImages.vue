<template>
    <div class="flex">


        <!--<div class="flex flex-col items-center">-->
            <!--<h2>Task Images</h2>-->
            <!--<label for="task-name" v-if="jobTask.task !== undefined">{{ jobTask.task.name }}</label>-->


            <!--<div class="" v-for="(image, index) of jobTask.images" :key="image.id" v-show="jobTask !== undefined && jobTask !== null">-->
                <!--<div class="image-ct">-->
                    <!--<a class="lightbox" @click.prevent="openImage(image.id)">-->
                        <!--<img :src="image.url" alt="">-->
                    <!--</a>-->
                    <!--<button class="btn btn-danger image-btn" :id="'image-' + image.id" @click="deleteImage(image.id, index)">-->
                        <!--<i class="fas fa-trash-alt"></i>-->
                    <!--</button>-->
                <!--</div>-->
                <!--&lt;!&ndash; lightbox container hidden with CSS &ndash;&gt;-->
                <!--<a class="lightbox-target" :id="'image' + image.id">-->
                    <!--<img :src="image.url" class="" :id="'image-img' + image.id">-->
                    <!--<a class="lightbox-close" :id="'image-close' + image.id" @click.prevent="closeImage(image.id)"></a>-->
                <!--</a>-->
            <!--</div>-->
        <!--</div>-->



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
                        <div class="col-xs-12 col-sm-4" v-for="(image, index) of jobTask.images" :key="image.id" v-show="jobTask !== undefined && jobTask !== null">
                                <div class="image-ct">
                                <a class="lightbox" @click.prevent="openImage(image.id)">
                                <img :src="image.url" alt="">
                                </a>
                                <button class="btn btn-danger image-btn" :id="'image-' + image.id" @click="deleteImage(image.id, index)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                </div>
                            <!-- lightbox container hidden with CSS -->
                            <a class="lightbox-target" :id="'image' + image.id">
                                <img :src="image.url" class="" :id="'image-img' + image.id">
                                <a class="lightbox-close" :id="'image-close' + image.id" @click.prevent="closeImage(image.id)"></a>
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
                jobTaskId: this.$route.params.id
            }
        },
        watch: {
            '$route' (to, from) {
                // get the bid
                this.getJobTask(this.jobTaskId);
            }
        },
        computed: {},
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
            async deleteImage(imageId, index) {
                console.log(index);
                
                document.getElementById('image-' + imageId).disabled = true;
                try {
                    const data = await axios.delete('/task/image/' + imageId, {imageId: imageId});
                    this.getJobTask(this.jobTaskId);
                    document.getElementById('image-' + imageId).disabled = false;
                } catch (error) {
                    Vue.toasted.error(error.message);
                    document.getElementById('image-' + imageId).disabled = false;

                }
            },
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
            this.getJobTask(this.jobTaskId);
        },
        mounted: function () {},
    }
</script>