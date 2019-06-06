<template>
    <div>

        <transition
                mode="out-in"
                @before-enter="beforeEnter"
                @enter="enter"

                @before-leave="beforeLeave"
                @leave="leave"
                :css="false">
            <div class="jem-block">
                <h1>Who Are You?</h1>
                <div class="jem-buttons">
                    <button class="btn btn-lg btn-primary flex-1 btleft" v-on:click="">Customer</button>
                    <button class="btn btn-lg btn-primary flex-1 btright" v-on:click="">Contractor</button>
                </div>
            </div>
        </transition>


    </div>
</template>

<script>
  export default {
    name: 'Register',
    methods: {
      goToCheckAccounting() {
        // have to check quickbooks is turned on
        // have to check if this page has been loaded from the check_accounting page
        // these buttons should only load this page was routed to directly and not from the check accounting page
        // these buttons have to display that they were clicked
        // these buttons need to load with the registerForm
      }
    },
  }
</script>

<style scoped>

    .jem-block {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .jem-buttons {
        width: 100%;
        display: flex;
    }

    .flex-1 {
        width: 100%;
    }

    .btleft {
        margin-left: 1rem;
        margin-right: .5rem;
    }

    .btright {
        margin-left: .5rem;
        margin-right: 1rem;
    }

</style>