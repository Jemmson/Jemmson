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
      if (this.shouldSendMessage) {
        this.messageIsBeingSent = true;
        await GeneralContractor.updateMessage(
          this.theServerMessage,
          this.jobId,
          this.actor
        );
        this.messageIsBeingSent = false;
      }
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
