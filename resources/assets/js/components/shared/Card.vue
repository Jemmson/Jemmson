<template>
    <div class="card">
        <div class="card-header" v-if="header !== undefined && header">
            {{ header }}
        </div>
        <div class="card-body">
            <h5 class="card-title" v-if="title !== undefined && title">{{ title }}</h5>
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['header', 'title']
    }
</script>