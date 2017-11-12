<template>
    <div>
        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                </ul>
            </div>
            <!--<b-dropdown id="" text="Select a Contract" class="m-md-2">-->
                <!--<b-dropdown-item-button @click="selectedContract('simple')">Simple</b-dropdown-item-button>-->
                <!--<b-dropdown-item-button @click="selectedContract('medium')">Medium</b-dropdown-item-button>-->
                <!--<b-dropdown-item-button @click="selectedContract('complicated')">Complicated</b-dropdown-item-button>-->
                <!--<b-dropdown-item-button @click="selectedContract('custom')">Custom</b-dropdown-item-button>-->
            <!--</b-dropdown>-->
            <label class="contractSelection" @click="showContract">{{ selectedCon }}</label>
        </div>
        <div v-if="simpleContract">
            <h3>Simple Contract</h3>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam animi maxime omnis quaerat quod repellat! Consequuntur est facere ipsa quaerat reiciendis vel. Culpa est et maiores nostrum officia pariatur, sequi!
            <button class="btn btn-primary btn-large" @click="saveContract('simple')">Save</button>
        </div>
        <div v-if="mediumContract">
            <h3>Medium Contract</h3>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam animi maxime omnis quaerat quod repellat! Consequuntur est facere ipsa quaerat reiciendis vel. Culpa est et maiores nostrum officia pariatur, sequi!
            <button class="btn btn-primary btn-large" @click="saveContract('medium')">Save</button>
        </div>
        <div v-if="complicatedContract">
            <h3>Complicated Contract</h3>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam animi maxime omnis quaerat quod repellat! Consequuntur est facere ipsa quaerat reiciendis vel. Culpa est et maiores nostrum officia pariatur, sequi!
            <button class="btn btn-primary btn-large" @click="saveContract('large')">Save</button>
        </div>
        <div v-if="customContract">
            <h3>Custom Contract</h3>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam animi maxime omnis quaerat quod repellat! Consequuntur est facere ipsa quaerat reiciendis vel. Culpa est et maiores nostrum officia pariatur, sequi!
            <button class="btn btn-primary btn-large" @click="saveContract('custom')">Save</button>
        </div>
    </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: '',
    data () {
      return {
        selectedCon: '',
        simpleContract: false,
        mediumContract: false,
        complicatedContract: false,
        customContract: false,
        simpleContractTerms: '',
        mediumContractTerms: '',
        complicatedContractTerms: '',
        customContractTerms: [
          {terms: ''},
          {terms: ''},
          {terms: ''},
          {terms: ''}
        ]
      }
    },
    methods: {
      selectedContract: function (type) {
        this.resetContracts()
        this.selectedCon = type
      },
      showContract: function () {
//        debugger
        if (this.selectedCon === 'simple') {
          this.simpleContract = true
        } else if (this.selectedCon === 'medium') {
          this.mediumContract = true
        } else if (this.selectedCon === 'complicated') {
          this.complicatedContract = true
        } else if (this.selectedCon === 'custom') {
          this.customContract = true
        }
      },
      resetContracts: function () {
//        debugger
        this.$data.simpleContract = false
        this.$data.mediumContract = false
        this.$data.complicatedContract = false
        this.$data.customContract = false
      },
      saveContract () {
        axios.get('', {
          params: {
            query: this.query
          }
        }).then(response => {
          console.log(response.data)
          this.results = response.data
        })
      }
    }
  }
</script>

<style scoped>
    .contractSelection {
        color: blue;
        text-decoration: underline;
        text-decoration-color: blue;
    }
</style>
