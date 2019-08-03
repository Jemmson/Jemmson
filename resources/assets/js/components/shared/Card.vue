<template>
    <div class="card">
        <div class="card-header" v-if="header !== undefined && header">
            {{ header }}
        </div>
        <div class="card-body" :class="classes">
            <h5 class="card-title" v-if="title !== undefined && title">{{ title }}</h5>
            <slot></slot>
        </div>
        <div class="card-footer">
            <slot slot="card-footer"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['header', 'title', 'classes']
    }
</script>