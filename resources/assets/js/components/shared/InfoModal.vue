<template>
    <transition name="modal" class="spacing">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-header">
                        <div class="flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <div></div>
                                <h3 class="text-center mb-1" id="header-spacing">{{ title }}</h3>
                                <button class="modal-default-button btn-close" @click="$emit('close')">x</button>
                            </div>
                            <div v-show="buttons === 'true'">
                                <div class="flex">
                                    <button class="flex-1 mr-1 btn btn-normal" @click="showBody('tldr')">OVERVIEW
                                    </button>
                                    <button class="flex-1 ml-1 btn btn-normal" @click="showBody('full')">DETAILS</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div v-show="tldr">
                            <slot name="tldr">

                            </slot>
                        </div>

                        <div v-show="full">
                            <slot name="full" v-show="full">

                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
  export default {
    name: '',
    data() {
      return {
        //   showModal: false
        tldr: true,
        full: false
      }
    },
    computed: {},
    methods: {
      showBody(val) {
        if (val === 'tldr') {
          this.full = false
          this.tldr = true
        } else {
          this.tldr = false
          this.full = true
        }
      }
    },
    props: {
      show: Boolean,
      title: String,
      buttons: String
    }
  }
</script>

<style scoped>

    .btn-close {
        margin-top: -3rem;
        margin-right: -2rem;
        margin-left: 2rem;
    }

    #header-spacing {
        margin-top: -1rem;
    }

    .spacing {
        margin-bottom: 10rem;
    }

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: table;
        transition: opacity 0.3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .btn-color {
        background-color: #2779bd;
        /* color: black; */
    }

    .modal-container {
        width: 90%;
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
        transition: all 0.3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 0rem;
    }

    .modal-default-button {
        float: right;
    }

    /*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>
