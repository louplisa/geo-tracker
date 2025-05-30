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
      <td>
        <input
            v-model="selectedLocations"
            :disabled="!selectedLocations.includes(location.id) && selectedLocations.length >= 2"
            :value="location.id"
            type="checkbox"
        />
      </td>
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
  <p>Localisations sélectionnées (max 2) : {{ selectedLocations }}</p>

  <button
      :disabled="selectedLocations.length !== 2"
      class="btn btn-success"
      @click="calculateDistance"
  >
    Calculer la distance entre les 2 points
  </button>

  <p v-if="distanceKm !== null" class="mt-3">
    Distance entre les deux localisations : <strong>{{ distanceKm.toFixed(3) }} km</strong>
  </p>
</template>

<script setup>
import {useRouter} from 'vue-router'
import axios from 'axios'
import {onMounted, ref} from "vue";

let locations = ref([])
const selectedLocations = ref([])
const message = ref('')
const distanceKm = ref(null)
const router = useRouter()

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
function haversineDistance(lat1, lng1, lat2, lng2) {
  const toRad = angle => (angle * Math.PI) / 180

  const R = 6371 // Rayon de la Terre en km
  const dLat = toRad(lat2 - lat1)
  const dLng = toRad(lng2 - lng1)

  const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
      Math.sin(dLng / 2) * Math.sin(dLng / 2)

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

  return R * c
}

function calculateDistance() {
  if (selectedLocations.value.length !== 2) {
    distanceKm.value = null
    return
  }

  const loc1 = locations.value.find(loc => loc.id === selectedLocations.value[0])
  const loc2 = locations.value.find(loc => loc.id === selectedLocations.value[1])

  if (!loc1 || !loc2) {
    distanceKm.value = null
    return
  }

  distanceKm.value = haversineDistance(
      loc1.coordinates.lat,
      loc1.coordinates.lng,
      loc2.coordinates.lat,
      loc2.coordinates.lng
  )
}
</script>

<style scoped>

</style>
