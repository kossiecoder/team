<template>
    <v-form
        ref="form"
        v-model="valid"
        @submit.prevent="submit"
    >
        <v-text-field
            v-model="form.title"
            :rules="nameRules"
            label="Category Name*"
            :error-messages="errors.title"
            required
            @keydown="errors.title = null"
        ></v-text-field>
        <v-text-field
            v-model="form.description"
            label="Description"
            required
        ></v-text-field>
        <v-row>
            <v-col>
                <div class="flex-grow-1"></div>
                <v-btn
                    :disabled="!valid"
                    class="mt-3 float-right"
                    color="success"
                    type="submit"
                >
                    Submit
                </v-btn>
            </v-col>
        </v-row>
    </v-form>
</template>

<script>
    import categoryApi from "@/api/category";
    import snackBarMixin from "@/mixins/snackBarMixin";

    export default {
        mixins: [snackBarMixin],

        props: {
            isEditing: {
                type: Boolean,
                required: true
            }
        },

        data() {
            return {
                valid: true,
                form: {
                    title: '',
                    description: '',
                },
                errors: {
                    title: null
                },
                nameRules: [
                    v => !!v || 'Category Name is required',
                    v => (v && v.length > 1) || 'Category Name must be at least 2 characters'
                ],
            }
        },

        computed: {
            categoryId() {
                return this.$route.params.id;
            }
        },

        async created() {
            if (this.isEditing) {
                await this.fetchCategory();
            }
        },

        methods: {
            async fetchCategory() {
                const res = await categoryApi.fetchCategory(this.categoryId);

                this.form.title = res.data.category.title;
                this.form.description = res.data.category.description;
            },

            async submit() {
                if (this.$refs.form.validate()) {
                    try {
                        if (this.isEditing) {
                            await categoryApi.updateCategory(this.categoryId, this.form);
                            this.openSnackBar('This category has been updated successfully!');
                        } else {
                            await categoryApi.storeCategory(this.form);
                            this.openSnackBar('New category has been created successfully!');
                            await this.$router.push({ name: 'admin-work-request-category'})
                        }
                    } catch (err) {
                        console.log(err.response.data.errors);
                        Object.assign(this.errors, err.response.data.errors);
                    }
                }
            }
        }
    }
</script>
