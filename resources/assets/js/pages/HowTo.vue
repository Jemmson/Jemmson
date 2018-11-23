<template>
    <div class="container">
        <h1 class="text-center text-white">How To</h1>
        <hr>

        <h3 class="text-center text-white"></h3>
        <h3 class="text-center text-white"></h3>
        <h3 class="text-center text-white"></h3>
        <h3 class="text-center text-white"></h3>
        <h3 class="text-center text-white"></h3>
        <h3 class="text-center text-white"></h3>


        <h2 class="text-center text-white">I am a Contractor</h2>
        <span class="number">1. </span><h3 class="text-center text-white">I Initiate A Bid</h3>



        <h3 class="text-center text-white">Add Task</h3>


        <p>The add task screen allows you to add a task to the job. These are not meant to be detailed materials task
            but the overall task. The overall means such things as a replumb pool equipment but does not include the
            number of fittings, amount of glue, or number of hours worked. The purpose of this is to allow a sub to bid
            on an overall total for a job but not bid for each of the direct materials, direct labor, or overhead. In
            future releases this feature will be split up to where you will be able to get a bid for an overall job and
            put in direct materials, direct labor, and overhead for each individual job.</p>

        <p>To add a task simply select the add task but on the bid page and a modal window will pop up. The only
            required field will be the task name. The rest is editable when you add the task to the job. When you add a
            task two things happen. The first is that you add a task to your standard tasks. Your standard tasks are
            tasks that are like your general tasks. This is task information that is most likely to be the case for the
            majority of your jobs. Once a standard task is created the task will also be added to your current job.</p>

        <p>If you decide to add this task to another job then you will receive a drop down menu with the name of the
            task that you added previously. The previously added task will populate with the information that you
            previously created earlier. This information is editable. If you edit this information then you will have
            two buttons show up on the bottom. These buttons will ask you to “Update and Add Task” or “Ignore changes
            and Add Task”. These buttons are asking if you want the changes to affect your general template or if you
            want the changes to just apply to the current job. Either way the task will be added to the current job.</p>

        <p>However, if you select the drop down and change the task name then you will also see a third button that says
            “Create New Task”. This button pops up because changing the name may mean that you do want a new task that
            may use some of the same information of an existing task. If select that button then you will create a new
            task otherwise you will either update the existing task or just make changes for the current job.</p>

        <p>The Price field refers to the price per unit. If the quantity value is 1 then it will apply to the entire
            unit. If the price is greater than one then it will be the price per unit and not the total price. The total
            price for the task will show up when the task is added to the job.</p>

        <p>Quantity is the total number of items that you intend to do with the job.</p>

        <p>Item Description refers to the unit description of the job. For instance, feet of lumber, feet of pipe,
            bottles of glue, etc.</p>

        <p>Sub Price refers to the minimum price that you wish for a sub to do the job for. This can be left to zero and
            the sub can simply give you the price they wish to charge. One does not always know what the current price
            for a job is and sometimes it is left to the sub who specializes in the area to make their suggestions.</p>

        <p>Start Date will default to the current date if no date is selected but otherwise will save to the selected
            date.</p>

        <p>Customer notes are meant to be information that you may wish for the customer to know about the job. You may
            need to tell them to turn the hose off or leave the gate open. You can keep updating these notes when the
            task has been added and the customer will always have the latest information.</p>

        <p>It would be best practice to have the most general information when you are creating the task and then more
            specific information when you update the task in the job. This is because when creating a task you are also
            creating the standard task and if you have the information more general then when select the task from the
            drop down menu in a future task then you will also have the general information auto populate as well.</p>

        <p>Subcontractor notes has the same behavior as the customer notes but it will be sent to all subs and the
            customer will not be able to see this information. It is best practice here to have the notes be general
            during task creation but be updated for more specificity when updating the task in the job itself.</p>

        <p>If you do not select from the drop down then the assumption will be that the task is new and you will be
            creating a new standard task and adding that task to the current job.</p>

        <p>Adding a task consists of three things.</p>

        <ol>
            <li>Creating a New task for the job</li>
            <li>Updating a Standard task that was already created</li>
            <li>Changing an existing task but not updating the standard task.</li>
        </ol>

        <h3 class="text-center text-white">Initiating a Bid</h3>

        <p>This is the first step in the workflow. Initially you will have had some contact with the customer
            that you will be sending them a bid. This can occur by phone, email, or text. The purpose of
            initiating a bid is twofold. One is to create an initial contact with them and the other is to have
            the customer input the data that is required to do the job. An overriding principle of this
            application is to limit the amount of input you have to put in. The customer will be able to to put
            in their demographics and you should not have to put in their address, phone number, and any special
            information you may need to know from them such as gate codes, animals, best times to visit, special
            instructions for paying cash, etc.</p>

        <h3 class="text-center text-white">Workflow:</h3>

        <p>Initiating a bid is as simple as logging in, selecting initiate bid, and putting in the customer's
            name, mobile phone and Job Name.</p>

        <p>Customer Name: This field is mandatory and should be as closely spelled as possible.</p>


    </div>
</template>

<script>
  export default {
    name: ''
  }
</script>

<style scoped>

    .number {
        background-color: white;
        padding: .75rem 1rem;
        font-size: 12pt;
        border-radius: 9999px;
    }

    p {
        color: white;
        margin-top: 3rem;
        margin-bottom: 3rem;
    }

    ol {
        color: white;
        margin-top: 3rem;
        margin-bottom: 3rem;
    }

</style>
