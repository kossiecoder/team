<template>
    <v-container>
        <v-col
            lg="8"
            offset-lg="2"
        >
            <v-card
                style="width: 100%"
                class="pa-lg-5"
            >
                <slot name="top"></slot>

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
    </v-container>
</template>

<script>
    export default {}
</script>
