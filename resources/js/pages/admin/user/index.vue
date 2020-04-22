<template>
    <v-container>
        <v-layout>
            <v-flex>
                <p
                    v-if="hasPermission('add_users')"
                    class="text-right"
                >
                    <v-btn
                        color="success"
                        :to="{name: 'admin-user-create'}"
                    >
                        <v-icon left>
                            mdi-account-plus
                        </v-icon>
                        <span>Add New Member</span>
                    </v-btn>
                </p>

                <v-card>
                    <v-card-title>
                        <h2>Team Members</h2>
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="search"
                            append-icon="mdi-account-search"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table
                        :headers="headers"
                        :items="users"
                        class="elevation-1"
                        :search="search"
                        :options.sync="pagination"
                    >
                        <template v-slot:items="props">
                            <td>{{ props.item.first_name }}</td>
                            <td>{{ props.item.last_name }}</td>
                            <td>{{ props.item.email }}</td>
                            <td class="justify-left layout">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editItem(props.item)"
                                >
                                    edit
                                </v-icon>
                                <v-icon
                                    v-if="hasPermission('delete_users') && props.item.id !== currentUser.id"
                                    small
                                    @click="deleteItem(props.item)"
                                >
                                    delete
                                </v-icon>
                            </td>
                        </template>
                    </v-data-table>
                </v-card>
            </v-flex>
        </v-layout>
        <MiniChat />
    </v-container>
</template>

<script>
    import MiniChat from '@/components/mini-chat/MiniChat';
    import userApi from '@/api/user';
    import snackBarMixin from "@/mixins/snackBarMixin";
    import permissionMixin from "@/mixins/permissionMixin";
    import userMixin from "@/mixins/userMixin";

    export default {
        components: {
            MiniChat,
        },

        mixins: [
            snackBarMixin,
            permissionMixin,
            userMixin
        ],

        data() {
            return {
                search: '',
                dialog: false,
                headers: [
                    {
                        text: 'First Name',
                        value: 'first_name'
                    },
                    { text: 'Last Name', value: 'last_name' },
                    { text: 'Email', value: 'email' },
                    { text: 'Actions', value: 'name', sortable: false }
                ],
                pagination: {
                    rowsPerPage: -1
                },
                editedIndex: -1,
                editedItem: {
                    name: '',
                    calories: 0,
                    fat: 0,
                    carbs: 0,
                    protein: 0
                },
                defaultItem: {
                    name: '',
                    calories: 0,
                    fat: 0,
                    carbs: 0,
                    protein: 0
                }
            }
        },

        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
            },
            users() {
                return this.$store.state.user.users;
            }
        },

        watch: {
            dialog (val) {
                val || this.close()
            }
        },

        created () {
        },

        methods: {
            editItem (item) {
                this.$router.push({name: 'profile-detail', params: {id: item.id}});
            },

            async deleteItem (user) {
                if(confirm('Are you sure you want to delete this user?')) {
                    try {
                        const res = await userApi.deleteUser(user.id);
                        this.openSnackBar('You have successfully deleted ' + res.data.user.first_name);
                        await this.$store.dispatch('user/fetchUsers');
                    } catch (err) {
                        console.log(err);
                    }
                }
            },

            close () {
                this.dialog = false;
                setTimeout(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                }, 300)
            },

            save () {
                if (this.editedIndex > -1) {
                    Object.assign(this.desserts[this.editedIndex], this.editedItem)
                } else {
                    this.desserts.push(this.editedItem)
                }
                this.close()
            }
        },
    }
</script>
