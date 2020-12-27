<template>
  <div>
    <div class="flex align-content-center justify-content-between">
      <v-card-title>Job Task Messages</v-card-title>
      <v-icon @click="add = true" color="primary" class="mr-3">mdi-plus</v-icon>
    </div>
    <v-simple-table>
      <template>
        <tbody>
        <tr v-for="(item, i) in allMessages" :key="i">
          <message
              @messages="updateMessages($event)"
              :general="isGeneral"
              :job-task-id="getJobTaskId()"
              :item="item"></message>
        </tr>
        </tbody>
      </template>
    </v-simple-table>

    <v-dialog
        v-if="add"
        v-model="add"
        fullscreen
        id="addDialog"
    >

      <v-card>
        <v-card-title>Add Message</v-card-title>
        <v-card-text class="pt-10">
          <v-textarea
              auto-grow
              label="Add A Private Message"
              clearable
              outlined
              filled
              v-model="newMessage"
          >
          </v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn size="small" text @click="add = false" color="red">
            cancel
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
              :loading="loadingNewMessage"
              @click="addNewMessage()" size="small" text color="primary">
            submit
          </v-btn>
        </v-card-actions>
      </v-card>


    </v-dialog>

  </div>
</template>

<script>
import Message from "./Message";

export default {
  name: "Messages",
  data() {
    return {
      loadingNewMessage: false,
      add: false,
      allMessages: [],
      newMessage: '',
      userId: Spark.state.user.id,
      username: Spark.state.user.name,
      forCustomer: false
    }
  },
  components: {
    Message
  },
  props: {
    isGeneral: Boolean,
    jobTaskId: {
      default: -1,
      type: [Number, String]
    }
  },
  methods: {
    async addNewMessage() {
      this.loadingNewMessage = true
      const jobTaskId = this.getJobTaskId()
      const {data} = await axios.post('message/add', {
        jobTaskId: jobTaskId,
        userId: this.userId,
        username: this.username,
        message: this.newMessage,
        forCustomer: this.forCustomer
      })

      if (data.error) {

      } else {
        this.allMessages = null;
        this.allMessages = data;
      }
      this.loadingNewMessage = false;
      this.add = false;
    },
    getJobTaskId(){
      if (this.jobTaskId !== -1) {
        return this.jobTaskId
      } else {
        return this.$route.params.index
      }
    },
    updateMessages(messages){
      this.allMessages = null;
      this.$nextTick(() => {
        this.allMessages = messages;
      })
    },
    async getJobTaskMessages() {
      const {data} = await axios.get('messages/all/' + this.getJobTaskId())
      if (data.error) {

      } else {
        this.updateMessages(data)
      }
    }
  },
  mounted() {
    this.getJobTaskMessages();
  }
}
</script>

<style scoped>

</style>