<template>
    <div class="app-wrapper">
        <AppHeader />
        <v-content style="height: 100%;">
            <slot></slot>
        </v-content>
    </div>
</template>

<script>
    import AppHeader from "@/components/common/AppHeader";

    export default {
        components: {
            AppHeader
        }
    }
</script>


<style scoped>
    .app-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
</style>

