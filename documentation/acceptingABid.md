1. add a sub
    - route - GeneralContractor.sendSubInviteToBidOnTask -> /task/notify -> TaskController@notify
2. jobTask.vue
    - show added subs
        - 3 statuses
            - accepted
                - bid.accepted === 1
            - added but no bid has been sent by sub
            - bid has not been accepted but a bid has been sent by a sub
        
        