<template>
    <v-card>
        <v-list
            class="py-0"
            dense
        >
            <v-list-group
                :value="true"
                prepend-icon="mdi-information-outline"
                no-action
                color="black"
            >
                <template v-slot:activator>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>Channel Details</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <v-container>
                    <v-row>
                        <v-col
                            cols="12"
                            flex-row
                        >
                            <label for="name" ><strong>Name</strong></label>
                            <span v-if="hasUpdatePermission">
                                <a
                                    v-if="editChannel.editName"
                                    @click="updateChannelName"
                                >
                                    <small class="ml-2">
                                        save
                                    </small>
                                </a>
                                <a
                                    v-if="!editChannel.editName"
                                    @click="editChannelName"
                                >
                                    <small class="ml-2">
                                        edit
                                    </small>
                                </a>
                                <a
                                    v-if="editChannel.editName"
                                    @click="cancelChannelNameUpdate"
                                >
                                    <small class="ml-1">
                                        cancel
                                    </small>
                                </a>
                            </span>
                            <br>
                            <input
                                v-if="editChannel.editName"
                                id="name"
                                ref="name"
                                v-model="channel.name"
                                class="hs-channel-input"
                                type="text"
                                style="width: 100%;"
                                @keyup.esc="cancelChannelNameUpdate"
                            />
                            <span v-else>{{ channel.name }}</span>
                        </v-col>
                        <v-col
                            cols="12"
                            px-3
                            py-2
                        >
                            <label for="description"><strong>Description</strong></label>
                            <span v-if="hasUpdatePermission">
                                <a
                                    v-if="editChannel.editDescription"
                                    @click="updateChannelDescription"
                                >
                                    <small class="ml-2">save</small>
                                </a>
                                <a
                                    v-if="!editChannel.editDescription"
                                    @click="editChannelDescription"
                                >
                                    <small class="ml-2">edit</small>
                                </a>
                                <a
                                    v-if="editChannel.editDescription"
                                    @click="cancelChannelDescriptionUpdate"
                                >
                                    <small class="ml-1">cancel</small></a>
                            </span>
                            <br>
                            <textarea
                                v-if="editChannel.editDescription"
                                id="description"
                                ref="description"
                                v-model="channel.description"
                                class="hs-channel-input"
                                style="width: 100%;"
                                rows="5"
                                @keyup.esc="cancelChannelDescriptionUpdate"
                            ></textarea>
                            <span v-else>{{ channel.description ? channel.description : 'No Description' }}</span>
                        </v-col>
                        <v-col
                            cols="12"
                            px-3
                            py-2
                        >
                            <strong>Created</strong><br>
                            <small>Created by {{ creator.name }} on {{ channel.createdAt | utcToLocal }}</small>
                        </v-col>
                        <v-col
                            v-if="hasDeletePermission"
                            cols="12"
                            px-3
                            py-2
                        >
                            <v-btn
                                block
                                color="error"
                                @click="deleteChannel"
                            >
                                Delete Channel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-list-group>
            <v-divider></v-divider>
            <v-list-group
                prepend-icon="mdi-account-outline"
                no-action
                color="black"
            >
                <template v-slot:activator>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ users.length }} Member{{ users.length > 1 ? 's' : '' }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                <v-list subheader>
                    <v-list-item
                        v-for="(user, index) in users"
                        :key="index"
                        :to="{name: 'chat-main-private', params: {id: user.id}}"
                    >
                        <v-list-item-action>
                            <v-icon color="teal">
                                mdi-chat
                            </v-icon>
                        </v-list-item-action>

                        <v-list-item-content>
                            <v-list-item-title>{{ user.first_name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="creator.id === currentUser.id"
                        @click="toggleDialog"
                    >
                        <v-list-item-content>
                            Add more people...
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-list-group>
            <v-divider></v-divider>
        </v-list>
        <ChatChannelAddPeople
            :already-in-channel="users"
            @addedUsers="updateUsers"
        />
    </v-card>
</template>
<script>
    import ChatChannelAddPeople from '@/components/chat/channel/ChatChannelAddPeople';
    import chatApi from '@/api/chat';
    import snackBarMixin from "@/mixins/snackBarMixin";
    import permissionMixin from "@/mixins/permissionMixin";
    import userMixin from "@/mixins/userMixin";

    export default {
        components: {
            ChatChannelAddPeople
        },

        mixins: [
            snackBarMixin,
            permissionMixin,
            userMixin
        ],

        data() {
            return {
                channel: {
                    name: '',
                    description: '',
                    createdAt: ''
                },
                creator: {
                    id: '',
                    name: ''
                },
                users: [],
                loading: true,
                editChannel: {
                    originalChannelName: '',
                    originalChannelDescription: '',
                    editName: false,
                    editNameError: '',
                    editDescription: false,
                    editDescriptionError: ''
                }
            }
        },

        computed: {
            channelId() {
                return this.$route.params.id;
            },
            hasDeletePermission() {
                return this.hasPermission('delete_chat_channels') || (this.hasPermission('delete_own_chat_channels') && this.creator.id === this.currentUser.id);
            },
            hasUpdatePermission() {
                return this.hasPermission('edit_chat_channels') || (this.hasPermission('edit_own_chat_channels') && this.creator.id === this.currentUser.id);
            },
        },

        async created() {
            try {
                const res = await chatApi.showChannel(this.channelId);
                this.loading = true;
                this.channel.name = res.data.name;
                this.channel.description = res.data.description;
                this.channel.firstName = res.data.first_name;
                this.channel.createdAt = res.data.created_at;
                this.creator.name = res.data.user.first_name;
                this.creator.id = res.data.user.id;
                this.users = res.data.users;
                this.loading = false;
            } catch (err) {
                console.log(err);
            }
        },

        methods: {
            toggleDialog() {
                this.$store.dispatch('chat/toggleAddPeopleDialog', true);
            },
            updateUsers(value) {
                this.users = value;
            },
            isCreator() {
                return this.creator.id === this.currentUser.id;
            },
            async updateChannelName() {
                if(this.channel.name) {
                    try {
                        const res = await chatApi.updateChannel(this.channelId, {
                            name: this.channel.name
                        });
                        this.openSnackBar(res.data.message);
                        this.channel.name = res.data.channel.name;
                        this.editChannel.editName = false;
                    } catch (err) {
                        console.log(err);
                    }
                }
            },
            async updateChannelDescription() {
                try {
                    const res = await chatApi.updateChannel(this.channelId, {
                        description: this.channel.description
                    });
                    this.openSnackBar(res.data.message);
                    this.channel.description = res.data.channel.description;
                    this.editChannel.editDescription = false;
                } catch (err) {
                    console.log(err);
                }
            },

            editChannelName() {
                this.editChannel.editName = true;
                this.editChannel.originalChannelName = this.channel.name;
                this.$nextTick(() => this.$refs.name.focus());
            },
            editChannelDescription() {
                this.editChannel.editDescription = true;
                this.editChannel.originalChannelDescription = this.channel.description;
                this.$nextTick(() => this.$refs.description.focus());
            },
            cancelChannelNameUpdate() {
                this.editChannel.editName = false;
                this.channel.name = this.editChannel.originalChannelName;
                this.editChannel.originalChannelName = '';
            },
            cancelChannelDescriptionUpdate() {
                this.editChannel.editDescription = false;
                this.channel.description = this.editChannel.originalChannelDescription;
                this.editChannel.originalChannelDescription = '';
            },
            async deleteChannel() {
                if(confirm('Are you sure you want to delete this channel?')) {
                    try {
                        const res = await chatApi.deleteChannel(this.channelId);
                        await this.$store.dispatch('chat/fetchMyChannels');
                        this.openSnackBar(res.data.message);
                        await this.$router.push({name: 'chat-main-private', params: {id: this.currentUser.id}})
                    } catch (err) {
                        console.log(err);
                    }
                }
            }
        },
    }
</script>
<style lang="scss" scoped>
    .hs-channel-input {
        width: 100%;
        border: 1px solid #90a4ae;
    }
</style>
