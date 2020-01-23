<template>
    <button
            style="background-image: url('/img/blue-on-light.png'); width: 12rem; height: 2.15rem;"
            type="button" class="btn-normal " @click="connectWithStripe($route.path)"></button>
</template>

<script>

  import StripeMixin from '../../components/mixins/StripeMixin'

  export default {
    mixins: [StripeMixin]
  }
</script>
