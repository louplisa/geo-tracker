import { createRouter, createWebHistory } from 'vue-router'
import MapSelector from '../components/MapSelector.vue'
import LocationCreate from "../components/LocationCreate.vue";
import MapViewer from "../components/MapViewer.vue";
import NearbyLocations from "../components/NearbyLocations.vue";
import LocationTable from "../components/LocationTable.vue";
import LocationEdit from "../components/LocationEdit.vue";
import LocationShow from "../components/LocationShow.vue";
import DistanceCalculator from "../components/DistanceCalculator.vue";

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
        path: '/locations/',
        name: 'location-index',
        component: LocationTable,
    },
    {
        path: '/locations/create',
        name: 'location-create',
        component: LocationCreate,
    },
    {
        path: '/locations/edit/:id',
        name: 'location-edit',
        component: LocationEdit,
    },
    {
        path: '/locations/show/:id',
        name: 'location-show',
        component: LocationShow,
    },
    {
        path: '/locations/nearby',
        name: 'location-nearby',
        component: NearbyLocations,
    },
    {
        path: '/locations/distance-calculator',
        name: 'location-distance-calculator',
        component: DistanceCalculator,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
