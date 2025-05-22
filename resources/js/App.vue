<template>
    <div class="app">
        <h1>GeoTracker</h1>
        <NavBar/>
        <router-view />
    </div>
</template>

<script setup>
import NavBar from "./components/NavBar.vue";
</script>
