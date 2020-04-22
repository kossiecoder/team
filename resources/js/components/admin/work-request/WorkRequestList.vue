<template>
    <v-container>
        <v-row class="mb-5">
            <v-text-field
                v-model="search"
                outlined
                append-icon="mdi-magnify"
                placeholder="Search Category.."
                hide-details
                @input="fetchCategories()"
            ></v-text-field>
        </v-row>

        <v-row
            v-for="category in categories.data"
            :key="category.id"
            class="mb-2"
        >
            <AdminPageListItem
                :to="{ name: 'admin-work-request-category-edit', params: { id: category.id }}"
                class="flex-grow-1"
            >
                <div class="d-flex">
                    <div class="d-flex flex-grow-1 align-center">
                        {{ category.title }}
                    </div>
                    <div>
                        <v-btn
                            color="error"
                            @click.stop="deleteCategory(category)"
                        >
                            <v-icon color="white">
                                mdi-delete
                            </v-icon>
                        </v-btn>
                    </div>
                </div>
            </AdminPageListItem>
        </v-row>

        <div
            v-if="categories.data.length > 0"
            class="text-center"
        >
            <v-pagination
                v-model="categories.current_page"
                :length="categories.last_page"
                :total-visible="7"
                @input="fetchCategories"
            ></v-pagination>
        </div>
        <p v-else>
            No results to be displayed.
        </p>
    </v-container>
</template>

<script>
    import AdminPageListItem from "@/components/ui/card/CardListItem";
    import categoryApi from "@/api/category";
    import snackBarMixin from "@/mixins/snackBarMixin";

    export default {
        components: {
            AdminPageListItem
        },

        mixins: [snackBarMixin],

        data() {
            return {
                categories: {},
                search: ''
            }
        },

        async created() {
            this.fetchCategories();
        },

        methods: {
            async fetchCategories(page = 1) {
                try {
                    const res = await categoryApi.fetchCategories({
                        page,
                        search: this.search
                    });
                    this.categories = res.data.categories;
                } catch (err) {
                    console.log(err);
                }
            },

            async deleteCategory(category) {
                if (confirm(`Are you sure to delete this category: ${category.title}?`)) {
                    try {
                        await categoryApi.deleteCategory(category.id);
                        this.openSnackBar('The category has been deleted successfully!');
                        this.fetchCategories({ page: 1});
                    } catch (err) {
                        console.log(err);
                    }
                }
            }
        }
    }
</script>
