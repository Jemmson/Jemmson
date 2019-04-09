<template>
<div class="container-fluid border-top">
    <div class="row mt-2 align-items-center justify-content-center">
        <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <img class="sm-icon" src="/img/home.svg"/>
                <figcaption class="caption small-header">Home</figcaption>
            </figure>
            <!-- <img class="sm-icon d-block mx-auto" src="/img/home.svg" alt="home button" srcset="">
            <p class="small-header">Home</p> -->
        </div>
        <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <img class="sm-icon" src="/img/jobs.svg"/>
                <figcaption class="caption small-header">Jobs</figcaption>
            </figure>
            <!-- <img class="sm-icon d-block mx-auto" src="/img/jobs.svg" alt="home button" srcset="">
            <p class="small-header">Jobs</p> -->
        </div>
        <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <img class="sm-icon" src="/img/invoices.svg"/>
                <figcaption class="caption small-header">Invoices</figcaption>
            </figure>
            <!-- <img class="sm-icon d-block mx-auto" src="/img/invoices.svg" alt="home button" srcset="">
            <p class="small-header">Invoices</p> -->
        </div>
        <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <img class="sm-icon" src="/img/plus.svg"/>
                <figcaption class="caption small-header">New Job</figcaption>
            </figure>
            <!-- <img class="sm-icon mx-auto" src="/img/plus.svg" alt="home button" srcset="">
            <p class="small-header">New Job</p> -->
        </div>
    </div>
</div>
</template>

<script>
    export default {
    }
</script>

<style lang="less" scoped>
.container-fluid {
    background-color: #ffffff;
    position:fixed;
    bottom:0;
    height: 60px;
}
.row {
    height: 60px;
}
.col {
    height: 60px;
}
div.item {
    vertical-align: top;
    display: inline-block;
    text-align: center;
    width: 60px;
}
.caption {
    display: block;
}
</style>
