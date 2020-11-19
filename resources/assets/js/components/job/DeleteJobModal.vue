<template>

    <div class="modal h-100 modal-background-gray" id="delete-job-modal" tabindex="-1" role="dialog"
         aria-labelledby="deny-task-modal"
         aria-hidden="false">
      <v-card style="margin-top: 4rem" class="mr-1rem ml-1rem">
        <v-card-title>{{ title }}</v-card-title>
        <v-card-title style="color: red">This can not be undone</v-card-title>
        <v-card-actions>
          <v-btn class="w-40" text color="red" @click="confirm()">Delete</v-btn>
          <v-btn class="w-40" text color="green" @click="cancel()">Keep</v-btn>
        </v-card-actions>
      </v-card>
    </div>
</template>

<script>
  export default {
    name: 'DeleteJobModal',
    methods: {
      confirm() {
        this.$emit('action', 'delete')
      },
      cancel() {
        this.$emit('action', 'cancel')
      }
    },
    props: {
      title: {
        type: String
      }
    }
  }
</script>

<style scoped>
    .title {
        font-size: 17pt;
    }

    .text-center {
        text-align: center;
    }
</style>