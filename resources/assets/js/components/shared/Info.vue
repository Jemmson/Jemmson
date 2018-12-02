<template>
    <i class="fa 
            fa-question-circle 
            bid-status-info
            bid-status-shadow
            " aria-hidden="true"></i>
</template>

<script>
export default {
  name: "",
  data() {
    return {};
  },
  computed: {},
  methods: {}
};
</script>

<style scoped>
 /* .bid-status-info {
     border: white thin solid;
 } */

@media (min-width: 450px) {
  .bid-status-info {
    margin-left: 0.5rem;
  }
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
