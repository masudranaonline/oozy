<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Start Breakdown Service</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">

        <!-- Select Machine Code -->
        <v-autocomplete
          v-model="breakdown_service.machine_id"
          :items="machine_codes"
          item-value="id"
          item-title="machine_code"
          label="Select Machine Code"
          outlined
          clearable
          density="comfortable"
          :rules="[rules.required]"
          :error-messages="errors.machine_id ? errors.machine_id : ''"
          @update:search="fetchMachineCodes"
        >
          <template v-slot:label>
            Select Machine Code <span style="color: red">*</span>
          </template>
        </v-autocomplete>

        <!-- Technician ID and Name Display -->
        <v-text-field
          v-model="breakdown_service.technician_name"
          :readonly="true"
          label="Technician Name"
          outlined
        ></v-text-field>

        <!-- Technician Selection (if you want to change the technician) -->
        <v-autocomplete
          v-model="breakdown_service.technician_id"
          :items="technicianItems"
          item-value="id"
          item-title="name"
          label="Select Technician"
          outlined
          clearable
          density="comfortable"
          @update:search="fetchTechnicians"
        >
          <template v-slot:label>
            Select Technician
          </template>
        </v-autocomplete>

        <!-- Service Status -->
        <v-select
          v-model="breakdown_service.breakdown_service_status"
          :items="statusItems"
          label="Service Status"
          clearable
          density="comfortable"
        ></v-select>

        <!-- Action Buttons -->
        <v-row class="mt-4">
          <!-- Reset Button -->
          <v-col cols="12" class="text-right">
            <v-btn
              type="button"
              color="secondary"
              @click="resetForm"
              class="mr-3"
            >
              Reset Form
            </v-btn>

            <v-btn
              type="submit"
              color="primary"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Start Service
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>

  <!-- Server Error Message -->
  <v-alert v-if="serverError" type="error" class="my-4">
    {{ serverError }}
  </v-alert>
</template>

<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false, // Controls loading state of the button
      statusItems: ["Processing"],

      breakdown_service: {
        machine_id: null,
        technician_id: null,
        technician_name: "", // Store the technician's name
        breakdown_service_status: "Processing", // Default status
      },

      errors: {},
      serverError: null,
      machine_codes: [],
      technicianItems: [], // Array to store technician data
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },

  created() {
    // Fetch technician data on page load
    const serviceId = this.$route.params.uuid;
    this.fetchBreakdownService(serviceId);
  },

  methods: {
    async fetchMachineCodes(search = "") {
      try {
        const response = await this.$axios.get("/get_machine_codes", {
          params: { search },
        });
        this.machine_codes = response.data; // Populate machine codes
      } catch (error) {
        console.error("Error fetching machine codes:", error);
      }
    },

    // Fetch technician data
    async fetchTechnicians(search = "") {
      try {
        const response = await this.$axios.get("/technician", {
          params: { search },
        });
        this.technicianItems = response.data; // Populate technician list
      } catch (error) {
        console.error("Error fetching technicians:", error);
      }
    },

    // Fetch the breakdown service data for editing
    async fetchBreakdownService(serviceId) {
      try {
        const response = await this.$axios.get(`/breakdown-service/${serviceId}`);
        const serviceData = response.data;

        // Populate breakdown_service with the fetched data
        this.breakdown_service.machine_id = serviceData.machine_id;
        this.breakdown_service.technician_id = serviceData.technician_id;
        this.breakdown_service.technician_name = serviceData.technician
          ? serviceData.technician.name
          : ""; // Display technician's name if available
        this.breakdown_service.breakdown_service_status = serviceData.breakdown_service_status;

      } catch (error) {
        console.error("Error fetching breakdown service data:", error);
      }
    },

    async submit() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;

      const serviceId = this.$route.params.uuid;
      try {
        const response = await this.$axios.put(
          `/breakdown-service/${serviceId}`,
          this.breakdown_service
        );

        if (response.data.success) {
          toast.success("Breakdown Service Start successfully!");
          this.$router.push({ name: "ServiceIndex" }); // Redirect to the service index page
        }
      } catch (error) {
        this.errors = error.response.data.errors || {};
        toast.error("Failed to start Breakdown Service.");
      } finally {
        this.loading = false;
      }
    },

    resetForm() {
      this.breakdown_service = {
        machine_id: null,
        technician_id: null,
        technician_name: "",
        breakdown_service_status: "Processing",
      };
      this.errors = {};
    },
  },
};
</script>
