Happy Path

#Step 1 - Contractor Registers for the site

## User
A contractor would register for the site. The contractor would put in all of their business information. 

## System
Once the contractor is registered then they would be redirected to the home page. The home page would then make calls
to pull back all Jobs, Tasks, and Invoices for this given contractor

 - Request URL: http://localhost:9500/user/current
   Request Method: GET
   `{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":5,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","accounting_software":null,"stripe_express":null,"location":{"id":1,"user_id":1,"default":0,"address_line_1":"705 E Oxford Dr.","address_line_2":"","city":"Tempe","state":"AL","zip":"85283","area":null,"country":"AF","created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","lat":null,"long":null}},"customer":null,"tax_rate":0}`
 
 - Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`
   
 - Request URL: http://localhost:9500/jobs
   Request Method: GET
   
 - Request URL: http://localhost:9500/bid/tasks
   Request Method: POST
   
 - Request URL: http://localhost:9500/invoices
   Request Method: GET
 
 - Request URL: https://m.stripe.com/4
   Request Method: POST

# Step 2 - Contractor Initiates a Bid

  - Request URL: http://localhost:9500/checkAuth
    Request Method: GET
    
   - Request URL: https://m.stripe.com/4
     Request Method: POST
     
   Contractor begins to type in the first name of a customer and then there is a drop down of names
   
   - Request URL: http://localhost:9500/customer/search?query=Shawn+Pike
     Request Method: GET
     
   
   Contractor types in a phone number and the number needs to be validated
   
   - Request URL: http://localhost:9500/api/user/validatePhoneNumber
     Request Method: POST
     
     req -> {num: "4807034902"}
     response -> ["success", "mobile", "mobile"]
     
   
   Contractor hits submit to initiate the job
   
###initiate-bid
 - Request URL: http://localhost:9500/initiate-bid
   Request Method: POST
   
          req -> { busy: false
                 customerName: "Shawn Pike"
                 email: ""
                 errors: {errors: {}}
                 errors: {}
                 firstName: "Shawn"
                 jobName: "pool job"
                 lastName: "Pike"
                 phone: "(480) 703-4902"
                 quickbooks_id: ""
                 successful: false }
          response -> Bid was created
     
# Step 3 A -> Contractor is navigated to the Jobs Page

###jobs       
  - Request URL: http://localhost:9500/jobs
     Request Method: GET

     response -> [{"id":1,"customer_id":2,"contractor_id":1,"location_id":null,"job_name":"pool job","status":"bid.initiated","bid_price":0,"completed_bid_date":null,"agreed_start_date":null,"agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[],"customer":{"id":2,"customer":{"id":1,"user_id":2,"location_id":null,"email_method_of_contact":null,"phone_method_of_contact":null,"sms_method_of_contact":null,"notes":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59"},"tax_rate":0}}]
   
   - Request URL: https://m.stripe.com/4
     Request Method: POST
         
# Step 3 B -> Customer receives a text to the job that was just initiated

    

# Step 4 -> Contractor selects a job to see in more details

Selects the job and goes to the Job.vue page

- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`

- Request URL: http://localhost:9500/job/1
  Request Method: GET
  response -> {"id":1,"customer_id":2,"contractor_id":1,"location_id":null,"job_name":"pool job","status":"bid.initiated","bid_price":0,"completed_bid_date":null,"agreed_start_date":null,"agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[],"location":null,"contractor":{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null},"tax_rate":0},"customer":{"id":2,"name":"Shawn Pike","customer":{"id":1,"user_id":2,"location_id":null,"email_method_of_contact":null,"phone_method_of_contact":null,"sms_method_of_contact":null,"notes":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59"},"tax_rate":0}}

- Request URL: http://localhost:9500/user/current -> seems to be called 3 times
  Request Method: GET
  response -> {"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null,"stripe_express":null,"location":{"id":1,"user_id":1,"default":0,"address_line_1":"705 E Oxford Dr.","address_line_2":"","city":"Tempe","state":"AL","zip":"85283","area":null,"country":"AF","created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","lat":null,"long":null}},"customer":null,"tax_rate":0}

- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
      
# Step 5 -> Contractor wants to add a task

Contractor selects add task button and gets routed to AddJobTask.vue page
    
- Request URL: http://localhost:9500/checkAuth
       Request Method: GET
       `{"auth":true}`
        
- Request URL: http://localhost:9500/job/1
  Request Method: GET
  
  response -> {"id":1,"customer_id":2,"contractor_id":1,"location_id":null,"job_name":"pool job","status":"bid.initiated","bid_price":0,"completed_bid_date":null,"agreed_start_date":null,"agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[],"location":null,"contractor":{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null},"tax_rate":0},"customer":{"id":2,"name":"Shawn Pike","customer":{"id":1,"user_id":2,"location_id":null,"email_method_of_contact":null,"phone_method_of_contact":null,"sms_method_of_contact":null,"notes":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59"},"tax_rate":0}}
   
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  
### Types in a name of a task

Searching for a Task

- Request URL: http://localhost:9500/search/task
  Request Method: POST
  request {"taskname":"Clean Tile","jobId":1}
  
### hit add task
- Request URL: http://localhost:9500/spark/token
  Request Method: PUT
  response -> Refreshed.

- Request URL: http://localhost:9500/task/addTask
  Request Method: POST
  
  request -> {"area":"","assetAccountRef":{"value":"0","name":"Inventory Asset"},"contractorId":1,"createNew":true,"customer_id":2,"customer_message":"ASDSSADDAS","expenseAccountRef":{"value":"0","name":"Cost of Goods Sold"},"hasQtyUnitError":false,"hasStartDateError":false,"incomeAccountRef":{"value":"0","name":"Sales of Product Income"},"item_id":"","invStartDate":"","jobId":1,"qty":"120","qtyOnHand":"0","qtyUnit":"ft","qtyUnitErrorMessage":"","start_date":"2019-09-17","start_when_accepted":true,"startDateErrorMessage":"","sub_message":"DSADSADSADSA","subTaskPrice":"0","taskExists":"","taskId":0,"taskPrice":"3","taskName":"Clean Tile","trackQtyOnHand":true,"type":"Inventory","updateTask":false,"useStripe":false,"errors":{"errors":{}},"busy":true,"successful":false}
  response -> [{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS","pivot":{"job_id":1,"task_id":1,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38"}}]
  
# Step 6 -> Contractor hits back and then goes to the job page
- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`

- Request URL: http://localhost:9500/job/1
  Request Method: GET
  response -> {"id":1,"customer_id":2,"contractor_id":1,"location_id":null,"job_name":"pool job","status":"bid.initiated","bid_price":0,"completed_bid_date":null,"agreed_start_date":null,"agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[],"location":null,"contractor":{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null},"tax_rate":0},"customer":{"id":2,"name":"Shawn Pike","customer":{"id":1,"user_id":2,"location_id":null,"email_method_of_contact":null,"phone_method_of_contact":null,"sms_method_of_contact":null,"notes":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59"},"tax_rate":0}}

- Request URL: http://localhost:9500/user/current -> seems to be called 3 times
  Request Method: GET
  response -> {"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null,"stripe_express":null,"location":{"id":1,"user_id":1,"default":0,"address_line_1":"705 E Oxford Dr.","address_line_2":"","city":"Tempe","state":"AL","zip":"85283","area":null,"country":"AF","created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","lat":null,"long":null}},"customer":null,"tax_rate":0}

- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  

# Step 7 -> Contractor wants to view the tasks they have created. Contractor selects View Tasks
Contractor goes to the JobTasks page

- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`
   
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  

#Step 8 Contractor selects a task to view and goes to the JobTask Page

- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`
   
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  
- Request URL: http://localhost:9500/spark/token
  Request Method: PUT
  response -> Refreshed.
  

### Contractor wants to update the task start date

- Request URL: http://localhost:9500/api/task/updateTaskStartDate
  Request Method: POST
  request -> {"date":"2019-09-17","jobTaskId":1}
  response -> no response seems to be returned from this
  
- Request URL: http://localhost:9500/getJobTaskForGeneral/1/1
  Request Method: GET
  response -> [{"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":360,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:07:23","stripe_transfer_id":null,"customer_message":"ASDSSADDAS","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":3,"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"},"images":[],"bid_contractor_job_tasks":[]}]
  
- Request URL: http://localhost:9500/spark/token
  Request Method: PUT
  response -> Refreshed.
  

### Contractor wants to update the price for the task

- Request URL: http://localhost:9500/api/task/updateCustomerPrice
  Request Method: POST
  request -> {"jobId":1,"jobTaskId":1,"price":"5"}
  response -> {"price":500,"taskId":null}
  
- Request URL: http://localhost:9500/getJobTaskForGeneral/1/1
  Request Method: GET
  response -> [{"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":360,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:07:23","stripe_transfer_id":null,"customer_message":"ASDSSADDAS","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":3,"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"},"images":[],"bid_contractor_job_tasks":[]}]
  
- Request URL: http://localhost:9500/spark/token
  Request Method: PUT
  response -> Refreshed.
  

### Contractor wants to add an image

- Request URL: http://localhost:9500/task/image
    Request Method: POST
    request -> Form Data
        ------WebKitFormBoundaryfNOzGFb2yRYjlWtI
        Content-Disposition: form-data; name="photo"; filename="RenderedI.jpg"
        Content-Type: image/jpeg
    response -> https://res.cloudinary.com/jemmson-inc/image/upload/v1568686532/tasks/xYOrea1gMxYp4YKigQvSGDAMCimgyExGsE0FYET5.jpeg.jpg

### Contractor wants to view the images for the task. Contractor gets routed to the TaskImages Page


- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`

- Request URL: http://localhost:9500/jobtask/1
  Request Method: GET
  response -> {"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":60000,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:21:23","stripe_transfer_id":null,"customer_message":"ASDSSADDASasasasas","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":500,"images":[{"id":1,"job_id":1,"job_task_id":1,"public_id":"tasks\/xYOrea1gMxYp4YKigQvSGDAMCimgyExGsE0FYET5.jpeg","version":1568686532,"signature":"d9bb10181efe3f6da1c927032a80a8d7f5618648","width":1745,"height":2048,"format":"jpg","resource_type":"image","bytes":745697,"type":"upload","etag":"1b6020adde675a9d6ca2dbc2f39f0c41","placeholder":0,"url":"https:\/\/res.cloudinary.com\/jemmson-inc\/image\/upload\/v1568686532\/tasks\/xYOrea1gMxYp4YKigQvSGDAMCimgyExGsE0FYET5.jpeg.jpg","secure_url":"https:\/\/res.cloudinary.com\/jemmson-inc\/image\/upload\/v1568686532\/tasks\/xYOrea1gMxYp4YKigQvSGDAMCimgyExGsE0FYET5.jpeg.jpg","overwritten":null,"original_filename":"phpoE60Ou","created_at":"2019-09-17 02:15:31","updated_at":"2019-09-17 02:15:31"}],"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"}}
  
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  
### Contractor wants to delete an image

- Request URL: http://localhost:9500/task/image/1
  Request Method: DELETE
  
- Request URL: http://localhost:9500/jobtask/1
  Request Method: GET
  response -> {"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":60000,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:21:23","stripe_transfer_id":null,"customer_message":"ASDSSADDASasasasas","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":500,"images":[],"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"}}

### CONTRACTOR wants to go back to the task page

- Request URL: http://localhost:9500/checkAuth
   Request Method: GET
   `{"auth":true}`

### Contractor wants to update a message

- Request URL: http://localhost:9500/api/task/updateMessage/
  Request Method: POST
  Status Code: 200 OK
  request -> {"message":"ASDSSADDASasasasas","jobTaskId":1,"actor":"customer"}
  response -> no response seems to be returned from this
  
- Request URL: http://localhost:9500/getJobTaskForGeneral/1/1
  Request Method: GET
  response -> [{"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":360,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:07:23","stripe_transfer_id":null,"customer_message":"ASDSSADDAS","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":3,"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"},"images":[],"bid_contractor_job_tasks":[]}]
  
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  

### Contractor wants to add a sub to the task and clicks the add sub button

Sub invite modal pops up

Sub wants to type in a sub contractors name so the sub will come down

- Request URL: http://localhost:9500/search/Garden%20Bud
  Request Method: GET


### Contractor puts in the mobile number

- Request URL: http://localhost:9500/api/user/validatePhoneNumber
  Request Method: POST
  response -> ["success","mobile","mobile"]
  

### Contractor hits submit to invite a sub

- Request URL: http://localhost:9500/task/notify
  Request Method: POST
  request -> {"task_id":0,"email":"sfranchuk@cox.net","phone":"6024326933","counter":0,"name":"","firstName":"Susan","lastName":"Franchuk","givenName":"","familyName":"","quickbooksId":"","companyName":"Sue the tax lady","paymentType":"stripe","errors":{"errors":{}},"busy":true,"successful":false,"jobTaskId":1}
  response -> No response

- Request URL: http://localhost:9500/getJobTaskForGeneral/1/1
  Request Method: GET
  response -> [{"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":600,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:21:23","stripe_transfer_id":null,"customer_message":"ASDSSADDASasasasas","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":5,"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":300,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS"},"images":[],"bid_contractor_job_tasks":[{"id":1,"contractor_id":3,"job_task_id":1,"bid_price":0,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","status":"bid_task.initiated","proposed_start_date":null,"bid_description":null,"accepted":0,"payment_type":null,"contractor":{"id":3,"location_id":null,"name":"Kristen Battafarano","email":"kbattafarano@gmail.com","usertype":"contractor","password_updated":0,"photo_url":"https:\/\/www.gravatar.com\/avatar\/65f6dbf56086502c60436432c6fd2f2f.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"6023508801","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","first_name":"Kristen","last_name":"Battafarano","contractor":{"id":2,"user_id":3,"location_id":null,"free_jobs":5,"company_name":"Garden Bud","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","accounting_software":null},"tax_rate":0}},{"id":2,"contractor_id":4,"job_task_id":1,"bid_price":0,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","status":"bid_task.initiated","proposed_start_date":null,"bid_description":null,"accepted":0,"payment_type":null,"contractor":{"id":4,"location_id":null,"name":"Susan Franchuk","email":"sfranchuk@cox.net","usertype":"contractor","password_updated":0,"photo_url":"https:\/\/www.gravatar.com\/avatar\/c31858f3d80e0e4840e1b05b0fd84c26.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"6024326933","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","first_name":"Susan","last_name":"Franchuk","contractor":{"id":3,"user_id":4,"location_id":null,"free_jobs":5,"company_name":"Sue the tax lady","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","accounting_software":null},"tax_rate":0}}]}]
  

### Contractor hits back to go to the tasks
- Request URL: http://localhost:9500/job/1
  Request Method: GET
  response -> {"id":1,"customer_id":2,"contractor_id":1,"location_id":null,"job_name":"pool job","status":"bid.in_progress","bid_price":60000,"completed_bid_date":null,"agreed_start_date":"2019-09-17 00:00:00","agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-17 02:13:06","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[{"id":1,"job_id":1,"task_id":1,"bid_id":null,"location_id":null,"contractor_id":1,"status":"bid_task.initiated","cust_final_price":600,"sub_final_price":0,"start_when_accepted":0,"stripe":0,"start_date":"2019-09-17 00:00:00","deleted_at":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 02:21:23","stripe_transfer_id":null,"customer_message":"ASDSSADDASasasasas","sub_message":"DSADSADSADSA","qty":120,"sub_sets_own_price_for_job":1,"declined_message":null,"unit_price":5,"task":{"id":1,"name":"clean tile","standard_task_id":null,"contractor_id":1,"proposed_cust_price":3,"average_cust_price":null,"proposed_sub_price":0,"average_sub_price":null,"description":null,"fully_qualified_name":null,"unit_price":null,"type":null,"payment_method_ref":null,"avg_cost":null,"item_id":null,"created_at":"2019-09-17 01:57:38","updated_at":"2019-09-17 01:57:38","qtyUnit":"ft","sub_instructions":"DSADSADSADSA","customer_instructions":"ASDSSADDAS","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null}},"bid_contractor_job_tasks":[{"id":1,"contractor_id":3,"job_task_id":1,"bid_price":0,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","status":"bid_task.initiated","proposed_start_date":null,"bid_description":null,"accepted":0,"payment_type":null,"contractor":{"id":3,"location_id":null,"name":"Kristen Battafarano","email":"kbattafarano@gmail.com","usertype":"contractor","password_updated":0,"photo_url":"https:\/\/www.gravatar.com\/avatar\/65f6dbf56086502c60436432c6fd2f2f.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"6023508801","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","first_name":"Kristen","last_name":"Battafarano","contractor":{"id":2,"user_id":3,"location_id":null,"free_jobs":5,"company_name":"Garden Bud","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-17 03:11:13","updated_at":"2019-09-17 03:11:13","accounting_software":null},"tax_rate":0}},{"id":2,"contractor_id":4,"job_task_id":1,"bid_price":0,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","status":"bid_task.initiated","proposed_start_date":null,"bid_description":null,"accepted":0,"payment_type":null,"contractor":{"id":4,"location_id":null,"name":"Susan Franchuk","email":"sfranchuk@cox.net","usertype":"contractor","password_updated":0,"photo_url":"https:\/\/www.gravatar.com\/avatar\/c31858f3d80e0e4840e1b05b0fd84c26.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"6024326933","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":null,"trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","first_name":"Susan","last_name":"Franchuk","contractor":{"id":3,"user_id":4,"location_id":null,"free_jobs":5,"company_name":"Sue the tax lady","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-17 03:13:22","updated_at":"2019-09-17 03:13:22","accounting_software":null},"tax_rate":0}}],"location":null,"images":[]}],"location":null,"contractor":{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 23:16:59","accounting_software":null},"tax_rate":0},"customer":{"id":2,"name":"Shawn Pike","customer":{"id":1,"user_id":2,"location_id":null,"email_method_of_contact":null,"phone_method_of_contact":null,"sms_method_of_contact":null,"notes":null,"created_at":"2019-09-16 23:16:59","updated_at":"2019-09-16 23:16:59"},"tax_rate":0}}

- Request URL: http://localhost:9500/user/current -> does this 3 times for some reason
  Request Method: GET
  
### Contractor hits back to go to the Job

- Request URL: http://localhost:9500/user/current -> does this 3 times for some reason
  Request Method: GET
  
- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  
### Contractor wants to connect to stripe

- Request URL: https://m.stripe.com/4
  Request Method: POST
  b7c9c453-885b-4ac6-add9-df4d6a5d4e82
  
### Contractor wants to submit the bid

- Request URL: http://localhost:9500/api/task/finishedBidNotification
  Request Method: POST
  request ->  { jobId: bid.id, customerId: bid.customer_id }
  response -> no response
  
### Customer gets a text and clicks on text goes to the Further Info page

- Request URL:
  Request Method: POST
  {
    address_line_1: "705 E Oxford Dr"
    address_line_2: ""
       busy: true
    city: "Tempe"
    company_name: "Garden Bud"
    email: "kbattafarano@gmail.com"
    email_contact: true
       errors: {errors: {}}
    first_name: "Kristen"
    last_name: "Battafarano"
    name: "Battafarano 6023508801"
    notes: ""
    password: "asdasd"
    password_confirmation: "asdasd"
    phone_contact: false
    hone_number: "6023508801"
    sms_text: false
    state: "AZ"
       successful: false
    zip: "85283"
  }
    Response: "\/#\/bids"


- Request URL:http://127.0.0.1:9500/checkAuth
  Request method:GET
  {"auth":true}
  
- Request URL:https://m.stripe.com/4
  Request method:POST
  b267c381-e884-4966-a911-ed9e820f9fb9
  
- Request URL:http://127.0.0.1:9500/api/user/validatePhoneNumber
  Request method:POST
  ["success","mobile","mobile","alreadyExists"]

- Request URL:http://127.0.0.1:9500/spark/token
  Request method:PUT
  Refreshed.
  
### Customer submits further info and goes to the bid page

- Request URL:http://127.0.0.1:9500/home
  Request method:POST
  req -> {"email":"pike.shawn@gmail.com","name":"Shawn Pike","company_name":"","phone_number":"4807034902","address_line_1":"705 E Oxford Dr","address_line_2":"","first_name":"Shawn","last_name":"Pike","city":"Tempe","state":"AZ","zip":"85283","notes":"asdkjl. sajsdjksadjksd.\nsdklldskslka","password":"asdasd","password_confirmation":"asdasd","email_contact":true,"phone_contact":false,"sms_text":false,"errors":{"errors":{}},"busy":true,"successful":false}
  resp -> "\/#\/bid\/1"

- Request URL:http://127.0.0.1:9500/checkAuth
    Request method:GET
    {"auth":true}
    
- Request URL: http://localhost:9500/user/current
   Request Method: GET
   `{"id":1,"location_id":1,"name":"Shawn Pike","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","first_name":"Shawn","last_name":"Pike","subscriptions":[],"contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":5,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","accounting_software":null,"stripe_express":null,"location":{"id":1,"user_id":1,"default":0,"address_line_1":"705 E Oxford Dr.","address_line_2":"","city":"Tempe","state":"AL","zip":"85283","area":null,"country":"AF","created_at":"2019-09-16 22:44:40","updated_at":"2019-09-16 22:44:40","lat":null,"long":null}},"customer":null,"tax_rate":0}`
 
- Request URL: http://localhost:9500/job/1
  Request Method: GET
  {"id":1,"customer_id":2,"contractor_id":1,"location_id":2,"job_name":"pool work","status":"bid.initiated","bid_price":0,"completed_bid_date":null,"agreed_start_date":null,"agreed_end_date":null,"actual_end_date":null,"deleted_at":null,"created_at":"2019-09-17 15:58:33","updated_at":"2019-09-18 01:15:02","declined_message":null,"paid_with_cash_message":null,"qb_estimate_id":"NULL","job_tasks":[],"location":{"id":2,"user_id":2,"default":1,"address_line_1":"705 E Oxford Dr","address_line_2":"","city":"Tempe","state":"AZ","zip":"85283","area":"Tempe","country":null,"created_at":"2019-09-18 01:15:02","updated_at":"2019-09-18 01:16:36","lat":null,"long":null},"contractor":{"id":1,"location_id":1,"name":"Jem Son","email":"jemmsoninc@gmail.com","usertype":"contractor","password_updated":1,"photo_url":"https:\/\/www.gravatar.com\/avatar\/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm","logo_url":null,"uses_two_factor_auth":false,"phone":"4806226441","two_factor_reset_code":null,"current_team_id":null,"stripe_id":null,"current_billing_plan":null,"billing_state":"AL","trial_ends_at":null,"last_read_announcements_at":null,"created_at":"2019-09-17 15:56:56","updated_at":"2019-09-17 15:56:56","first_name":"Jem","last_name":"Son","contractor":{"id":1,"user_id":1,"location_id":1,"free_jobs":4,"company_name":"Jemmson","company_logo_name":null,"email_method_of_contact":null,"sms_method_of_contact":null,"phone_method_of_contact":null,"created_at":"2019-09-17 15:56:56","updated_at":"2019-09-17 15:58:33","accounting_software":null},"tax_rate":0},"customer":{"id":2,"name":"Shawn Pike","customer":{"id":1,"user_id":2,"location_id":2,"email_method_of_contact":"1","phone_method_of_contact":"0","sms_method_of_contact":"0","notes":"asdkjl. sajsdjksadjksd.\nsdklldskslka","created_at":"2019-09-17 15:58:33","updated_at":"2019-09-18 01:16:36"},"tax_rate":0}}
  
