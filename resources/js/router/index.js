import { createRouter, createWebHistory } from 'vue-router'
import MapSelector from '../components/MapSelector.vue'
import LocationCreate from "../components/LocationCreate.vue";
import MapViewer from "../components/MapViewer.vue";

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
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
