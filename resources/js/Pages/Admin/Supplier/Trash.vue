<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Rents Trash List</span></v-col>
                <v-col cols="6" class="d-flex justify-end">
                    <v-text-field
                        v-model="search"
                        density="compact"
                        label="Search"
                        prepend-inner-icon="mdi-magnify"
                        variant="solo-filled"
                        class="mx-4"
                        flat
                        hide-details
                        solo
                        single-line
                        clearable
                    ></v-text-field>
                </v-col>
            </v-row>
        </v-card-title>

        <v-data-table-server
            v-model:items-per-page="itemsPerPage"
            :headers="headers"
            :search="search"
            :items="serverItems"
            :items-length="totalItems"
            :loading="loading"
            item-value="created_at"
            loading-text="Loading... Please wait"
            @update:options="loadItems"
        >
            <template v-slot:item.photo="{ item }">
                <img class="rentsImg" :src="item.photo" alt="" />
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon @click="showRestoreDialog(item.id)" color="green"
                    >mdi-restore</v-icon
                >
                <v-icon @click="showConfirmDialog(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table-server>

        <!-- Restore Confirmation Dialog -->
        <RestoreConfirmDialog
            :restoreDialogName="restoreDialogName"
            v-model:modelValue="restoreDialog"
            :onConfirm="confirmRestore"
            :onCancel="() => (restoreDialog = false)"
        />
        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :dialogName="dialogName"
            v-model:modelValue="deleteDialog"
            :onConfirm="confirmDelete"
            :onCancel="() => (deleteDialog = false)"
        />
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
import RestoreConfirmDialog from "../../Components/RestoreConfirmDialog.vue";
import ConfirmDialog from "../../Components/ConfirmDialog.vue";

export default {
    components: {
        RestoreConfirmDialog,
        ConfirmDialog,
    },
    data() {
        return {
            restoreDialogName: "Are you sure you want to restore this Rents?",
            dialogName: "Are you sure you want to delete this Rents ?",
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Name", key: "name", sortable: true },
                { title: "Email", key: "email", sortable: true },
                { title: "Phone", key: "phone", sortable: true },
                {
                    title: "Contact Person",
                    value: "contact_person",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                { title: "Photo", key: "photo", sortable: false },

                { title: "Actions", key: "actions", sortable: false },
            ],
            serverItems: [],
            loading: true,
            totalItems: 0,
            restoreDialog: false, // Separate state for restore dialog
            deleteDialog: false, // Separate state for delete dialog
            selectedBrandId: null,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/supplier/trashed", {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortKey,
                        sortOrder,
                        search: this.search,
                    },
                });
                this.serverItems = response.data.items || [];
                this.totalItems = response.data.total || 0;
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        showRestoreDialog(id) {
            this.selectedSupplierId = id;
            this.restoreDialog = true; // Open restore dialog
        },
        showConfirmDialog(id) {
            this.selectedSupplierId = id;
            this.deleteDialog = true; // Open delete dialog
        },
        async confirmRestore() {
            this.restoreDialog = false; // Close the restore dialog
            try {
                await this.$axios.post(
                    `/supplier/${this.selectedSupplierId}/restore`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Supplier restored successfully!");
            } catch (error) {
                console.error("Error restoring Supplier:", error);
                toast.error("Failed to restore Supplier.");
            }
        },
        async confirmDelete() {
            this.deleteDialog = false; // Close the delete dialog
            try {
                const response = await this.$axios.delete(
                    `/supplier/${this.selectedSupplierId}/force-delete`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });

                toast.success("Supplier deleted successfully!");
            } catch (error) {
                console.error("Error deleting Supplier:", error);
                toast.error("Failed to delete Supplier.");
            }
        },
    },
    created() {
        this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
        });
    },
};
</script>
