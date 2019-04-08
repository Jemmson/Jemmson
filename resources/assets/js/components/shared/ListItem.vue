<template>
    <div class="col-12 pl-1 pt-0 pr-2" :class="noDivider ? '' : 'divider'">
        <div>
            <p class="align-middle float-left">{{ left }}</p>
            <p class="align-middle float-right" v-if="right !== undefined">{{ right }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['right', 'left', 'noDivider']
    }
</script>

<style lang="less">
p {
    margin-top: 1rem;
}
</style>
