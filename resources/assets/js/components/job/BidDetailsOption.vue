<template>
  <v-card :height="height" :width="width" class="mt-1rem mb-1rem"
          active-class="successful-submit"
    @click="openDialog()"
  >
    <v-card-text class="flex align-center justify-center">
      <div class="flex flex-column align-center justify-center">

        <v-badge
            v-if="num"
            :content="num"
        >
          <v-icon
              height="50%"
              :size="iconSize"
          >
            {{ icon }}
          </v-icon>
        </v-badge>

        <v-icon
            v-else
            height="50%"
            :size="iconSize"
        >
          {{ icon }}
        </v-icon>

        <div>{{ title }}</div>

      </div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "BidDetailsOption",
  props: {
    num: null,
    height: Number,
    width: Number,
    title: String,
    icon: String,
    iconSize: {
      type: Number,
      default: 50
    }
  },
  methods: {
    openDialog(){
      this.$emit('openDialog')
    }
  }
}
</script>

<style scoped>

</style>