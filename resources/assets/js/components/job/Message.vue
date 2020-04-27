<template>
    <div>
        <span class="label mb-2">{{ label }}</span>
        <textarea cols="0" rows="0" class="form-control"
                  :disabled="disableMessages"
                  v-model="theServerMessage"
                  id="message">
            </textarea>


        <v-btn
                class="primary mt-1rem"
                @click="updateMessage()"
                text
                :disabled="!shouldSendMessage"
                :loading="messageIsBeingSent"
        >
            <span v-if="shouldSendMessage">Send Message</span>
            <span v-else-if="messageIsSent" class="mr-2">Message Sent</span>
            <span v-else-if="messageIsBeingSent" class="mr-2">Sending Message</span>
        </v-btn>
    </div>
</template>

<script>
    import Language from "../../classes/Language";

    export default {
        name: "Message",
        data() {
            return {
                sendMessage: true,
                sendingMessage: true,
                messageSent: true,
                theServerMessage: "",
                messageIsBeingSent: false,
                priorMessage: ""
            };
        },
        computed: {
            shouldSendMessage() {
                if (this.serverMessage !== this.theServerMessage && !this.messageIsBeingSent) {
                    return true;
                } else {
                    return false
                }
            },
            messageIsSent() {
                if (this.serverMessage === this.theServerMessage && !this.messageIsBeingSent) {
                    return true;
                } else {
                    return false;
                }
            }
        },
        mounted() {
            this.theServerMessage = this.serverMessage;
        },
        methods: {
            async updateMessage() {
                this.messageIsBeingSent = true;
                const data = await axios.post('task/updateMessage', {
                    message: this.theServerMessage,
                    jobTaskId: this.jobId,
                    actor: this.actor
                });
                if (data.error) {
                    console.log('error', data.error)
                    this.messageIsBeingSent = false;
                } else {
                    console.log('success', data.success)
                    User.emitChange('bidUpdated');
                    Vue.toasted.success(Language.lang().bid_task.message_updated.general);
                }
                this.messageIsBeingSent = false;
            }
        },
        props: {
            jobId: Number,
            label: String,
            serverMessage: String,
            actor: String,
            disableMessages: Boolean
        }
    };
</script>

<style scoped>
</style>
