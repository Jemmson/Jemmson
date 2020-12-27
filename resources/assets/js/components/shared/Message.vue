<template>
  <td>
    <div class="flex flex-col">
      <div class="flex align-content-center justify-content-between"
           style="margin-bottom: .25rem; margin-top: .5rem">
        <div class="w-full">{{ currentMessage.username }}</div>
        <v-spacer></v-spacer>
        <div class="w-full" style="color:grey">{{ dateOnly(currentMessage.updated_at) }}</div>
        <v-spacer></v-spacer>
        <div class="flex justify-content-end">
          <v-icon v-if="iOwnMessage" @click="edit = true" color="primary" class="mr-3">mdi-comment-edit</v-icon>
          <v-icon v-if="isGeneral || iOwnMessage" @click="deleteMessage = true" color="red" class="mr-3">mdi-delete
          </v-icon>
        </div>
      </div>
      <div style="margin-bottom: .25rem; font-size: 12pt; font-weight: bold">{{ currentMessage.message }}</div>
    </div>

    <v-dialog
        v-if="edit"
        v-model="edit"
        fullscreen
        id="editDialog"
    >
      <v-card>
        <v-card-title>Edit Message</v-card-title>
        <v-card-text class="pt-10">
          <v-textarea
              auto-grow
              label="Edit Message"
              clearable
              outlined
              filled
              v-model="currentMessage.message"
          >
          </v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn size="small" text @click="edit = false" color="red">
            cancel
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
              :loading="loadingUpdateMessage"
              @click="updateExistingMessage()" size="small" text color="primary">
            submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
        v-if="deleteMessage"
        v-model="deleteMessage"
        fullscreen
        id="deleteDialog"
    >
      <v-card>
        <v-card-title>Delete Message</v-card-title>
        <v-card-text class="pt-10">
          <p style="color: black">
            {{ currentMessage.message }}
          </p>
        </v-card-text>
        <v-card-actions>
          <v-btn size="small" text @click="deleteMessage = false" color="red">
            cancel
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
              :loading="loadingDeleteMessage"
              @click="deleteExistingMessage()" size="small" text color="primary">
            delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>


  </td>
</template>

<script>
import Utilities from "../mixins/Utilities";

export default {
  name: "Message",
  mixins: [
    Utilities
  ],
  data() {
    return {
      edit: false,
      loadingDeleteMessage: false,
      loadingUpdateMessage: false,
      deleteMessage: false,
      updatedMessage: false,
      currentMessage: {}
    }
  },
  props: {
    item: Object,
    general: Boolean,
    jobTaskId: {
      default: -1,
      type: [Number, String]
    }
  },
  computed: {
    isGeneral() {
      return this.general;
    },
    iOwnMessage() {
      return this.currentMessage.user_id === Spark.state.user.id
    }
  },
  methods: {
    async deleteExistingMessage() {
      this.loadingDeleteMessage = true;
      const jobTaskId = this.getJobTaskId()
      const {data} = await axios.post('message/delete', {
        jobTaskId: jobTaskId,
        messageId: this.item.id
      })

      if (data.error) {

      } else {
        this.$emit('messages', data);
      }
      this.deleteMessage = false
      this.loadingDeleteMessage = false;
    },
    getJobTaskId() {
      if (this.jobTaskId !== -1) {
        return this.jobTaskId
      } else {
        return this.$route.params.index
      }
    },
    async updateExistingMessage() {
      this.loadingUpdateMessage = true;
      const jobTaskId = this.getJobTaskId()
      const {data} = await axios.post('message/update', {
        jobTaskId: jobTaskId,
        updatedMessage: this.currentMessage
      })

      if (data.error) {

      } else {
        this.$emit('messages', data);
      }
      this.edit = false;
      this.loadingUpdateMessage = false;
    }
  },
  mounted() {
    this.currentMessage = this.item
  }
}
</script>

<style scoped>

</style>