<template>
    <div>
        <span class="label mb-2">{{ label }}</span>
            <textarea cols="0" rows="0" class="form-control"
                    :disabled="disableMessages" 
                    v-model="theServerMessage"
                    id="message">
            </textarea>
        <button class="btn btn-green m-t-3 mb-4"
                @click="updateMessage()">
            <span v-if="sendMessage">Send Message</span>
            <span v-else class="mr-2">Message Sent
                    <i class="fas fa-check-circle"></i>
            </span>
        </button>
    </div>   
</template>

<script>
export default {
  name: "",
  data() {
    return {
      sendMessage: true,
      theServerMessage: "",
      priorMessage: ""
    };
  },
  computed: {
    currentMessage() {
      this.theServerMessage = this.serverMessage;
    }
  },
  mounted() {
    this.theServerMessage = this.serverMessage;
  },
  methods: {
    async updateMessage() {
      if (this.serverMessage !== this.theServerMessage) {
        this.sendMessage = false;
        await GeneralContractor.updateMessage(
          this.theServerMessage,
          this.jobId,
          this.actor
        );
        this.sendMessage = true;
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
