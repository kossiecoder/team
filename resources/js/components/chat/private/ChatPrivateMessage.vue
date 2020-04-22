<template>
    <v-container pa-0>
        <v-layout column>
            <v-flex v-if="checkPreviousMessage()">
                <span class="hs-name">{{ message.from.first_name }} {{ message.from.last_name }}</span>
                <span class="hs-time">{{ message.created_at | utcToLocal }}</span>
            </v-flex>
            <v-flex>
                {{ message.message }}
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import moment from 'moment/moment';
    export default {
        props: {
            message: {
                type: Object,
                required: true
            },
            previous: {
                required: true
            },
            index: {
                type: Number,
                required: true
            }
        },

        data() {
            return {

            }
        },

        methods: {
            checkPreviousMessage() {
                if(this.previous) {
                    return !(moment.utc(this.message.created_at).local().format('mm') === moment.utc(this.previous.created_at).local().format('mm')) || this.message.from.id !== this.previous.from.id;
                }
                return true;
            },
        },
    }
</script>
<style scoped lang="scss">
    .hs-name {
        font-weight: 900;
    }
    .hs-time {
        font-size: 0.7rem;
    }
</style>
