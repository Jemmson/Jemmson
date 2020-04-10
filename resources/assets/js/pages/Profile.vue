<template>
    <v-container>
        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            ref="showPhotoSection"
                            @click="showSection('photo')"
                    >Upload Photo
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('contactInformation')"
                    >Contact Info
                    </v-btn>
                </div>

            </v-card-actions>
        </v-card>

        <section
                ref="photoSection"
                v-if="show.photo"
        >
            <v-card>
                <div class="flex justify-content-between">
                    <v-card-title>Change Current Photo</v-card-title>
                    <div
                            v-if="checkForPhoto()"
                            class="m-1rem"
                    >
                        <v-icon
                                class="mr-1rem"
                                color="primary"
                                ref="rotateBtn"
                                @click="rotateImage()"
                        >mdi-axis-x-rotate-clockwise
                        </v-icon>
                        <v-btn
                                v-if="imageHasBeenRotated()"
                                :loading="loadingSaveBtn"
                                @click="saveNewUrl()"
                        >Save
                        </v-btn>
                    </div>
                </div>
                <v-card-subtitle>{{ username() }}</v-card-subtitle>

                <v-img
                        v-if="checkForPhoto()"
                        ref="userProfilePhoto"
                        :class="rotated()"
                        :src="getPhotoUrl()" aspect-ratio="1.7"></v-img>

                <v-card-actions class="flex flex-col">

                    <input
                            ref="profileImage"
                            :disabled="photo.loading"
                            class="btn btn-normal ml-2 mt-4"
                            style="width: 95%"
                            id="profileImage"
                            type="file" @change="uploadProfileImage()">

                    <v-btn
                            color="primary"
                            text
                            :loading="loadingPhoto"
                            @click="submitPhoto()"
                    >
                        Submit
                    </v-btn>

                </v-card-actions>

            </v-card>
        </section>

        <section
                v-if="show.contactInformation"
        >
            <v-card>
                <v-card-title
                >Contact Information
                </v-card-title>
                <v-form v-model="valid">
                    <v-container>

                        <v-text-field
                                id="fname"
                                v-model="form.fname"
                                required
                                label="First Name"
                        >
                        </v-text-field>

                        <v-text-field
                                id="lname"
                                v-model="form.lname"
                                required
                                label="Last Name"
                        >
                        </v-text-field>

                        <v-text-field
                                id="email"
                                v-model="form.email"
                                required
                                label="Email"
                        >
                        </v-text-field>

                        <v-text-field
                                id="phone"
                                v-model="form.phone"
                                required
                                label="Phone"
                        >
                        </v-text-field>

                        <v-text-field
                                id="addressline1"
                                v-model="form.addressline1"
                                required
                                label="Address Line 1"
                        >
                        </v-text-field>

                        <v-text-field
                                id="addressline2"
                                v-model="form.addressline2"
                                required
                                label="Address Line 2"
                        >
                        </v-text-field>

                        <v-text-field
                                id="city"
                                v-model="form.city"
                                required
                                label="City"
                        >
                        </v-text-field>

                        <v-text-field
                                id="state"
                                v-model="form.state"
                                required
                                label="State"
                        >
                        </v-text-field>

                        <v-text-field
                                id="zip"
                                v-model="form.zip"
                                required
                                label="Zip"
                        >
                        </v-text-field>


                        <v-card-actions>
                            <v-btn
                                    class="w-full"
                                    color="primary"
                                    name="submit"
                                    id="submit"
                                    dusk="submitBid"
                                    @click.prevent="update()"
                                    :loading="loadingSettings"
                            >
                                Submit
                            </v-btn>
                        </v-card-actions>

                        <br>
                        <feedback
                                page="initiateBid"
                        ></feedback>
                    </v-container>
                </v-form>

            </v-card>

        </section>

    </v-container>
</template>

<script>

    import Feedback from "../components/shared/Feedback";

    export default {
        name: 'profile',
        props: {
            user: Object
        },
        components: {
            Feedback
        },
        /**
         * The component's data.
         */
        data() {
            return {
                loadingSaveBtn: false,
                currentPhoto: null,
                degree: 0,
                show: {
                    photo: false,
                    contactInformation: false,
                },
                photoUrl: null,
                valid: false,
                loadingPhoto: false,
                loadingSettings: false,
                profile: {
                    fname: null,
                    lname: null,
                    email: null,
                    phone: null,
                    addressline1: null,
                    addressline2: null,
                    city: null,
                    state: null,
                    zip: null,
                },
                photo: {
                    loading: false
                },
                rotatedPhotoUrl: null,
                profilePhoto: null,
                beingSubmitted: false,
                form: $.extend(true, new SparkForm({
                    name: null,
                    fname: null,
                    lname: null,
                    email: null,
                    phone: null,
                    addressline1: null,
                    addressline2: null,
                    city: null,
                    state: null,
                    zip: null,
                }), Spark.forms.updateContactInformation)
            };
        },


        /**
         * Bootstrap the component.
         */
        mounted() {
            this.form.name = this.user.name;
            this.form.email = this.user.email;
            this.form.fname = this.user.first_name;
            this.form.lname = this.user.last_name;
            this.form.phone = this.user.phone;
            this.form.addressline1 = this.user.contractor.location.address_line_1;
            this.form.addressline2 = this.user.contractor.location.address_line_2;
            this.form.city = this.user.contractor.location.city;
            this.form.state = this.user.contractor.location.state;
            this.form.zip = this.user.contractor.location.zip;

            this.photoUrl = this.user.photo_url

            const urlsplit = this.photoUrl.split('/');

            if (urlsplit.length === 9) {
                let rotationDegree = urlsplit[6];
                if (rotationDegree === 'a_360') {
                    this.degree = 0
                } else if (rotationDegree === 'a_90') {
                    this.degree = 1
                } else if (rotationDegree === 'a_180') {
                    this.degree = 2
                } else if (rotationDegree === 'a_270') {
                    this.degree = 3
                }
            }

        },


        methods: {

            rotated() {

                // if (this.degree === 0) {
                //     return 'rotate360'
                // } else if (this.degree === 1) {
                //     return 'rotate90'
                // } else if (this.degree === 2) {
                //     return 'rotate180'
                // } else if (this.degree === 3) {
                //     return 'rotate270'
                // }

            },

            rotateImage() {

                this.degree = this.degree + 1;

                if (this.degree === 4) {
                    this.degree = 0;
                }

                // const urlsplit = this.photoUrl.split('/');
                // console.log('urlsplit', urlsplit);
                //
                // this.degree = this.degree + 1;
                //
                // if (this.degree === 5) {
                //     this.degree = 0;
                // }
                //
                // const rotationCase = [
                //     'a_90',
                //     'a_180',
                //     'a_270',
                //     'a_360'
                // ];
                //
                // let newUrl =
                //     urlsplit[0] + '//' +
                //     urlsplit[2] + '/' +
                //     urlsplit[3] + '/' +
                //     urlsplit[4] + '/' +
                //     urlsplit[5] + '/' +
                //     rotationCase[this.degree] + '/' +
                //     urlsplit[6] + '/' +
                //     urlsplit[7];
                //
                // this.photoUrl = newUrl;
                //
                // console.log('newUrl', this.photoUrl);

                // const degree = 'a_90';
                //
                // this.photoUrl = 'https://res.cloudinary.com/jemmson-inc/image/upload/' + degree + '/v1586043293/dslksdlkdslksdlk.jpg';
                // this.photoUrl = 'https://res.cloudinary.com/jemmson-inc/image/upload/v1586043293/dslksdlkdslksdlk.jpg';
            },

            async saveNewUrl() {
                this.loadingSaveBtn = true;

                const {data} = await axios.post('user/savePhoto', {
                    photo: this.currentPhoto
                });

                if (data.error) {

                } else {

                }

                this.loadingSaveBtn = false;
            },

            imageHasBeenRotated() {
                return this.photoUrl !== this.rotatedPhotoUrl
            },

            submitPhoto() {
                this.loadingPhoto = true;
                let formData = new FormData();
                formData.append('profilePhoto', this.profilePhoto);
                axios.post('/user/profileImage',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(function (response) {
                    this.photoUrl = response.data;
                    this.loadingPhoto = false;
                }.bind(this)).catch(function (e) {
                    console.log('e', e.message)
                    this.loadingPhoto = false;
                }.bind(this));
            },

            uploadProfileImage() {
                this.profilePhoto = this.$refs['profileImage'].files[0];
            },

            username() {

            },

            checkForPhoto() {
                if (this.user) {
                    return this.user.photo_url !== null;
                }
            },

            getPhotoUrl() {

                const urlsplit = this.photoUrl.split('/');
                const baseUrl = 'https://res.cloudinary.com/jemmson-inc/image/upload/';

                let hash = ''
                let imageName = ''

                if (urlsplit.length === 8) {
                    hash = urlsplit[6];
                    imageName = urlsplit[7];
                } else {
                    hash = urlsplit[7];
                    imageName = urlsplit[8];
                }


                if (this.degree === 0) {
                    this.currentPhoto = baseUrl + 'a_360/' + hash + '/' + imageName;
                    return this.currentPhoto;
                } else if (this.degree === 1) {
                    this.currentPhoto = baseUrl + 'a_90/' + hash + '/' + imageName;
                    return this.currentPhoto;
                } else if (this.degree === 2) {
                    this.currentPhoto = baseUrl + 'a_180/' + hash + '/' + imageName;
                    return this.currentPhoto;
                } else if (this.degree === 3) {
                    this.currentPhoto = baseUrl + 'a_270/' + hash + '/' + imageName;
                    return this.currentPhoto;
                }

            },

            showSection(section) {
                this.hideAllSections();
                if (section === 'photo') {
                    this.show.photo = true;
                } else if (section === 'contactInformation') {
                    this.show.contactInformation = true;
                }
            },

            hideAllSections() {
                this.show.photo = false;
                this.show.contactInformation = false;
            },

            /**
             * Update the user's contact information.
             */
            update() {
                this.loadingSettings = true;
                Spark.put('/settings/contact', this.form)
                    .then(() => {
                        Bus.$emit('updateUser');
                        this.loadingSettings = false;
                    });
            }
        }
    }
</script>

<style scoped>

    .rotate90 {
        transform: rotate(90deg);
    }

    .rotate180 {
        transform: rotate(180deg);
    }

    .rotate270 {
        transform: rotate(270deg);
    }

    .rotate360 {
        transform: rotate(360deg);
    }


</style>