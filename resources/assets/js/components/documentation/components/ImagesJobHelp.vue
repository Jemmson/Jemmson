<template>
  <div>
    Here you are able to add images to the job and to a particular task. You will click the browse button below and then
    you will be able to take a picture or associate an image that is in your library to the job. <br><br> This will show the 2
    latest images and you can click <strong>more images</strong> to see all of the images that were taken for the job.
    <br><br> If you see
    <strong>Associate Images With Tasks</strong> then this means that there is at least 1 task that is not tied to a
    task. By clicking
    <strong>more images</strong> you will be able to associate an image to a task. <br><br> <strong>NOTE:</strong> It is important to associate an image
    to a task if
    you plan on subbing out that task otherwise the sub contractors will be unable to see the image and they may not be
    able to properly bid on the job.
  </div>
</template>

<script>
export default {
  name: "ImagesJobHelp",
  mounted() {
    this.$store.commit('setCurrentPage', '/image-association');
    this.getSubs();
  }
}
</script>

<style scoped>

</style>