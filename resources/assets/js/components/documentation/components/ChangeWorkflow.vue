<template>
  <documentation-base
      title="Change Workflow"
  >
    When a customer wishes to change the bid for some reason then the workflow would be as follows:
    <ol>
      <li>The customer changes the bid.</li>
      <li>A text is sent to the General Contractor that a change has been requested.</li>
      <li>The General would then work with the sub to make an update to the bid if needed.</li>
      <li>The sub can make changes to the task and then resubmit the task.</li>
      <li>Once the task has been resubmitted then the General will have to approve the subs changes again
        Once all changes look good then the General can resubmit the bid
      </li>
    </ol>
  </documentation-base>
</template>

<script>
import DocumentationBase from "./DocumentationBase";

export default {
  data() {
    return {}
  },
  components: {
    DocumentationBase
  }
}
</script>

<style scoped>

</style>