<template>
    <v-btn
        outlined
        exact
        :to="to"
    >
        <v-icon left>
            mdi-arrow-left
        </v-icon>
        Back
    </v-btn>
</template>

<script>
    export default {
        props: {
            to: {
                type: Object,
                required: true
            }
        }
    }
</script>
