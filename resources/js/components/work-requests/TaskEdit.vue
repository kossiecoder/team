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
                    <v-flex lg6 offset-lg3>
                        <h1 class="px-2">Update work request</h1>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-layout wrap>
                                <v-flex lg4 px-2>
                                    <v-select
                                        v-model="task.category_id"
                                        :items="categories"
                                        item-text="title"
                                        item-value="id"
                                        label="Category"
                                    >
                                    </v-select>
                                </v-flex>
                                <v-flex lg3 px-2>
                                    <v-select
                                        v-model="task.priority_level_id"
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

                                <v-flex lg12 px-2 my-3>
                                    <vue-editor v-model="task.content" :editor-toolbar="customToolbar" placeholder="Description"/>
                                </v-flex>

                                <v-flex lg12 px-2>
                                    <v-autocomplete
                                        v-model="task.assignee_id"
                                        :disabled="isUpdating"
                                        :items="users"
                                        chips
                                        color="blue-grey lighten-2"
                                        item-value="id"
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
                                                <v-list-tile-content v-text="data.item"></v-list-tile-content>
                                            </template>
                                            <template v-else>
                                                <v-list-tile-content>
                                                    <v-list-tile-title v-html="data.item.first_name"></v-list-tile-title>
                                                </v-list-tile-content>
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
                                            color="success"
                                            @click="submit"
                                        >
                                            Update
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
    import { VueEditor } from "vue2-editor";
    import taskApi from '@/api/task';
    import categoryApi from '@/api/category';
    import priorityLevelApi from '@/api/priority-level';
    import commentApi from '@/api/comment';

    export default {
        components: {
            VueEditor
        },

        props: {
            open: {
                required: true,
                type: Boolean
            }
        },

        data() {
            return {
                valid: true,
                task: {},

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
            users() {
                return this.$store.state.user.users;
            },
            taskId() {
                return this.$route.params.id;
            },
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
                const res = await taskApi.showTask(this.taskId);
                this.task = res.data.task;
                this.taskLoading = false;
            } catch (err) {
                console.log(err);
                await this.$router.push({name: 'tasks'});
            }

            try {
                const categoryRes = await categoryApi.fetchCategories();
                this.categories = categoryRes.data.categories;
            } catch (err) {
                console.log(err);
            }

            try {
                const priorityLevelRes = priorityLevelApi.fetchPriorityLevels();
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
                this.$emit('open', false);
            },
            remove () {
                this.task.assignee_id = null;
            },
            submit() {
                if(this.$refs.form.validate()) {
                    this.updateTask();
                }

            },
            async updateTask() {
                try {
                    const res = await taskApi.updateTask(this.taskId, {
                        title: this.task.title,
                        category_id: this.task.category_id,
                        assignee_id: this.task.assignee_id,
                        content: this.task.content,
                        priority_level_id: this.task.priority_level_id
                    });
                    const task = res.data.task;
                    const commentRes = await commentApi.storeComment({
                        task_id: task.id,
                        content: this.task.creator.first_name + ' edited this work request'
                    });

                    this.$emit('updatedTask', task);
                    this.$emit('activityLog', commentRes.data.comment);
                    this.closeDialog();
                    // this.openSnackBar('#'+channel.name+' has been created successfully');
                    // this.$router.push({name: 'work-requests', params: { id: work-requests.id }});
                } catch (err) {
                    console.log(err);
                }
            },
        },
    }
</script>
