<template>
    <v-container>
        <v-row>
            <v-col
                lg="8"
                offset-lg="2"
            >
                <v-card
                    class="pa-lg-5 pa-3"
                >
                    <v-row class="mx-1">
                        <BackButton :to="backTo" />
                        <div class="flex-grow-1"></div>
                        <v-btn
                            v-if="newTo.name"
                            color="success"
                            exact
                            :to="newTo"
                        >
                            <slot name="button-title">
                                New
                            </slot>
                        </v-btn>
                    </v-row>

                    <v-row justify-center>
                        <v-col>
                            <h1 class="text-center">
                                <slot name="title"></slot>
                            </h1>
                        </v-col>
                    </v-row>

                    <v-divider class="mb-4"></v-divider>

                    <slot name="main"></slot>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import BackButton from "@/components/ui/button/BackButton";

    export default {
        components: {
            BackButton
        },

        props: {
            backTo: {
                type: Object,
                required: true
            },
            newTo: {
                type: Object,
                default(){
                    return {};
                }
            },
        },
    }
</script>
