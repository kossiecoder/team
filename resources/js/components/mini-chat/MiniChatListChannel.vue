<template>
    <v-list
        subheader
        dense
    >
        <v-subheader>Channels</v-subheader>

        <v-list-item
            v-for="channel in channels"
            :key="channel.name"
            @click="moveToChannel(channel.id)"
        >
            <v-list-item-content>
                <v-list-item-title :class="{ 'new-message': !channel.pivot.is_read }">
                    {{ '# '+channel.name }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <p
            v-if="!channels.length"
            class="text-center"
        >
            You have not joined any channel yet.
        </p>
    </v-list>
</template>
<script>
    export default {
        data() {
            return {
                loading: true,
                newMessageStyle: {
                    fontSize: '1.2rem',
                    fontWeight: 'bold'
                }
            }
        },

        computed: {
            channels() {
                return this.$store.state.chat.myChannels;
            }
        },

        created() {
            if(this.$store.state.chat.myChannels.length === 0) {
                this.$store.dispatch('chat/fetchMyChannels');
            }
        },

        methods: {
            moveToChannel(channelId) {
                this.$store.commit('chat/update_mini_chat_page', {
                    page: 'channel',
                    id: channelId
                });
            }
        }
    }
</script>
<style lang="scss" scoped>
    .new-message {
        font-weight: bold !important;
        font-size: 0.9rem !important;
    }
</style>
