<template>
    <h1>Les localisations</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Utilisateurs</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="location in locations" :key="location.id">
            <td>{{ location.name }}</td>
            <td>{{ location.coordinates.lat }}</td>
            <td>{{ location.coordinates.lng }}</td>
            <td v-if="location.user">{{ location.user.name }}</td>
            <td v-else>Pas d'utilisateur</td>
            <td>
                <div class="d-flex gap-2 w-100">
                    <a :href="`/locations/show/${location.id}`" class="btn btn-secondary">Voir</a>
                    <a :href="`/locations/edit/${location.id}`" class="btn btn-primary">Editer</a>
                    <button class="btn btn-danger" @click="deleteLocation(location.id)">Supprimer</button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'

let locations = ref([])
const message = ref('')
const router = useRouter()

import {onMounted, ref} from "vue";

onMounted(async () => {
    const res = await fetch('/api/locations')
    locations.value = await res.json()
})

async function deleteLocation(locationId) {
  try {
    await axios.delete(`/api/locations/${locationId}`)
    message.value = 'Localisation supprimée avec succès !'
    await fetchLocations()
  } catch (err) {
    console.error('Erreur lors de la suppression', err)
    message.value = 'Erreur lors de la suppression.'
  }
}

async function fetchLocations() {
  const res = await axios.get('/api/locations')
  locations.value = res.data
}
</script>

<style scoped>

</style>
