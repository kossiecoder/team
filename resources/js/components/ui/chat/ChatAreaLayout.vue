<template>
    <div class="d-flex flex-column fill-height flex-grow-1">
        <slot name="content"></slot>
        <div class="flex-grow-0 pa-2">
            <slot name="input"></slot>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>
