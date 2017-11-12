<template>
    <div class="wrapper">
        <div class="taskName header">Task Name</div>
        <div class="price header">Task Price</div>
        <div v-for="task in allTasks">
            <div class="taskName">{{ task.taskName }}</div>
            <div class="price">{{ task.price }}</div>
        </div>
        <div class="total">
            <label>Total {{ totalPrice }}</label>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'CurrentTasksForJob',
    props: {
      allTasks: {
        type: Array
      }
    },
    computed: {
      totalPrice () {
        let totalPrice = 0
        for (let i = 0; i < this.allTasks.length; i++) {
          totalPrice = totalPrice + this.allTasks[0].price
        }
        return totalPrice
      }
    }
  }
</script>

<style>

</style>
