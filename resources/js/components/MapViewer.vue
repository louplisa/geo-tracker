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
    const groupedByPoint = new Map()

    locations.forEach(loc => {
        const key = `${loc.name}-${loc.coordinates.lat}-${loc.coordinates.lng}`
        if (!groupedByPoint.has(key)) {
            groupedByPoint.set(key, {
                name: loc.name,
                lat: loc.coordinates.lat,
                lng: loc.coordinates.lng,
                users: []
            })
        }

        const entry = groupedByPoint.get(key)

        if (loc.user) {
            entry.users.push(loc.user.name)
        }
    })

    groupedByPoint.forEach(entry => {
        const userList = entry.users.length > 0
            ? '<br /><b>Utilisateurs :</b><br />' + entry.users.map(u => `👤 ${u}`).join('<br />')
            : '<br /><i>Aucun utilisateur</i>'

        L.marker([entry.lat, entry.lng])
            .addTo(map)
            .bindPopup(`<b>${entry.name}</b>${userList}`)
    })
})
</script>

<style scoped>

</style>
