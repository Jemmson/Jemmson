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
              @messages="allMessages = $event"
              :general="isGeneral"
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
    messages: [Object, Array],
    isGeneral: Boolean
  },
  methods: {
    async addNewMessage() {
      this.loadingNewMessage = true
      const {data} = await axios.post('message/add', {
        jobTaskId: this.$route.params.index,
        userId: this.userId,
        username: this.username,
        message: this.newMessage,
        forCustomer: this.forCustomer
      })

      if (data.error) {

      } else {
        this.allMessages = data;
      }
      this.loadingNewMessage = false;
      this.add = false;
    }
  },
  mounted() {
    this.allMessages = this.messages;
  }
}
</script>

<style scoped>

</style>