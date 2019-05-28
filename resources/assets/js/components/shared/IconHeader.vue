<template>
    <div class="row">
        <div class="col-12">
            <img :src="url" alt="" srcset="" class="pr-1 float-left">
            <div class="ml-4 pl-1" style="margin-top: -2px;">
                <h4 class="mb-0">{{mainHeader}}</h4>
                <p v-if="subHeader !== undefined" class="subHeader">{{subHeader}}</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['icon', 'mainHeader', 'subHeader'],
        computed: {
            url () {
                return "/img/" + this.icon + ".svg";
            }
        },
    }
</script>

<style lang="less">
</style>
