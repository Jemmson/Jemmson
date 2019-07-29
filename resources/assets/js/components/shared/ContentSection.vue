<template>
    <section class="col-12 mb-half-rem-negative flex justify-content-between" :class="sectionClasses">
        <label :for="type">{{ label }}</label>
        <strong :id="type" :class="inputClasses">
            <span v-if="dollarSign">$</span>
            {{ content }}</strong>
        <!--            <span ref="details_contractor_name" class="float-right">{{ contractorName }}</span>-->
    </section>
</template>

<script>
    export default {
      name: 'ContentSection',
      props: {
        label: String,
        content: String,
        type: String,
        dollarSign: {
          type: Boolean,
          default: false
        },
        inputClasses: {
          type: String,
          default: ""
        },
        sectionClasses: {
          type: String,
          default: ""
        }
      }
    }
</script>

<style>

</style>
