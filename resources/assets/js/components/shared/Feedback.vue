<template>
<div>
    <div id="feedback" style="position:fixed; bottom: 0; right: 0; margin-bottom: 4rem">
        <button class="btn btn-blue" @click="open">
            <i class="fas fa-comment fa-2x"></i>
        </button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="feedback-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" style="margin-top: 7rem">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">App Feedback</h4>
                </div>
                <div class="modal-body">
                    <form >
                        <div class="form-group">
                            <textarea class="form-control" name="comment" rows="4" v-model="comment" placeholder="Please tell us what you think about the site. What can be improved, something missing or whatever works well."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <span>
                        <slot name="button"></slot>
                        <button type="submit" class="btn btn-green btn-model-yes" @click.prevent="submit">Submit</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data: function () {
            return {
                comment: '',
                disabled: {
                    submit: false,
                }
            };
        },
        methods: {
            open() {
                $('#feedback-modal').modal();
            },
            submit() {
                User.submitFeedback(this.comment, this.disabled);
            }
        }
    }
</script>

<style>
    #feedback{
        z-index: 1000;
    }
</style>