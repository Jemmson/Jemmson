<template>
<div class="container-fluid border-top" v-if="page !== '/' || user.usertype !== undefined">
    <div v-if="isLoggedIn" class="row mt-2 align-items-center justify-content-center">
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('/home')" class="item mx-auto text-center" :class="isCurrentPage('/home', '/home/')">
                <i class="fas fa-home sm-icon"></i>
                <figcaption class="caption small-header" :class="isCurrentPage('/home', '/home/')">Home</figcaption>
            </figure>
        </div>
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('/bids')" class="item mx-auto text-center" :class="isCurrentPage('/bids', '/bids/')">
                <i class="fas fa-briefcase sm-icon"></i>
                <figcaption class="caption small-header" :class="isCurrentPage('/bids', '/bids/')">Jobs</figcaption>
            </figure>
        </div>
        <div class="col d-flex align-items-center">
            <figure @click.prevent="goTo('/invoices')" class="item mx-auto text-center" :class="isCurrentPage('/invoices', '/invoices/')">
                <i class="fas fa-file-alt sm-icon"></i>
                <figcaption class="caption small-header" :class="isCurrentPage('/invoices', '/invoices/')">Receipts</figcaption>
            </figure>
        </div>
        <div v-if="userType === 'contractor'" class="col d-flex align-items-center">
            <figure @click.prevent="goTo('/initiate-bid')" class="item mx-auto text-center" :class="isCurrentPage('/initiate-bid', '/initiate-bid/')">
                <i class="fas fa-plus-circle sm-icon"></i>
                <figcaption class="caption small-header" :class="isCurrentPage('/initiate-bid', '/initiate-bid/')">New Job</figcaption>
            </figure>
        </div>
        <div v-if="userType === 'customer'" class="col d-flex align-items-center">
            <a class="mx-auto" href="/settings">
            <figure class="item text-center" :class="isCurrentPage('/settings#', '/settings#/')">
                <i class="fas fa-cog text-secondary sm-icon"></i>
                <figcaption class="caption small-header" :class="isCurrentPage('/settings#', '/settings#/')">Settings</figcaption>
            </figure>
            </a>
        </div>
    </div>
    <div v-else class="row mt-2 align-items-center justify-content-center">
        <!-- <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <i class="fas fa-home sm-icon"></i>
                <figcaption class="caption small-header">Home</figcaption>
            </figure>
        </div> -->
        <!-- <div class="col d-flex align-items-center">
            <figure class="item mx-auto text-center">
                <i class="fas fa-briefcase sm-icon"></i>
                <figcaption class="caption small-header">Jobs</figcaption>
            </figure>
        </div> -->
        <div class="col d-flex align-items-center">
            <a href="/#" class="mx-auto">
            <figure class="item mx-auto text-center">
                <i class="fas fa-sign-in-alt text-secondary sm-icon"></i>
                <figcaption class="caption small-header">Login</figcaption>
            </figure>
            </a>
        </div>
        <div class="col d-flex align-items-center">
            <a href="/#/register" class="mx-auto">
            <figure class="item mx-auto text-center" :class="page === '/#/register' ? 'text-primary' : 'text-secondary'">
                <i class="fas fa-user-plus text-secondary sm-icon"></i>
                <figcaption class="caption small-header" :class="page === '/#/register' ? 'text-primary' : 'text-secondary'">Register</figcaption>
            </figure>
            </a>
        </div>
    </div>
</div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        props: ['user'],
        computed: {
            ...mapState({
                page: state => state.page,
                userType () {
                    if (this.user !== undefined && this.user !== null) {
                        return this.user.usertype;
                    }
                    return null;
                },
                isLoggedIn () {
                    return this.user !== undefined && this.user !== null;
                }
            })
        },
        methods: {
            goTo (to) {
                if (window.location.pathname === '/settings') {
                    window.location.href = '/#' + to;
                } else {
                    this.$router.push(to);
                }
            },
            isCurrentPage (a, b) {
                if (this.page === a || this.page === b) {
                    return 'text-primary';
                }
                return 'text-secondary';
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
