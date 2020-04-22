<template>
    <v-container
        class="fill-height"
        fluid
    >
        <v-row
            align="center"
            justify="center"
            class="fill-height"
        >
            <v-col
                cols="12"
                sm="8"
                class="fill-height"
            >
                <v-card
                    v-if="!loading"
                    class="fill-height pa-3 scroll"
                >
                    <h1 class="headline font-weight-bold">
                        Work Requests
                    </h1>
                    <div class="d-flex align-center">
                        <div class="flex-grow-1">
                            Filter to be here
                        </div>
                        <div>
                            <v-btn
                                color="success"
                                @click="toggleCreateTaskModal"
                            >
                                Create Task
                            </v-btn>
                        </div>
                    </div>

                    <v-container>
                        <v-card
                            v-for="task in tasks.data"
                            :key="task.id"
                            outlined
                            class="d-flex mb-3 pa-2"
                            @click="pushTo(task.id)"
                        >
                            <div class="flex-grow-1">
                                {{ task.title }}<br>
                                {{ task.category_id }} <small>updated at {{ task.updated_at | utcToLocal }}</small>
                            </div>
                            <div v-if="task.assignee">
                                Assigned to:<br>
                                {{ task.assignee.first_name }}
                            </div>
                            <div v-else>
                                Assigned to:<br>
                                None
                            </div>
                        </v-card>
                        <div class="text-center">
                            <v-pagination
                                v-model="tasks.current_page"
                                :length="tasks.last_page"
                                :total-visible="7"
                                @input="getTasks"
                            ></v-pagination>
                        </div>
                    </v-container>
                </v-card>
            </v-col>
        </v-row>
        <TaskCreate
            :open="createTaskOpen"
            @closeCreateTaskModal="toggleCreateTaskModal"
            @createdTask="getTasks"
        />
    </v-container>
</template>
<script>
    import TaskCreate from '@/components/work-requests/TaskCreate';
    import taskApi from '@/api/task';

    export default {
        components: {
            TaskCreate,
        },

        data() {
            return {
                loading: true,
                createTaskOpen: false,
                tasks: {}
            }
        },

        async created() {
            await this.getTasks();
            this.loading = false;
        },

        methods: {
            toggleCreateTaskModal() {
                this.createTaskOpen = !this.createTaskOpen;
            },

            pushTo(taskId) {
                this.$router.push({name: 'task', params: {id: taskId}});
            },

            async getTasks() {
                try {
                    const res = await taskApi.fetchTasks({ page: this.tasks.current_page});
                    this.tasks = res.data.tasks;
                } catch (err) {
                    console.log(err);
                }
            }
        }
    }
</script>
