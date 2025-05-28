<template>
    <nav class="navbar">
        <ul>
            <li><router-link to="/">Accueil</router-link></li>
            <li><router-link to="/map">Carte</router-link></li>
            <li><router-link to="/locations">Les localisations</router-link></li>
            <li><router-link to="/locations/create">Créer une localisation</router-link></li>
            <li><router-link to="/locations/nearby">Lieux proches</router-link></li>
        </ul>
    </nav>
</template>

<style scoped>
.navbar {
    background-color: #8a2a87;
    padding: 1rem;
}
ul {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}
li {
    color: white;
}
a.router-link-active {
    font-weight: bold;
    text-decoration: underline;
}
a {
    color: white;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
<script setup lang="ts">
</script>
