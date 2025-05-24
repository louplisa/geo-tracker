<template>
    <div class="nearby-locations">
        <h2>Rechercher des lieux proches</h2>

        <form @submit.prevent="fetchNearby">
            <label>Latitude :</label>
            <input v-model.number="lat" type="number" step="any" required />

            <label>Longitude :</label>
            <input v-model.number="lng" type="number" step="any" required />

            <label>Rayon (km) :</label>
            <input v-model.number="radius" type="number" step="any" required />

            <button type="submit">Rechercher</button>
        </form>

        <div v-if="locations.length">
            <h3>Lieux trouvés :</h3>
            <ul>
                <li v-for="loc in locations" :key="loc.id">
                    📍 <strong>{{ loc.name }}</strong> – ({{ loc.lat }}, {{ loc.lng }})
                </li>
            </ul>
        </div>

        <p v-if="error" class="error">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const lat = ref(48.8566)
const lng = ref(2.3522)
const radius = ref(5)

const locations = ref([])
const error = ref('')

const fetchNearby = async () => {
    error.value = ''
    try {
        const response = await axios.get('/api/locations/nearby', {
            params: {
                lat: lat.value,
                lng: lng.value,
                radius: radius.value,
            },
        })
        locations.value = response.data
    } catch (err) {
        error.value = "Erreur lors du chargement des lieux proches."
    }
}
</script>

<style scoped>
.nearby-locations {
    max-width: 500px;
    margin: 2rem auto;
}
label {
    display: block;
    margin-top: 0.5rem;
}
input {
    width: 100%;
    padding: 0.25rem;
}
.error {
    color: red;
    margin-top: 1rem;
}
</style>
