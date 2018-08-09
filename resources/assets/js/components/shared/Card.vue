<template>
    <div class="card flex-col">
        <div class="flex flex-col border-b p-4 card-header" v-if="header !== undefined && header">
            <slot name="card-header" class="text-center"></slot>
        </div>
        <div class="flex flex-col p-6 card-body">
            <!-- default content goes here -->
            <slot></slot>
        </div>
        <div class="flex card-footer">
            <slot name="card-footer"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: ['header']
}
</script>

