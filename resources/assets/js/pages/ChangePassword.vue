<template>
    <v-container>
        <v-card>
            <v-card-title
            >Change Password</v-card-title>
            <v-form v-model="valid">
                <v-container>

                    <v-text-field
                            id="currentPassword"
                            v-model="currentPassword"
                            required
                            :counter="20"
                            label="Current Password"
                    >
                    </v-text-field>

                    <v-text-field
                            id="password"
                            v-model="password"
                            required
                            :counter="20"
                            label="Password"
                    >
                    </v-text-field>

                    <v-text-field
                            id="confirmPassword"
                            v-model="confirmPassword"
                            required
                            :counter="20"
                            label="Confirm Password"
                    >
                    </v-text-field>

                    <v-card-actions>
                        <v-btn
                                class="w-full"
                                color="primary"
                                name="submit"
                                id="submit"
                                dusk="submitBid"
                                @click.prevent="submit()"
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
    </v-container>
</template>

<script>

    import Feedback from "../components/shared/Feedback";

    export default {
        name: 'ChangePassword',
        components: {
          Feedback
        },
        data() {
            return {
                valid: false,
                currentPassword: null,
                password: null,
                confirmPassword: null,
                beingSubmitted: false
            }
        },
        methods: {
            submit(){

            }
        }
    }
</script>

<style scoped>

</style>