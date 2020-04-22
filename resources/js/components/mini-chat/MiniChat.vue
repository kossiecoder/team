<template>
    <div class="mini-chat-wrapper">
        <transition name="fade">
            <v-card
                v-if="open"
                width="300"
                height="500"
                class="d-flex flex-column"
            >
                <v-toolbar
                    dark
                    dense
                    class="flex-grow-0"
                >
                    <v-icon
                        v-if="onPageChannel || onPagePrivate"
                        @click="goBackToList"
                    >
                        mdi-arrow-left
                    </v-icon>
                    <div class="flex-grow-1"></div>
                    <v-icon
                        @click="toggleMiniChat"
                    >
                        mdi-close
                    </v-icon>
                </v-toolbar>

                <MiniChatList v-if="onPageList" />
                <MiniChatChannel
                    v-if="onPageChannel"
                    :channel-id="chatId"
                />
                <MiniChatPrivate
                    v-if="onPagePrivate"
                    :chat-user-id="chatId"
                />
            </v-card>
        </transition>
        <v-btn
            fab
            :color="!open && (newMessageExists || newChannelMessageExists) ? 'error' : 'primary'"
            class="float-right mt-2"
            @click="toggleMiniChat"
        >
            <v-icon v-if="open">
                mdi-close
            </v-icon>
            <v-icon v-else>
                {{ !open && (newMessageExists || newChannelMessageExists) ? 'mdi-chat-alert' : 'mdi-chat' }}
            </v-icon>
        </v-btn>
    </div>
</template>
<script>
    import MiniChatChannel from '@/components/mini-chat/MiniChatChannel';
    import MiniChatPrivate from '@/components/mini-chat/MiniChatPrivate';
    import MiniChatList from "@/components/mini-chat/MiniChatList";

    export default {
        components: {
            MiniChatChannel,
            MiniChatPrivate,
            MiniChatList,
        },

        data() {
            return {
                open: false,
            }
        },

        computed: {
            onPage() {
                return this.$store.state.chat.miniChat.page;
            },
            chatId() {
                return this.$store.state.chat.miniChat.id;
            },
            onPageList() {
                return this.onPage === 'list';
            },
            onPageChannel() {
                return this.onPage === 'channel';
            },
            onPagePrivate() {
                return this.onPage === 'private';
            },
            newMessageExists() {
                return this.$store.state.notification.notification.newMessage;
            },
            newChannelMessageExists() {
                return this.$store.state.notification.notification.newChannelMessage;
            }
        },

        methods: {
            toggleMiniChat() {
                this.open = !this.open;
                this.open ? this.$store.commit('chat/update_mini_chat_page', {page: 'list', id: ''}) : this.$store.commit('chat/update_mini_chat_page', {page: ''});
            },
            goBackToList() {
                this.$store.commit('chat/update_mini_chat_page', {page: 'list', id: ''});
            }
        },
    }
</script>
<style lang="scss" scoped>
    .mini-chat-box {
        width: 600px;
        height: 500px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s;
    }

    .fade-enter, .fade-leave-to  {
        opacity: 0;
    }

    .mini-chat-wrapper {
        position: fixed;
        right: 5px;
        bottom: 5px;
    }
</style>
