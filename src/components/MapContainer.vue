<template>
  <div id="mapRoot"></div>
</template>

<script setup>
import 'leaflet/dist/leaflet.css'

import { onMounted } from 'vue'
import { useMapStore } from '@/stores/map'

const mapStore = useMapStore()

const setupMap = () => {
  const openStreetMaps = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attributions: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  })

  const map = L.map('mapRoot', {
    renderer: L.canvas(),
    preferCanvas: true,
    layers: [openStreetMaps],
    markerZoomAnimation: true,
    attributionControl: false,
    zoomAnimation: false,
    fadeAnimation: true,
    zoomControl: true,
    // minZoom: 5,
  }).setView([56.09349156919378, 9.678955078125002], 7)

  mapStore.setMap(map)

  // adding other layers to map
  for (let i = 0; i < Object.entries(mapStore.layerGroups).length; ++i) {
    if (Object.entries(mapStore.layerGroups)[i] !== undefined) {
      mapStore.map.addLayer(Object.entries(mapStore.layerGroups)[i][1])
    }
  }
}

onMounted(() => {
  setupMap()
})
</script>

<style>
#mapRoot {
  height: 100%;
  width: 100%;
}

/*@supports(height: 100dvh) {*/
/*    #mapRoot {*/
/*        height: 100dvh;*/
/*    }*/
/*}*/
</style>
