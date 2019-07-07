#Initiate Bid Vue

###Dropdown

This drop down will pull back users under these conditions
1. the contractors password has been updated and they have been through the 
    further info page if needed
2. A user that is in the User table that is a customer
3. The user has been associated with the contractor. meaning the user has been
   initiated by the contractor before. the user would exist in the contractor_customer
   table for that contractor
4. It will also pull back users that are in the quickbooks_customer table