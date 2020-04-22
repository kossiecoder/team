<template>
    <v-dialog
        :value="open"
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
                        lg6
                        offset-lg3
                    >
                        <h1>Create a new work request</h1>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                            @submit.prevent="submit"
                        >
                            <v-layout wrap>
                                <v-flex lg4 px-2>
                                    <v-select
                                        v-model="task.selectedCategoryId"
                                        :items="categories"
                                        item-text="title"
                                        item-value="id"
                                        label="Category"
                                    >
                                    </v-select>
                                </v-flex>
                                <v-flex lg3 px-2>
                                    <v-select
                                        v-model="task.selectedPriorityLevelId"
                                        item-text="name"
                                        item-value="id"
                                        :items="priorityLevels"
                                        label="Priority Level"
                                    ></v-select>
                                </v-flex>
                                <v-flex lg12 px-2>
                                    <v-text-field
                                        v-model="task.title"
                                        :rules="nameRules"
                                        label="Title*"
                                        :error-messages="nameErrorMessage"
                                        required
                                    ></v-text-field>
                                </v-flex>

                                <v-flex
                                    lg12
                                    px-2
                                    my-3
                                >
                                    <vue-editor
                                        v-model="task.content"
                                        :editor-toolbar="customToolbar"
                                        placeholder="Description"
                                    />
                                </v-flex>

                                <v-flex
                                    lg12
                                    px-2
                                >
                                    <v-autocomplete
                                        v-model="task.selectedUser"
                                        :disabled="isUpdating"
                                        :items="users"
                                        chips
                                        color="blue-grey lighten-2"
                                        item-value="id"
                                        return-object
                                        label="Assign this task to (Optional - you can do this later)"
                                        :filter="customFilter"
                                    >
                                        <template v-slot:selection="data">
                                            <v-chip
                                                :selected="data.selected"
                                                item-value="id"
                                                close
                                                class="chip--select-multi"
                                                @input="remove"
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
                                </v-flex>
                                <v-flex>
                                    <p class="text-xs-right">
                                        <v-btn @click="closeDialog">
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            type="submit"
                                            color="success"
                                        >
                                            Create
                                        </v-btn>
                                    </p>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-dialog>
</template>
<script>
    import { VueEditor, Quill } from "vue2-editor";
    import categoryApi from '@/api/category';
    import priorityLevelApi from '@/api/priority-level';
    import taskApi from '@/api/task';
    import userMixin from "@/mixins/userMixin";

    export default {
        components: {
            VueEditor
        },

        mixins: [
            userMixin
        ],

        props: {
            open: {
                required: true,
                type: Boolean
            }
        },

        data() {
            return {
                valid: true,
                task: {
                    title: '',
                    content: null,
                    selectedCategoryId: null,
                    selectedPriorityLevelId: null,
                    selectedUser: null,
                },

                category: '',
                priorityId: '',


                isUpdating: false,
                nameErrorMessage: '',
                nameRules:[
                    v => !!v || 'Please type the tile for this work request.',
                ],

                customToolbar: [
                    ["bold", "italic", "underline"],
                    [{ list: "ordered" }, { list: "bullet" }],
                    ["image", "code-block"]
                ],
                categories: [],
                priorityLevels: []
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

        async created() {
            try {
                const categoryRes = await categoryApi.fetchCategories();
                this.categories = categoryRes.data.categories;

                const priorityLevelRes = await priorityLevelApi.fetchPriorityLevels();
                this.priorityLevels = priorityLevelRes.data.priorityLevels;
            } catch (err) {
                console.log(err);
            }
        },

        methods: {
            customFilter (item, queryText) {
                const firstName = item.first_name.toLowerCase();
                const searchText = queryText.toLowerCase();

                return firstName.indexOf(searchText) > -1;
            },

            closeDialog() {
                this.nameErrorMessage = '';
                this.$refs.form.reset();
                this.$emit('closeCreateTaskModal', false);
            },
            remove () {
                this.selectedUser = null;
            },
            submit() {
                if(this.$refs.form.validate()) {
                    this.createTask();
                }

            },
            async createTask() {
                try {
                    const res = await taskApi.storeTask({
                        title: this.task.title,
                        category_id: this.task.selectedCategoryId,
                        assignee_id: this.task.selectedUser ? this.task.selectedUser.id : null,
                        content: this.task.content,
                        priority_level_id: this.task.selectedPriorityLevelId
                    });
                    this.closeDialog();
                    this.$emit('createdTask');
                    await this.$router.push({name: 'task', params: {id: res.data.task.id}});
                } catch (err) {
                    console.log(err);
                }
            },
        },
    }
</script>
