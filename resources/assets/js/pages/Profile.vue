<template>
    <v-container>
        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
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
                v-if="show.photo"
        >
            <v-card>
                <v-card-title>Change Current Photo</v-card-title>
                <v-card-subtitle>{{ username() }}</v-card-subtitle>

                <v-img
                        v-if="checkForPhoto()"
                        :src="photoUrl" aspect-ratio="1.7"></v-img>

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
                >Contact Information</v-card-title>
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
            this.form.email = this.user.email;
            this.form.phone = this.user.phone;
            this.form.addressline1 = this.user.contractor.location.address_line_1;
            this.form.addressline2 = this.user.contractor.location.address_line_2;
            this.form.city = this.user.contractor.location.city;
            this.form.state = this.user.contractor.location.state;
            this.form.zip = this.user.contractor.location.zip;

            this.photoUrl = this.user.photo_url

        },


        methods: {

            submitPhoto(){
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

            username () {

            },

            checkForPhoto() {
                if (this.user) {
                    return this.user.photo_url !== null;
                }
            },

            getPhotoUrl(){
                if (this.user) {
                    return this.user.photo_url;
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

</style>