<template>
    <div class="flex flex-col">
        <v-card
                class="margins"
        >
            <h2 class="self-center uppercase p-1rem text-center">Task Images</h2>
            <v-card-actions>
                <v-btn
                        color="primary"
                        class="w-100"
                        text
                        @click.prevent="goBack()">
                    Jobs Page
                </v-btn>
            </v-card-actions>
        </v-card>

        <!--<pre>{{ jobTask }}</pre>-->

        <v-card
                v-for="(image, index) of jobTask.images" :key="image.id"
                class="margins"
        >
            <v-card-title
                    v-if="jobTask.task !== undefined"
                    for="task-name"
                    class="w-break self-center mt-2">{{ jobTask.task.name }}
            </v-card-title>

            <v-img
                    :src="image ? image.url : null"
                    height="200px"
            ></v-img>

            <v-card-actions>
                <v-btn
                        v-if="userCreatedImage(image.user_id)"
                        class="w-full"
                        color="red"
                        text
                        :id="'image-' + image.id"
                        @click="deleteImage(image.id, index)">
                    <i class="fas fa-trash-alt"></i>
                </v-btn>
            </v-card-actions>

        </v-card>
        <!--        <div class="flex flex-col card">-->
        <!--            <div class="flex-1 mb-4" v-for="(image, index) of jobTask.images" :key="image.id"-->
        <!--                 >-->
        <!--&lt;!&ndash;                v-show="jobTask !== undefined && jobTask !== null"&ndash;&gt;-->
        <!--                <div class="image-ct">-->
        <!--                    <a class="lightbox" @click.prevent="openImage(image.id)">-->
        <!--                        <img :src="image.url" alt="">-->
        <!--                    </a>-->
        <!--                    <v-btn-->
        <!--                            v-if="userCreatedImage(image.user_id)"-->
        <!--                            class="w-full"-->
        <!--                            color="red"-->
        <!--                            :id="'image-' + image.id"-->
        <!--                            @click="deleteImage(image.id, index)">-->
        <!--                        <i class="fas fa-trash-alt"></i>-->
        <!--                    </v-btn>-->
        <!--                </div>-->
        <!--                &lt;!&ndash; lightbox container hidden with CSS &ndash;&gt;-->
        <!--                <a class="lightbox-target" :id="'image' + image.id">-->
        <!--                    <img :src="image.url" class="" :id="'image-img' + image.id">-->
        <!--                    <a class="lightbox-close" :id="'image-close' + image.id" @click.prevent="closeImage(image.id)"></a>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <feedback></feedback>
    </div>
</template>

<script>
    import Card from '../components/shared/Card'
    import Feedback from '../components/shared/Feedback'

    export default {
        props: {
            user: Object,
        },
        components: {
            Card,
            Feedback
        },
        data() {
            return {
                jobTask: {},
                jobTaskId: this.$route.params.id
            }
        },
        watch: {
            '$route'(to, from) {
                // get the bid
                this.getJobTask(this.jobTaskId)
            },

        },
        computed: {},
        methods: {
            userCreatedImage(imageUserId) {
                return this.user.id === imageUserId
            },
            goBack() {
                this.$router.push('/bids')
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
            async deleteImage(imageId, index) {
                console.log(index)

                document.getElementById('image-' + imageId).disabled = true
                try {
                    const data = await axios.delete('/task/image/' + imageId, {imageId: imageId})
                    this.getJobTask(this.jobTaskId)
                    document.getElementById('image-' + imageId).disabled = false
                } catch (error) {
                    Vue.toasted.error(error.message)
                    document.getElementById('image-' + imageId).disabled = false

                }
            },
            async getJobTask(id) {
                try {
                    const {
                        data
                    } = await axios.get('/jobtask/' + id)
                    this.jobTask = data
                } catch (error) {
                    error = error.response.data
                    Vue.toasted.error(error.message)
                }
            }
        },
        created: function () {
            // get the bid
            this.getJobTask(this.jobTaskId)
        },
        mounted: function () {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
            Bus.$on('bidUpdated', (payload) => {
                this.getJobTask()
            })
        },
    }
</script>