<template>
    <v-dialog
        :value="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
        @keydown.esc="closeDialog"
    >
        <v-card>
            <v-container>
                <p class="text-xs-right">
                    <v-btn
                        right
                        icon
                        @click="closeDialog"
                    >
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </p>
                <v-layout>
                    <v-flex
                        lg4
                        offset-lg4
                    >
                        <h1>Add people to the channel</h1>
                        <p>Channels are where your members communicate.</p>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-autocomplete
                                v-model="selectedUsers"
                                :disabled="isUpdating"
                                :items="users"
                                chips
                                color="blue-grey lighten-2"
                                item-text="id"
                                return-object
                                multiple
                                label="Add People to This Channel"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        :input-value="data.selected"
                                        close
                                        class="chip--select-multi"
                                        @click:close="remove(data.item)"
                                    >
                                        {{ data.item.first_name }}
                                    </v-chip>
                                </template>
                                <template v-slot:item="data">
                                    <template v-if="typeof data.item !== 'object'">
                                        <v-list-item-content v-text="data.item"></v-list-item-content>
                                    </template>
                                    <template v-else>
                                        <v-list-item-content>
                                            <v-list-item-title>{{ data.item.first_name }}</v-list-item-title>
                                        </v-list-item-content>
                                    </template>
                                </template>
                            </v-autocomplete>
                            <p class="text-xs-right">
                                <v-btn @click="closeDialog">
                                    Cancel
                                </v-btn>
                                <v-btn
                                    color="success"
                                    @click="submit"
                                >
                                    Add People
                                </v-btn>
                            </p>
                        </v-form>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-dialog>
</template>
<script>
    import chatApi from '@/api/chat';
    import snackBarMixin from "@/mixins/snackBarMixin";
    import userMixin from "@/mixins/userMixin";

    export default {
        mixins: [
            snackBarMixin,
            userMixin
        ],

        props: {
            alreadyInChannel: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                valid: true,

                selectedUsers: [],
                isUpdating: false,
                nameRules:[
                    v => !!v || 'Please name your channel.',
                ]
            }
        },

        computed: {
            dialog() {
                return this.$store.state.chat.addPeopleDialog;
            },
            validSubmit() {
                return this.valid;
            },
            channels() {
                return this.$store.state.chat.channels;
            },
            channelId() {
                return this.$route.params.id;
            },
            users() {
                return this.$store.state.user.users.filter(user => {
                    return user.id !== this.currentUser.id && !this.alreadyInChannel.find(u => {
                        return u.id === user.id;
                    });
                });
            }
        },

        watch: {
            isUpdating (val) {
                if (val) {
                    setTimeout(() => (this.isUpdating = false), 3000)
                }
            }
        },

        methods: {
            closeDialog() {
                this.$refs.form.reset();
                return this.$store.dispatch('chat/toggleAddPeopleDialog', false);
            },
            remove (item) {
                const index = this.selectedUsers.findIndex(user => user.id === item.id);
                if (index >= 0) {
                    this.selectedUsers.splice(index, 1)
                }
            },
            submit() {
                if(this.$refs.form.validate()) {
                    this.addPeople();
                }
            },
            async addPeople() {
                try {
                    const res = await chatApi.addPeopleToChannel({
                        selectedUsers: this.selectedUsers.map(user => user.id),
                        channelId: this.channelId
                    });
                    const channelId = res.data.channelId;
                    this.closeDialog();

                    await chatApi.channelCreatedEvent({
                        channelId: channelId
                    });

                    window.Echo.private('channelChat_' + channelId).listen('ChatChannelSent', e => {
                        console.log('4', e);
                        this.$store.commit('chat/add_new_message', e.message);
                    });
                    this.$emit('addedUsers', res.data.users);
                    this.openSnackBar('You have added people to your channel successfully');
                } catch (err) {
                    console.log(err);
                }

            },
        },
    }
</script>
