<template>
    <div class="location-create">
        <h2>Créer une nouvelle location</h2>

        <form @submit.prevent="submitForm">
            <div>
                <label for="name">Nom :</label>
                <input id="name" v-model="name" required />
            </div>

            <div>
                <label for="lat">Latitude :</label>
                <input id="lat" type="number" step="any" v-model.number="lat" required />
            </div>

            <div>
                <label for="lng">Longitude :</label>
                <input id="lng" type="number" step="any" v-model.number="lng" required />
            </div>

            <button type="submit" :disabled="loading">
                {{ loading ? 'Création...' : 'Créer' }}
            </button>
        </form>

        <p v-if="message" :class="{ error: error }">{{ message }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const name = ref('')
const lat = ref(null)
const lng = ref(null)
const loading = ref(false)
const message = ref('')
const error = ref(false)

async function submitForm() {
    if (!name.value || lat.value === null || lng.value === null) {
        message.value = 'Tous les champs sont obligatoires.'
        error.value = true
        return
    }

    loading.value = true
    message.value = ''
    error.value = false

    try {
        // Construire WKT POINT
        const wkt = `POINT(${lng.value} ${lat.value})`

        // Appel POST vers API Laravel
        await axios.post('/api/locations', {
            name: name.value,
            wkt: wkt,
        })

        message.value = 'Location créée avec succès !'
        error.value = false
        // Reset form
        name.value = ''
        lat.value = null
        lng.value = null

    } catch (e) {
        message.value = 'Erreur lors de la création.'
        error.value = true
    } finally {
        loading.value = false
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

input {
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
