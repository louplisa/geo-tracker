<template>
    <div>
        <h2>Calculer la distance entre deux points</h2>
        <div id="map" style="height: 400px;"></div>

        <p v-if="pointA && pointB">
            Point A : {{ pointA.lat.toFixed(4) }}, {{ pointA.lng.toFixed(4) }}<br>
            Point B : {{ pointB.lat.toFixed(4) }}, {{ pointB.lng.toFixed(4) }}<br>
            Distance : {{ distance.toFixed(2) }} km
        </p>
        <p v-else>
            Cliquez sur la carte pour définir les deux points.
        </p>

        <button @click="resetPoints" class="btn btn-warning">Réinitialiser</button>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const pointA = ref(null)
const pointB = ref(null)
const markers = []

const distance = computed(() => {
    if (!pointA.value || !pointB.value) return 0
    return haversineDistance(pointA.value.lat, pointA.value.lng, pointB.value.lat, pointB.value.lng)
})

function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371 // km
    const toRad = deg => deg * Math.PI / 180

    const dLat = toRad(lat2 - lat1)
    const dLon = toRad(lon2 - lon1)

    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

    return R * c
}

function resetPoints() {
    pointA.value = null
    pointB.value = null
    markers.forEach(m => m.remove())
    markers.length = 0
}

onMounted(() => {
    const map = L.map('map').setView([48.8566, 2.3522], 5) // Vue initiale : France

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map)

    map.on('click', e => {
        const { lat, lng } = e.latlng
        if (!pointA.value) {
            pointA.value = { lat, lng }
            const marker = L.marker([lat, lng], { color: 'blue' }).addTo(map).bindPopup('Point A').openPopup()
            markers.push(marker)
        } else if (!pointB.value) {
            pointB.value = { lat, lng }
            const marker = L.marker([lat, lng], { color: 'red' }).addTo(map).bindPopup('Point B').openPopup()
            markers.push(marker)
            drawLine(map)
        }
    })

    function drawLine(map) {
        if (pointA.value && pointB.value) {
            const latlngs = [
                [pointA.value.lat, pointA.value.lng],
                [pointB.value.lat, pointB.value.lng]
            ]
            const polyline = L.polyline(latlngs, { color: 'green' }).addTo(map)
            markers.push(polyline)
        }
    }
})
</script>

<style scoped>
#map {
    margin-bottom: 1rem;
    border: 1px solid #ccc;
}
button {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
}
</style>
