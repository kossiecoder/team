<template>
    <div class="wrap d-flex" :class="comment.user ? 'my-3' : ''">
        <div v-if="comment.user">
            <v-avatar size="30px" class="mr-2">
                <img src="https://cdn.vuetifyjs.com/images/lists/1.jpg" alt="">
            </v-avatar>
        </div>
        <div v-if="comment.user" style="flex: 200 1 auto !important; ">
            <span><strong>{{ comment.user.first_name }}</strong></span>
            <small class="ml-2 grey--text">{{ comment.created_at | utcToLocal}}</small><br>
            <div v-html="comment.content"></div>
        </div>
        <div v-else>
            <small class="ml-2 grey--text">{{comment.content}}</small><small class="ml-2 grey--text">{{ comment.created_at | utcToLocal}}</small><br>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            comment: {
                required: true,
                type: Object
            }
        }
    }
</script>