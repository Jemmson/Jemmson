<template>
    <div class="modal h-100 modal-background-gray" id="delete-task-modal" tabindex="-1" role="dialog" aria-labelledby="deny-task-modal"
         aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <h2>Do You Want To Delete This Task?</h2>
                </div>
                <div class="modal-body">
                    <div class="flex space-between">
                        <button class="btn btn-normal" @click="confirm()">Yes</button>
                        <button class="btn btn-normal" @click="cancel()">Cancel</button>
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
        this.$emit('action', 'cancel');
      }
    }
  }
</script>

<style scoped>

</style>