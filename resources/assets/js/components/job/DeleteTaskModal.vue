<template>
    <div class="modal h-100 modal-background-gray" id="delete-task-modal" tabindex="-1" role="dialog"
         aria-labelledby="deny-task-modal"
         aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <div class="title w-full text-center">{{ title }}</div>
                </div>
                <div class="modal-body">
                    <div class="flex space-between">
                        <v-btn class="w-40" text color="red" @click="confirm()">Delete</v-btn>
                        <v-btn class="w-40" text color="green" @click="cancel()">Keep</v-btn>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'DeleteTaskModal',
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