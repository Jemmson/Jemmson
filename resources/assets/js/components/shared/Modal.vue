<template>
    <!-- Modal -->
    <div class="modal h-100 modal-background-gray" style="margin-top:7rem;" id="modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex flex-col w-full">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title text-center ">{{ header }}</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <span style="font-weight: 800;">{{ body }}</span>
                </div>
                <div class="modal-footer" style="justify-content: space-between !important">
                    <slot name="button"></slot>
                    <div class="flex w-full">
                        <button type="button" class="btn btn-normal w-full mr-1rem" data-dismiss="modal">{{ no }}
                        </button>
                        <button type="button" class="btn btn-normal-red btn-model-yes w-full ml-1rem"
                                @click="$emit('modal', modalId)">{{ yes }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      modalId: String,
      header: String,
      body: String,
      no: String,
      yes: String
    }
  }
</script>