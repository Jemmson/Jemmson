<template>
  <div>
    <div @click="showModal = true">
      <i class="fa fa-question-circle bid-status-info bid-status-shadow" aria-hidden="true"></i>
    </div>
    <info-modal
            :buttons="buttons"
            class="spacing" v-if="showModal" v-on:close="closeit()"
      :title="title"
    >
      <div slot="tldr" class="body">
        <slot name="tldr">
        </slot>
      </div>

      <div slot="full" class="body">
        <slot name="full">
        </slot>
      </div>
    </info-modal>
  </div>
</template>

<script>
import InfoModal from "./InfoModal";

export default {
  components: {
    InfoModal
  },
  name: "",
  data() {
    return {
      showModal: false
    };
  },
  computed: {},
  props: {
    title: String,
    buttons: String
  },
  methods: {
    displayInfoModal() {
      // this.showModal = true;
      // $("#infoModal").show();
    },
    closeit() {
      console.log("i have been emitted");
      this.showModal = false;
      console.log(this.showModal);
    }
  }
};
</script>

<style scoped>

  /*.spacing {*/
    /*margin-top: 3rem;*/
    /*margin-bottom: 500rem;*/
  /*}*/

/* .bid-status-info {
     border: white thin solid;
 } */

@media (min-width: 450px) {
  .bid-status-info {
    margin-left: 0.5rem;
  }
}

.body {
  color: black;
  text-transform: none;
  font-size: 1.20rem;
  font-weight: 300;
}

@media (min-width: 620px) {
  .bid-status-info {
    margin-left: 0.75rem;
  }
}

/* .bid-status-shadow {
   box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 2px 4px 0 rgba(0, 0, 0, 0.08);
 }

 .bid-status-shadow:active {
   box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.11),
     0 5px 15px 0 rgba(0, 0, 0, 0.08);
 } */
</style>
