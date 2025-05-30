<template>
    <div>
        <h2>Modifier la localisation</h2>
        <div id="map" style="height: 400px;"></div>

        <p>Latitude : {{ lat }}</p>
        <p>Longitude : {{ lng }}</p>

        <button @click="saveLocation" :disabled="loading">
            {{ loading ? 'Enregistrement...' : 'Enregistrer les coordonnées' }}
        </button>

        <p v-if="message" :style="{ color: error ? 'red' : 'green' }">
            {{ message }}
        </p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'
import { useRoute } from 'vue-router'

const route = useRoute()
const locationId = route.params.id

const lat = ref()
const lng = ref()
const loading = ref(false)
const message = ref('')
const error = ref(false)

onMounted(async () => {
    try {
        const res = await axios.get(`/api/locations/${locationId}`)
        const data = res.data
        lat.value = data.coordinates.lat
        lng.value = data.coordinates.lng
        const map = L.map('map').setView([lat.value, lng.value], 13)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map)

        const marker = L.marker([lat.value, lng.value], { draggable: true }).addTo(map)

        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng()
            lat.value = pos.lat
            lng.value = pos.lng
        })

        map.on('click', function (e) {
            const pos = e.latlng
            marker.setLatLng(pos)
            lat.value = pos.lat
            lng.value = pos.lng
        })
    } catch (err) {
        console.error(err)
    }
})

async function saveLocation() {
    loading.value = true
    message.value = ''
    error.value = false

    try {
        await axios.put(`/api/locations/${locationId}`, {
            latitude: lat.value,
            longitude: lng.value,
            user: null,
        })

        message.value = 'Coordonnées mises à jour avec succès !'
    } catch (err) {
        console.error(err)
        message.value = 'Erreur lors de la mise à jour.'
        error.value = true
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
#map {
    margin-bottom: 1rem;
    border: 1px solid #ccc;
}
button {
    padding: 0.5rem 1rem;
    cursor: pointer;
}
</style>
