<template>
    <div class="map-selector">
        <div class="controls">
            <label for="location">Choisir un lieu :</label>
            <select v-model="selectedId" @change="fetchLocation">
                <option v-for="location in locations" :key="location.id" :value="location.id">
                    {{ location.name }}
                </option>
            </select>
        </div>
        <div ref="map" class="map-container"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'

const map = ref(null)
const mapInstance = ref(null)
const marker = ref(null)

const selectedId = ref(null)
const locations = ref([])

// Charger toutes les localisations (ex: depuis `/api/locations`)
const fetchLocations = async () => {
    try {
        const response = await axios.get('/api/locations')
        locations.value = response.data
    } catch (error) {
        console.error('Erreur lors du chargement des lieux:', error)
    }
}

// Charger la localisation sélectionnée
const fetchLocation = async () => {
    if (!selectedId.value) return

    try {
        const response = await axios.get(`/api/locations/${selectedId.value}`)
        const loc = response.data
        const lat = loc.coordinates.lat
        const lng = loc.coordinates.lng

        mapInstance.value.setView([lat, lng], 13)

        if (marker.value) {
            marker.value.setLatLng([lat, lng])
        } else {
            marker.value = L.marker([lat, lng]).addTo(mapInstance.value)
        }

        marker.value.bindPopup(`<b>${loc.name}</b>`).openPopup()
    } catch (error) {
        console.error('Erreur lors du chargement de la localisation:', error)
    }
}

// Initialiser la carte
onMounted(() => {
    mapInstance.value = L.map(map.value).setView([0, 0], 2)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapInstance.value)

    fetchLocations()
})
</script>

<style scoped>
.map-selector {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.controls {
    display: flex;
    flex-direction: column;
    max-width: 300px;
}

.map-container {
    height: 800px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 8px;
}
</style>
