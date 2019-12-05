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
                        <v-btn type="button"
                               class="w-40"
                               color="primary"
                               data-dismiss="modal">{{ no }}
                        </v-btn>
                        <v-btn type="button"
                                class="w-40"
                                color="primary"
                                @click="$emit('modal', modalId)">{{ yes }}
                        </v-btn>
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