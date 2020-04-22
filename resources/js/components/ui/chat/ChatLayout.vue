<template>
    <div class="d-flex flex-grow-1 fill-height">
        <div class="scroll flex xs2">
            <slot></slot>
        </div>
        <div class="flex-grow-1">
            <router-view :key="$route.params.id"></router-view>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>
