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
                        :src="getPhotoUrl()" aspect-ratio="1.7"></v-img>

                <v-card-actions class="flex flex-col">

                    <input
                            ref="profileImage"
                            :disabled="photo.loading"
                            class="btn btn-normal ml-2 mt-4"
                            style="width: 95%"
                            id="profileImage"
                            type="file" @change="uploadProfileImage()">
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
                                v-model="profile.fname"
                                required
                                label="First Name"
                        >
                        </v-text-field>

                        <v-text-field
                                id="lname"
                                v-model="profile.lname"
                                required
                                label="Last Name"
                        >
                        </v-text-field>

                        <v-text-field
                                id="email"
                                v-model="profile.email"
                                required
                                label="Email"
                        >
                        </v-text-field>

                        <v-text-field
                                id="phone"
                                v-model="profile.phone"
                                required
                                label="Phone"
                        >
                        </v-text-field>

                        <v-text-field
                                id="addressline1"
                                v-model="profile.addressline1"
                                required
                                label="Address Line 1"
                        >
                        </v-text-field>

                        <v-text-field
                                id="addressline2"
                                v-model="profile.addressline2"
                                required
                                label="Address Line 2"
                        >
                        </v-text-field>

                        <v-text-field
                                id="city"
                                v-model="profile.city"
                                required
                                label="City"
                        >
                        </v-text-field>

                        <v-text-field
                                id="state"
                                v-model="profile.state"
                                required
                                label="State"
                        >
                        </v-text-field>

                        <v-text-field
                                id="zip"
                                v-model="profile.zip"
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
                                    :loading="beingSubmitted"
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
                valid: false,
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
                beingSubmitted: false,
                form: $.extend(true, new SparkForm({
                    name: '',
                    email: ''
                }), Spark.forms.updateContactInformation)
            };
        },


        /**
         * Bootstrap the component.
         */
        mounted() {
            this.form.name = this.user.name;
            this.form.email = this.user.email;

            this.profile.fname = this.user.first_name;
            this.profile.lname = this.user.last_name;
            this.profile.email = this.user.email;
            this.profile.phone = this.user.phone;
            this.profile.addressline1 = this.user.contractor.location.address_line_1;
            this.profile.addressline2 = this.user.contractor.location.address_line_2;
            this.profile.city = this.user.contractor.location.city;
            this.profile.state = this.user.contractor.location.state;
            this.profile.zip = this.user.contractor.location.zip;

        },


        methods: {

            uploadProfileImage() {

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
                Spark.put('/settings/contact', this.form)
                    .then(() => {
                        Bus.$emit('updateUser');
                    });
            }
        }
    }
</script>

<style scoped>

</style>