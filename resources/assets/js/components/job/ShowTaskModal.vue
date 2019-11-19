<template>
<!--    <div class="modal h-100 modal-background-gray" id="show-task-modal" tabindex="-1" role="dialog"-->
<!--         aria-labelledby="deny-task-modal"-->
<!--         aria-hidden="false">-->
<!--        <div class="modal-dialog" role="document">-->
<!--            <div class="modal-content styled">-->
<!--                <div class="modal-header">-->
<!--                    <div class="title w-full text-center">TASK</div>-->
<!--                </div>-->
<!--                <div class="modal-body">-->

<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <div>

    </div>

</template>

<script>
  export default {
    name: 'ShowTaskModal',
    methods: {},
    props: {
      jobTask: {
        type: Object
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