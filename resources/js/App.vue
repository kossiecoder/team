<template>
    <v-app>
        <component :is="layout">
            <router-view />
        </component>
        <SnackBar />
    </v-app>
</template>

<script>
    const default_layout = 'default';
    import SnackBar from "@/components/common/SnackBar";

    export default {
        components: {
            SnackBar
        },

        computed: {
            layout() {
                return (this.$route.meta.layout || default_layout) + '-layout';
            }
        }
    }
</script>
