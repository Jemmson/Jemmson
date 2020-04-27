<template>
    <v-dialog
            width="500"
            v-model="openModal"
    >

        <div style="display: none">{{ open }}</div>

        <v-card>
            <v-card-title
                    class="w-break headline primary lighten-2"
                    primary-title
            >
                {{ title }}
            </v-card-title>

            <v-card-text class="mt-1rem">
                {{ text }}
            </v-card-text>

            <v-card-actions>
                <v-btn
                        primary
                        text
                        @click="close()"
                >CLOSE
                </v-btn>
            </v-card-actions>

        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: 'InfoModalGeneric',
        data: function () {
            return {
                openModal: false
            }
        },
        watch: {
            openModal (val) {
              if (!val) {
                  this.$emit('close-modal', this.modal)
              }
            },
        },
        props: {
            modal: String,
            openDialog: Boolean,
            text: String,
            title: String
        },
        computed: {
            open: function () {
                this.openModal = this.openDialog
            }
        },
        methods: {
            close: function () {
                this.openModal = false
                this.$emit('close-modal', this.modal)
            }
        }
    }
</script>

<style>

</style>
