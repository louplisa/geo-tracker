<template>
    <div class="container">
        <h1>Editer la localisation : {{ form.name }}</h1>

        <div v-if="loading">Chargement...</div>
        <div v-else>
            <form @submit.prevent="updateLocation">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input v-model="form.name" id="name" class="form-control" />
                </div>

                <div class="mb-3">
                    <label for="lat" class="form-label">Latitude</label>
                    <input v-model="form.latitude" id="lat" class="form-control" type="number" step="any" />
                </div>

                <div class="mb-3">
                    <label for="lng" class="form-label">Longitude</label>
                    <input v-model="form.longitude" id="lng" class="form-control" type="number" step="any" />
                </div>

                <div class="mb-3">
                    <label for="user">Utilisateur (optionnel) :</label>
                    <select id="user" v-model="selectedUserId">
                        <option value="">-- Sélectionnez un utilisateur --</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>

            <div v-if="message" class="alert alert-success mt-3">{{ message }}</div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const locationId = route.params.id
const users = ref([])
const selectedUserId = ref('')
const loading = ref(true)
const form = ref({
    name: '',
    latitude: '',
    longitude: '',
    user: null,
})
const message = ref('')

onMounted(async () => {
    try {
        const response = await axios.get('/api/users')
        users.value = response.data
        const res = await axios.get(`/api/locations/${locationId}`)
        const data = res.data
        form.value.name = data.name
        form.value.latitude = data.coordinates.lat
        form.value.longitude = data.coordinates.lng
        form.value.user = data.user ? data.user.id : null
    } catch (err) {
        console.error('Erreur en chargeant la localisation', err)
    } finally {
        loading.value = false
    }
})

async function updateLocation() {
    try {
        form.value.user = selectedUserId.value ? Number(selectedUserId.value) : null
        await axios.put(`/api/locations/${locationId}`, {
            name: form.value.name,
            latitude: form.value.latitude,
            longitude: form.value.longitude,
            user: form.value.user,
        })
        message.value = 'Localisation mise à jour avec succès !'
        router.push('/locations')
    } catch (err) {
        console.error('Erreur lors de la mise à jour', err)
        message.value = 'Erreur lors de la mise à jour.'
    }
}
</script>

<style scoped>
/* Tes styles ici */
</style>
