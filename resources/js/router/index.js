import { createRouter, createWebHistory } from 'vue-router'
import MapSelector from '../components/MapSelector.vue'
import LocationCreate from "../components/LocationCreate.vue";
import MapViewer from "../components/MapViewer.vue";
import NearbyLocations from "../components/NearbyLocations.vue";

const routes = [
    {
        path: '/',
        name: 'home',
        component: MapSelector,
    },
    {
        path: '/map',
        name: 'Map',
        component: MapViewer
    },
    {
        path: '/locations/create',
        name: 'location-create',
        component: LocationCreate,
    },
    {
        path: '/locations/nearby',
        name: 'location-nearby',
        component: NearbyLocations,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
