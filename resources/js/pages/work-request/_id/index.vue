<template>
    <v-container fill-height>
        <v-row
            v-if="!taskLoading"
            class="main xs12 lg6 offset-lg3 mt-4 hs-task scroll fill-height"
        >
            <div class="white px-4 py-3 task-menu">
                <v-btn
                    outlined
                    small
                >
                    Mark Complete (not working yet)
                </v-btn>
                <div class="float-right">
                    <v-btn
                        v-if="isCreator"
                        color="success"
                        small
                        @click="taskEditOpen = true"
                    >
                        Edit
                    </v-btn>
                </div>
            </div>
            <div class="chat-area px-4 py-3 white">
                <h1>{{ task.title }}</h1>
                <v-autocomplete
                    ref="assignee"
                    v-model="task.assignee_id"
                    :disabled="isUpdating"
                    :items="users"
                    chips
                    color="blue-grey lighten-2"
                    item-value="id"
                    :label="task.assignee_id ? 'Assigned to' : 'Unassigned'"
                    :filter="customFilter"
                    style="width: 170px"
                >
                    <template v-slot:selection="data">
                        <v-chip
                            :input-value="data.selected"
                            item-value="id"
                            close
                            class="chip--select-multi"
                            @input="remove"
                        >
                            <v-avatar>
                                <img src="https://cdn.vuetifyjs.com/images/lists/1.jpg" alt="">
                            </v-avatar>
                            {{ data.item.first_name }}
                        </v-chip>
                    </template>
                    <template v-slot:item="data">
                        <template v-if="typeof data.item !== 'object'">
                            <v-list-item-content v-text="data.item"></v-list-item-content>
                        </template>
                        <template v-else>
                            <v-avatar size="30px" class="mr-2">
                                <img src="https://cdn.vuetifyjs.com/images/lists/1.jpg" alt="">
                            </v-avatar>
                            <v-list-item-content>
                                <v-list-item-title v-html="data.item.first_name"></v-list-item-title>
                            </v-list-item-content>
                        </template>
                    </template>
                </v-autocomplete>
                <!--                    </v-flex>-->
                <hr class="hs-hr">
                <div class="my-3">
                    <div v-if="task.content" v-html="task.content"></div>
                    <div v-else class="grey--text">Description</div>
                </div>
                <hr class="hs-hr">
                <div class="my-4" v-if="!hideComments"><a @click="toggleComments">Hide Earlier Activity</a></div>
                <div class="my-3 wrap d-flex">
                    <div>
                        <v-avatar size="30px" class="mr-2">
                            <img src="https://cdn.vuetifyjs.com/images/lists/1.jpg" alt="">
                        </v-avatar>
                    </div>
                    <div style="flex: 200 1 auto !important; ">
                        <span><strong>{{ task.creator.first_name }} created this work request.</strong></span>
                        <small class="ml-2 grey--text">{{ task.created_at | utcToLocal}}</small>
                    </div>
                </div>
                <div class="my-4 pl-2" v-if="this.task.comments.length > 3 && hideComments"><a @click="toggleComments">{{ this.task.comments.length - this.computedComments.length }} more comments</a></div>
                <task-comment v-for="(comment, index) in computedComments" :key="index" :comment="comment"></task-comment>
            </div>

            <div class="pa-4 input-area">
                <vue-editor v-if="commentEditor" ref="editor" v-model="comment" :editor-toolbar="customToolbar" placeholder="Description" class="white" @blur="toggleCommentEditor" id="editor"></vue-editor>
                <div v-else class="input-box"  @click="toggleCommentEditor" style="cursor: auto;">
                    <div v-if="comment" v-html="comment" ></div>
                    <input v-else type="text" placeholder="Ask a question or post an update..." style="width: 100%"/>
                </div>
                <p v-if="commentEditor" class="text-xs-right">
                    <v-btn color="info" class="mr-0" @click="submitComment">Comment</v-btn>
                </p>
            </div>
            <task-edit v-if="taskEditOpen" :open="taskEditOpen" @open="taskEditOpen = false" @updatedTask="updateTask" @activityLog="addActivityLog"></task-edit>
            <mini-chat></mini-chat>
        </v-row>
        <div v-else>
            <p class="text-xs-center mt-5">
                <v-progress-circular
                    :size="70"
                    color="primary"
                    indeterminate
                ></v-progress-circular>
            </p>
        </div>
    </v-container>
</template>
<script>
    import { VueEditor } from "vue2-editor";
    import TaskComment from '@/components/work-requests/TaskComment';
    import TaskEdit from '@/components/work-requests/TaskEdit';
    import MiniChat from '@/components/mini-chat/MiniChat';
    import taskApi from '@/api/task';
    import commentApi from '@/api/comment';
    import userMixin from "@/mixins/userMixin";

    export default {
        components: {
            VueEditor,
            TaskComment,
            TaskEdit,
            MiniChat
        },

        mixins: [userMixin],

        data() {
            return {
                taskLoading: true,
                taskEditOpen: false,

                task: {},
                isUpdating: false,
                comment: '',
                commentEditor: false,
                comments: [],
                hideComments: true,
                editTitle: false,

                customToolbar: [
                    ["bold", "italic", "underline"],
                    [{list: "ordered"}, {list: "bullet"}],
                ],
            }
        },

        computed: {
            isCreator() {
                return this.currentUser.id === this.task.creator_id;
            },

            taskId() {
                return this.$route.params.id;
            },

            users() {
                return this.$store.state.user.users;
            },

            computedComments() {
                if(this.hideComments) {
                    return this.task.comments.slice(-3);
                }
                return this.task.comments;
            },

            taskAssigneeId() {
                return this.task.assignee_id;
            }
        },

        watch: {
            async taskAssigneeId (newValue, oldValue) {
                if((oldValue || newValue) && (oldValue !== newValue) && typeof oldValue !== "undefined") {
                    try {
                        await taskApi.updateTask(this.taskId, {
                            assignee_id: newValue,
                            title: this.task.title,
                        });
                    } catch (err) {
                        console.log(err);
                    }
                }
            },

            '$route.params.id' (newValue, oldValue) {
                if(parseInt(newValue) !== parseInt(oldValue)) {
                    this.getTask();
                }
            }
        },

        created() {
            this.getTask();
        },

        beforeDestroy() {
            window.Echo.leaveChannel('Task.' + this.taskId);
            window.Echo.leaveChannel('Task.Comment.' + this.taskId);
        },

        methods: {
            async getTask() {
                try {
                    const res = await taskApi.showTask(this.taskId);
                    this.task = res.data.task;
                    this.taskLoading = false;

                    window.Echo.private('Task.' + this.taskId).listen('TaskUpdated', e => {
                        const task = e.task;
                        this.task.assignee_id = task.assignee_id;
                        this.task.category_id = task.category_id;
                        this.task.content = task.content;
                        this.task.priority_level_id = task.priority_level_id;
                        this.task.title = task.title;
                    });

                    window.Echo.private('Task.Comment.' + this.taskId).listen('TaskCommentSubmitted', e => {
                        this.task.comments.push(e.comment);
                    });
                } catch (err) {
                    console.log(err);
                    await this.$router.push({name: 'tasks'});
                }
            },

            addActivityLog(e) {
                this.task.comments.push(e);
            },

            updateTask(e) {
                this.task.title = e.title;
                this.task.content = e.content;
                this.task.priority_level_id = e.priority_level_id;
                this.task.assignee_id = e.assignee_id;
                this.task.category_id = e.category_id;
            },

            customFilter (item, queryText) {
                const firstName = item.first_name.toLowerCase()
                const searchText = queryText.toLowerCase()

                return firstName.indexOf(searchText) > -1;
            },

            remove () {
                this.$refs.assignee.blur();
                this.task.assignee_id = null;
            },

            toggleComments() {
                this.hideComments = !this.hideComments;
            },

            toggleCommentEditor() {
                this.commentEditor = !this.commentEditor;
                this.$nextTick(() => {
                    if(this.commentEditor) {
                        this.$refs.editor.quill.focus();
                    }
                })
            },

            async submitComment() {
                if(this.comment) {
                    try {
                        const res = await commentApi.storeComment({
                            task_id: this.taskId,
                            content: this.comment,
                            user_id: this.currentUser.id
                        });
                        this.commentEditor = false;
                        this.task.comments.push(res.data.comment);
                    } catch (err) {
                        console.log(err);
                    }
                }
                this.comment = '';
            }
        },
    }
</script>
<style scoped lang="scss">
    .hs-hr {
        border: 0.5px solid gainsboro;
    }
    .hs-mini-chat-box {
        display: flex;
        flex-direction: column;
        height: 100vh; min-height: 100vh;
        box-sizing: border-box;
    }


    .main {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow-y: auto;
        border: 1px solid #e0e6e8;
    }

    .chat-area {
        overflow-y: auto;
        flex: 1;
    }

    .input-area {
        background-color: #f6f8f9;
        border-top: #e0e6e8 1px solid;
    }

    .input-box {
        width: 100%;
        border: 1px solid #e0e6e8;
        background-color: white;
        padding: 5px 10px;
        border-radius: 5px;
        input:focus {
            outline: none;
        }
    }

    .hs-task {
        @media (max-width: 1024px) {
            margin-top: 0 !important;
        }
    }

    p {
        margin-bottom: 5px !important;
    }

    .task-menu {
        border-bottom: 1px solid gainsboro !important;
    }
</style>
<style scoped>
    /deep/ #editor .ql-editor {
        overflow-y: auto !important;
        max-height: 300px !important;
    }
</style>
