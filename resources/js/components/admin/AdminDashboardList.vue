<template>
    <v-row>
        <v-col
            v-for="(item, index) in items"
            :key="index"
            cols="12"
            md="4"
        >
            <v-card
                class="pa-4 text-center"
                :to="item.to"
                hover
            >
                {{ item.name }}
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        data() {
            return {
                items: [
                    {
                        name: 'Team Members',
                        to: { name: 'admin-user'}
                    },
                    {
                        name: 'Work Request Categories',
                        to: { name: 'admin-work-request-category'}
                    },
                ]
            }
        },
    }
</script>
