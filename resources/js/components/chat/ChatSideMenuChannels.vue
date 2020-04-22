<template>
    <div>
        <v-list
            subheader
            dense
        >
            <v-subheader>
                Channels
                <v-spacer></v-spacer>
                <v-tooltip
                    v-if="hasAddChatChannelsPermission"
                    right
                >
                    <template v-slot:activator="{ on }">
                        <v-icon
                            v-on="on"
                            @click="openCreateChannelDialog"
                        >
                            mdi-plus-circle-outline
                        </v-icon>
                    </template>
                    <span>Create a new channel</span>
                </v-tooltip>
            </v-subheader>

            <v-list-item
                v-for="channel in channels"
                :key="channel.name"
                :to="{ name: 'chat-main-channel', params: {id: channel.id} }"
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
    </div>
</template>
<script>
    export default {
        props: {
            addedChannel: {
                type: Object,
            }
        },
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
            hasAddChatChannelsPermission() {
                return this.$store.state.permission.myPermissions.some(permission => permission === 'add_chat_channels');
            },
            channels() {
                return this.$store.state.chat.myChannels;
            }
        },

        watch: {
            addedChannel(value) {
                this.channels.push(value);
            },
        },

        created() {
            if(this.$store.state.chat.myChannels.length === 0) {
                this.$store.dispatch('chat/fetchMyChannels');
            }
        },

        methods: {
            openCreateChannelDialog() {
                this.$store.dispatch('chat/toggleCreateChannelDialog', true);
            }
        },
    }
</script>
<style lang="scss" scoped>
    .new-message {
        font-weight: bold !important;
        font-size: 0.9rem !important;
    }
</style>
