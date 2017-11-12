<template>
    <div class="form-group">
        <label class="control-label">{{ label }}
            <div class="">
                <input :type="(type !== '')? type : 'text'" class="form-control" v-model="value" :class="isDisabled">
            </div>
        </label>
    </div>
</template>

<script>
  export default {
    name: 'InputValue',
    data () {
      return {
        width: '',
        value: ''
      }
    },
    props: {
      type: {
        type: String
      },
      label: {
        type: String
      },
      disable: {
        type: Boolean
      }
    },
    computed: {
      isDisabled () {
        if (this.disable) {
          return {disabled: true}
        }
      }
    }
  }
</script>

<style scoped>
    .control-label {
        font-weight: 300;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        display: block;
        /*width: 100%;*/
        height: 36px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.6;
        color: #555555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccd0d2;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }

    .disabled {
        background-color: #eeeeee;
        opacity: 1;
        cursor: not-allowed;
    }
</style>
