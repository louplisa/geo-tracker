<template>
    <div id="map" style="height: 800px;"></div>
</template>

<script setup>
import L from 'leaflet'
import {onMounted} from "vue";

onMounted(async () => {
    const map = new L.Map('map').setView([0, 0], 3)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map)

    const res = await fetch('/api/locations')
    const locations = await res.json()

    locations.forEach(loc => {
        L.marker([loc.coordinates.lat, loc.coordinates.lng])
            .addTo(map)
            .bindPopup(`<b>${loc.name}</b>`)

    })
})
</script>

<style scoped>

</style>
