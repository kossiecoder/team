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
                        <h1 class="tw-text-4xl tw-font-bold">
                            Create a new channel
                        </h1>
                        <p>Channels are where your members communicate.</p>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-text-field
                                v-model="name"
                                :rules="nameRules"
                                label="Channel Name"
                                :error-messages="nameErrorMessage"
                                required
                                @keyup="checkChannelExists"
                            ></v-text-field>

                            <v-text-field
                                v-model="description"
                                label="Channel Description (Optional)"
                                required
                            ></v-text-field>
                            <v-autocomplete
                                v-model="selectedUsers"
                                :disabled="isUpdating"
                                :items="users"
                                chips
                                color="blue-grey lighten-2"
                                item-text="id"
                                return-object
                                multiple
                                label="Add People to This Channel (Optional - you can do this later)"
                            >
                                <template v-slot:selection="data">
                                    <v-chip
                                        :input-value="data.selected"
                                        close
                                        class="chip--select-multi"
                                        @input="remove(data.item)"
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
                                    Create Channel
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

        data() {
            return {
                valid: true,
                name: '',
                description: '',

                selectedUsers: [],
                isUpdating: false,
                nameErrorMessage: '',
                nameRules:[
                    v => !!v || 'Please name your channel.',
                ]
            }
        },

        computed: {
            dialog() {
                return this.$store.state.chat.createChannelDialog;
            },
            validSubmit() {
                return this.valid;
            },
            users() {
                return this.$store.state.user.users.filter(user => user.id !== this.currentUser.id);
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
                this.nameErrorMessage = '';
                this.$refs.form.reset();
                return this.$store.dispatch('chat/toggleCreateChannelDialog', false);
            },
            remove (item) {
                const index = this.selectedUsers.findIndex(user => user.id === item.id);
                if (index >= 0) {
                    this.selectedUsers.splice(index, 1)
                }
            },
            submit() {
                if(this.$refs.form.validate()) {
                    this.createChannel();
                }

            },
            async createChannel() {
                try {
                    const res = await chatApi.storeChannel({
                        name: this.name,
                        description: this.description,
                        selectedUsers: this.selectedUsers.map(user => user.id)
                    });

                    const channel = res.data.channel;
                    this.closeDialog();
                    await chatApi.channelCreatedEvent({
                        channelId: channel.id
                    });
                    window.Echo.private('channelChat_' + channel.id).listen('ChatChannelSent', e => {
                        this.$store.commit('chat/add_new_message', e.message);
                    });
                    this.$emit('addedChannel', channel);
                    this.openSnackBar('#' + channel.name + ' has been created successfully');
                    await this.$router.push({name: 'chat-main-channel', params: {id: channel.id}});
                } catch (err) {
                    console.log(err);
                }
            },

            async checkChannelExists() {
                try {
                    const res = await chatApi.checkIfChannelExists({name: this.name});
                    if (res.data.status) {
                        this.nameErrorMessage = this.name + ' is already taken.';
                    } else {
                        this.nameErrorMessage = '';
                    }
                } catch (err) {
                    console.log(err);
                }
            },
        },
    }
</script>
