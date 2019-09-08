<template>
    <section :class="totalSectionClasses" class="col-12 mb-half-rem-negative flex flex-col">
        <div class="flex justify-content-between">
            <label class="flex-1" :for="type">{{ label }}</label>
            <strong class="flex-1 ta-right" :id="type" :class="inputClasses">
                <span v-if="warning"><i class="fas fa-exclamation mr-half-rem"></i></span>
                <i v-if="icon" :class="icon" class="mr-half-rem"></i>
                <span v-if="dollarSign">$</span>
                <span :ref="inputRef" :class="contentClasses">{{ content }}</span>
            </strong>
        </div>
        <aside class="red uppercase ml-1rem mr-1rem" v-if="warning">{{ warningMessage }}</aside>
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
        contentClasses: {
          type: String,
          default: ""
        },
        warningMessage: {
          type: String,
          default: ""
        },
        sectionClasses: {
          type: String,
          default: ""
        },
        warning: {
          type: Boolean,
          default: false
        },
        icon: {
          type: String,
          default: ''
        },
        inputRef: {
          type: String,
          default: ''
        }
      },
      computed: {
        totalSectionClasses(){
          if (this.warning) {
            return 'red ' + this.sectionClasses;
          } else {
            return this.sectionClasses;
          }
        }
      }
    }
</script>

<style>

</style>
