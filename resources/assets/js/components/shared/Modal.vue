<template>
       <!-- Modal -->
     <div class="modal fade" style="margin-top:7rem;" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">{{ header }}</h4>
                </div>
                <div class="modal-body">
                    <span style="font-weight: 800;">{{ body }}</span>
                </div>
                <div class="modal-footer">
                    <span>
                        <slot name="button"></slot>
                        <button type="button" class="btn btn-normal" data-dismiss="modal">{{ no }}</button>
                        <button type="button" class="btn btn-normal btn-model-yes" @click="$emit('modal', modalId)">{{ yes }}</button>
                    </span>
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
};
</script>