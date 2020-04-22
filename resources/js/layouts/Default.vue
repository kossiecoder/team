<template>
    <div>
        <AppHeader />
        <v-content>
            <slot></slot>
        </v-content>
        <MiniChat />
    </div>
</template>

<script>
    import MiniChat from "@/components/mini-chat/MiniChat";
    import AppHeader from "@/components/common/AppHeader";

    export default {
        components: {
            AppHeader,
            MiniChat
        }
    }
</script>
