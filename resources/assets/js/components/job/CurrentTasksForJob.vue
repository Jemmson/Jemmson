<template>
    <div>
        <!--<pre>{{ allTasks }}</pre>-->
        <!--<pre>{{ getUser }}</pre>-->

        <!--<pre>{{ showDetails[0].show }}</pre>-->
        <!--<pre>{{ showDetails[1].show }}</pre>-->
        <!--<pre>{{ showDetails[2].show }}</pre>-->
        <!--<pre>{{ showDetails[3].show }}</pre>-->
        <!--<pre>{{ showDetails[4].show }}</pre>-->

        <div class="joblist" v-if="getUser === 'contractor'">
            <div class="wrapper1">
                <div class="header">Task Name</div>
                <div class="header">Final Customer Price</div>
                <div class="header">Final Sub Price</div>
                <div></div>
            </div>
            <div v-for="(task, index) in allTasks">
                <div class="wrapper1">
                    <div class="taskName">{{ task.name }}</div>
                    <div class="price">{{ task.cust_final_price }}</div>
                    <div class="price">{{ task.sub_final_price }}</div>
                    <button class="btn btn-sm btn-primary" @click="showDetails[index].show = !showDetails[index].show">Details
                    </button>
                </div>
                <div class="subwrapper" v-if="showDetails[index].show">
                    <div class="customer">Customer</div>
                    <div class="contractor">Contractor</div>
                    <label class="contcustpricelabel">Proposed Contractor Price</label>
                    <div class="contcustprice">{{ task.cont_cust_proposed }}</div>
                    <label class="contsubpricelabel">Proposed Contractor Price</label>
                    <div class="contsubprice">{{ task.cont_sub_proposed }}</div>
                    <label class="custpricelabel">Proposed Customer Price</label>
                    <div class="custprice">{{ task.cust_cont_proposed }}</div>
                    <label class="subpricelabel">Proposed SubContractor Price</label>
                    <div class="subprice">{{ task.sub_cont_proposed }}</div>
                    <button class="btn btn-sm btn-primary custaccepted">Customer Accepted</button>
                    <button class="btn btn-sm btn-primary subaccepted">SubContractor Accepted</button>
                    <button class="btn btn-sm btn-primary contcustaccepted">Contractor Accepted</button>
                    <button class="btn btn-sm btn-primary contsubaccepted">Contractor Accepted</button>
                    <!--<jemm-date class="startDate custstartdate" label="Task Start Date" serverurl="/job/update"-->
                    <!--dbcolumn="agreed_start_date"-->
                    <!--&gt;</jemm-date>-->
                    <!--<jemm-date class="endDate custenddate" label="Task End Date" serverurl="/job/update"-->
                    <!--dbcolumn="agreed_end_date"-->
                    <!--&gt;</jemm-date>-->
                    <!--<jemm-date class="startDate substartdate" label="Task Start Date" serverurl="/job/update"-->
                    <!--dbcolumn="agreed_start_date"-->
                    <!--&gt;</jemm-date>-->
                    <!--<jemm-date class="endDate subenddate" label="Task End Date" serverurl="/job/update"-->
                    <!--dbcolumn="agreed_end_date"-->
                    <!--&gt;</jemm-date>-->
                    </div>
                    <!--<button class="btn btn-sm btn-primary">Accept</button>-->
                    <!--<button class="btn btn-sm btn-primary">Edit</button>-->
                    <!--<a href="#">Sub Info</a>-->
                    <!--<div class="price">Price</div>-->
                    <!--</div>-->
                    <!--<div class="wrapper2">-->
                    <!--<h3>Customer</h3>-->
                    <!--<h3>SubContractor</h3>-->
                    <!--<div class="header">Proposed Contractor Price For Customer</div>-->
                </div>
            </div>
        </div>
        <!--<div class="wrapper">-->
        <!--<div class="header">Task Name</div>-->
        <!--<div class="header">Proposed Contractor Price For Customer</div>-->
        <!--<div class="header">Proposed Customer Price</div>-->
        <!--<div class="header">Accept Price Of Customer</div>-->
        <!--<div class="header">Final Customer Price</div>-->
        <!--<div class="header">Proposed Contractor Price For Sub</div>-->
        <!--<div class="header">Proposed Sub Price</div>-->
        <!--<div class="header">Accept Price Of Sub</div>-->
        <!--<div class="header">Final Sub Price</div>-->
        <!--<div class="header">Start Date</div>-->
        <!--<div class="header">End Date</div>-->
        <!--<div class="header">Delete</div>-->
        <!--<div class="header">Edit</div>-->
        <!--<div class="header">Sub Info</div>-->
        <!--<div class="header">Status</div>-->
        <!--</div>-->
        <!--<div v-for="task in allTasks">-->
        <!--<div class="wrapper">-->
        <!--<div class="taskName">{{ task.name }}</div>-->
        <!--<div class="price">{{ task.proposed_cust_price }}</div>-->
        <!--<div class="price">{{ task.cust_cont_proposed }}</div>-->
        <!--<div class="price">{{ task.proposed_sub_price }}</div>-->
        <!--<div class="price">{{ task.sub_final_price }}</div>-->
        <!--<div class="price">{{ task.cust_final_price }}</div>-->
        <!--<jemm-date class="startDate" label="" serverurl="/job/update"-->
        <!--dbcolumn="agreed_start_date"-->
        <!--&gt;</jemm-date>-->
        <!--<jemm-date class="endDate" label="" serverurl="/job/update" dbcolumn="agreed_end_date"-->
        <!--&gt;</jemm-date>-->
        <!--<button class="btn btn-sm btn-primary">Accept</button>-->
        <!--<button class="btn btn-sm btn-primary">Edit</button>-->
        <!--<a href="#">Sub Info</a>-->
        <!--<div class="price">Price</div>-->
        <!--</div>-->
        <!--</div>-->
        <!--<div class="total">-->
        <!--<label>Total {{ totalPrice }}</label>-->
        <!--</div>-->
    </div>
    </div>
</template>

<script>
  import JemmDate from './JemmDate.vue'

  export default {
    name: 'CurrentTasksForJob',
    components: {
      JemmDate
    },
    data () {
      return {
        showDetails: [
          {show: true},
          {show: false},
          {show: false},
          {show: false},
          {show: false}
        ]
      }
    },
    props: {
      allTasks: {
        type: Array
      },
      user: {
        type: String
      }
    },
    computed: {
      totalPrice () {
        let totalPrice = 0
        for (let i = 0; i < this.allTasks.length; i++) {
          totalPrice = totalPrice + this.allTasks[0].price
        }
        return totalPrice
      },
      getUser () {
        console.log (this.user)
        console.log (typeof this.user)
        return this.user
      }
    }
  }
</script>

<style scoped>

    .wrapper1 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }

    .joblist {
        margin-left: 36rem;
        margin-right: 36rem;
    }

    .subwrapper {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }

    .customer {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contractor {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contcustpricelabel{
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .contcustprice{
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contsubpricelabel{
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contsubprice{
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custpricelabel{
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .custprice{
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .subpricelabel{
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .subprice{
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custaccepted{
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .subaccepted{
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contcustaccepted{
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contsubaccepted{
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custstartdate{
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .custenddate{
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .substartdate{
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .subenddate{
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 4;
        grid-column-end: 5;
    }

</style>
