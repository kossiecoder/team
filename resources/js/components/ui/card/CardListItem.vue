<template>
    <v-card
        outlined
        hover
        class="pa-3"
        @click="pushTo"
    >
        <slot></slot>
    </v-card>
</template>

<script>
    export default {
        props: {
            to: {
                type: Object,
                required: true
            }
        },

        methods: {
            pushTo() {
                this.$router.push(this.to)
            }
        }
    }
</script>
