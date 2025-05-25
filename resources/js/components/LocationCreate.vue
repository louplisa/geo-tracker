<template>
    <div class="location-create">
        <h2>Créer une nouvelle localisation</h2>
        <form @submit.prevent="submitForm">
            <div>
                <label for="name">Nom de la ville :</label>
                <input id="name" v-model="name" required/>
                <div v-if="fieldErrors.name">
                    <p v-for="err in fieldErrors.name" :key="err" class="error">{{ err }}</p>
                </div>
            </div>
            <div>
                <label for="user">Utilisateur (optionnel) :</label>
                <select id="user" v-model="selectedUserId">
                    <option value="">-- Sélectionnez un utilisateur --</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
                <div v-if="fieldErrors.user">
                    <p class="error" v-for="err in fieldErrors.user" :key="err">{{ err }}</p>
                </div>
            </div>
            <h3>Par coordonnées GPS</h3>
            <div>
                <label for="lat">Latitude :</label>
                <input id="lat" v-model.number="lat" step="any" type="number"/>
            </div>
            <div>
                <label for="lng">Longitude :</label>
                <input id="lng" v-model.number="lng" step="any" type="number"/>
            </div>

            <h3>Par code postal (villes françaises uniquement)</h3>
            <div>
                <label for="zipCode">Code postal :</label>
                <input id="zipCode" v-model="zipCode"/>
            </div>
            <button :disabled="loading || isFormInvalid" type="submit">
                <span v-if="loading">⏳ Création...</span>
                <span v-else>Créer</span>
            </button>
        </form>

        <p v-if="message" :class="{ error: error }">{{ message }}</p>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue'
import axios from 'axios'

const name = ref('')
const lat = ref(null)
const lng = ref(null)
const zipCode = ref('')
const selectedUserId = ref('')
const users = ref([])
const loading = ref(false)
const message = ref('')
const error = ref(false)
const fieldErrors = ref({})

const isFormInvalid = computed(() => {
    const hasName = !!name.value
    const hasZipCode = !!zipCode.value
    const hasCoordinates = lat.value !== null && lng.value !== null

    return !hasName || (hasZipCode === hasCoordinates)
})

onMounted(async () => {
    try {
        const res = await axios.get('/api/users')
        users.value = res.data
    } catch {
        message.value = 'Erreur lors du chargement des utilisateurs.'
        error.value = true
    }
})

function resetForm() {
    name.value = ''
    lat.value = null
    lng.value = null
    zipCode.value = ''
    selectedUserId.value = ''
    fieldErrors.value = {}
}

async function postLocation(payload, url) {
    try {
        await axios.post(url, payload)
        message.value = 'Localisation créée avec succès !'
        error.value = false
        resetForm()
    } catch (e) {
        if (e.response && e.response.status === 422) {
            fieldErrors.value = e.response.data.errors
        } else {
            message.value = 'Erreur lors de la création.'
        }
        error.value = true
    } finally {
        loading.value = false
    }
}

function validateForm() {
    if (!name.value) {
        message.value = 'Le nom est obligatoire.'
        error.value = true
        return false
    }
    if (!zipCode.value && (!lat.value || !lng.value)) {
        message.value = 'Veuillez renseigner soit un code postal, soit des coordonnées GPS complètes.'
        error.value = true
        return false
    }
    if (zipCode.value && (lat.value || lng.value)) {
        message.value = 'Vous devez choisir soit par code postal, soit par coordonnées GPS, pas les deux.'
        error.value = true
        return false
    }
    return true
}

async function submitForm() {
    if (!validateForm()) return

    loading.value = true
    message.value = ''
    error.value = false

    const payload = {
        name: name.value,
        user: selectedUserId.value ? Number(selectedUserId.value) : null,
    }

    if (zipCode.value) {
        payload.zipCode = zipCode.value
        await postLocation(payload, '/api/locations/name-and-zip-code')
    } else {
        payload.wkt = `POINT(${lng.value} ${lat.value})`
        await postLocation(payload, '/api/locations')
    }
}
</script>

<style scoped>
.location-create {
    max-width: 400px;
    margin: 1rem auto;
}

label {
    display: block;
    margin-bottom: 0.25rem;
}

input, select {
    width: 100%;
    padding: 0.3rem;
    margin-bottom: 1rem;
    box-sizing: border-box;
}

button {
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.error {
    color: red;
}
</style>
