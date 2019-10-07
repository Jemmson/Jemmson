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
                        <button class="btn btn-normal-red flex-1 mr-1rem ml-1rem" @click="cancel()">Cancel</button>
                        <button class="btn btn-normal flex-1 mr-1rem ml-1rem" @click="confirm()">Yes</button>
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