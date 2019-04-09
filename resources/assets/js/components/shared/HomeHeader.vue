<template>
    <div class="row custom-row">
        <div class="col-12 pt-3" style="height: 40px;">
            <img src="/img/search.svg" class="icon sm-icon float-left" alt="search icon" srcset="">
            <img src="/img/notification.svg" class="icon sm-icon float-right" alt="add new item icon" srcset="">
        </div>
        <div class="col-12">
            <img class="profile-pic float-left ml-2" src="http://infinisolutionslk.com/wp-content/uploads/2018/09/profilephotocircle.png" alt="profile pic">
            <div class="profile-details">
                <h4>Jane Doe</h4>
                <div class="row">
                    <div class="col pr-0">
                        <img class="float-left" src="/img/edit.svg" alt="">
                        <p>Edit Profile</p>
                    </div>
                    <div class="col pl-0">
                        <img class="float-left" src="/img/Logout.svg" alt="">
                        <p>Log Out</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
    }
</script>

<style lang="less" scoped>
.custom-row {
    background-color: #4D7CFE;
    height: 152px;
    margin-bottom: 4rem;
}

.profile-pic {
    margin-top: 57px;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.5);
}
.profile-details {
    margin-top: 52px;
    padding-left: 8rem;
}
p {
    font-size: 12px;
    font-weight: 600px;
    margin-top: -3px;
    color: #ffffff;
    padding-left: 1rem;
}
h4 {
    font-size: 23px;
    color: #ffffff;
    font-weight: 600;
}

</style>

