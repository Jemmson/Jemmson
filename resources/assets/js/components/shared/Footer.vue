<template>
<div class="container-fluid border-top">
    <div class="row mt-2 align-items-center justify-content-center">
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('home')" class="item mx-auto text-center" :class="page === '/home' ? 'text-primary' : 'text-secondary'">
                <i class="fas fa-home sm-icon"></i>
                <figcaption class="caption small-header" :class="page === '/home' ? 'text-primary' : 'text-secondary'">Home</figcaption>
            </figure>
        </div>
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('bids')" class="item mx-auto text-center" :class="page === '/bids' ? 'text-primary' : 'text-secondary'">
                <i class="fas fa-briefcase sm-icon"></i>
                <figcaption class="caption small-header" :class="page === '/bids' ? 'text-primary' : 'text-secondary'">Jobs</figcaption>
            </figure>
        </div>
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('invoices')" class="item mx-auto text-center" :class="page === '/invoices' ? 'text-primary' : 'text-secondary'">
                <i class="fas fa-file-alt sm-icon"></i>
                <figcaption class="caption small-header" :class="page === '/invoices' ? 'text-primary' : 'text-secondary'">Invoices</figcaption>
            </figure>
        </div>
        <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <i class="fas fa-plus-circle text-secondary sm-icon"></i>
                <figcaption class="caption small-header">New Job</figcaption>
            </figure>
        </div>
    </div>
</div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        computed: {
            ...mapState({
                page: state => state.page,
            })
        },
        methods: {
            goTo (to) {
                this.$router.push(to);
            }
        },
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
