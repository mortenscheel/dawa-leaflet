<template>
  <div class="layout-wrapper layout-static">
    <div class="layout-sidebar">
      <div class="flex flex-column gap-2">
        <multi-select
          v-model="selected.zipcodes"
          :options="options.zipcodes"
          :max-selected-labels="3"
          filter
          class="w-full bg-green-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Postnumre"
          @change="onEntitySelectionChanged('zipcodes', $event)"
        />
        <multi-select
          v-model="selected.municipalities"
          :options="options.municipalities"
          :max-selected-labels="3"
          filter
          class="w-full bg-yellow-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Kommuner"
          @change="onEntitySelectionChanged('municipalities')"
        />
        <multi-select
          v-model="selected.regions"
          :options="options.regions"
          :max-selected-labels="3"
          filter
          class="w-full bg-pink-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Regioner"
          @change="onEntitySelectionChanged('regions')"
        />
        <multi-select
          v-model="selected.police"
          :options="options.police"
          :max-selected-labels="3"
          filter
          class="w-full bg-blue-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Politikredse"
          @change="onEntitySelectionChanged('police')"
        />
        <multi-select
          v-model="selected.courts"
          :options="options.courts"
          :max-selected-labels="3"
          filter
          class="w-full bg-red-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Retskredse"
          @change="onEntitySelectionChanged('courts')"
        />
        <multi-select
          v-model="selected.churches"
          :options="options.churches"
          :max-selected-labels="3"
          filter
          class="w-full bg-cyan-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Sogne"
          @change="onEntitySelectionChanged('churches')"
        />
        <multi-select
          v-model="selected.elections"
          :options="options.elections"
          :max-selected-labels="3"
          filter
          class="w-full bg-indigo-200"
          auto-filter-focus
          reset-filter-on-hide
          display="chip"
          :show-toggle-all="false"
          option-label="label"
          option-value="value"
          placeholder="Storkredse"
          @change="onEntitySelectionChanged('elections')"
        />
      </div>
    </div>
    <div class="layout-main-container">
      <div ref="main" class="layout-main relative" :style="{ height: `${mainHeight}px` }">
        <div id="mapRoot"></div>
        <div
          v-if="loading"
          class="absolute top-0 bottom-0 right-0 left-0 flex align-items-center justify-content-center"
          style="z-index: 999; background-color: rgba(255, 255, 255, 0.4)"
        >
          <progress-spinner />
        </div>
      </div>
    </div>
    <!--    <app-config></app-config>-->
    <div class="layout-mask"></div>
  </div>
</template>

<script setup>
import L from 'leaflet'
import { onMounted, ref } from 'vue'
import 'leaflet/dist/leaflet.css'
import { fetchGeoJson, options } from '@/dawa'
import MultiSelect from 'primevue/multiselect'
import ProgressSpinner from 'primevue/progressspinner'
import { useResizeObserver } from '@vueuse/core'

let map
const layers = {
  zipcodes: L.layerGroup(),
  municipalities: L.layerGroup(),
  regions: L.layerGroup(),
  police: L.layerGroup(),
  courts: L.layerGroup(),
  churches: L.layerGroup(),
  elections: L.layerGroup()
}

const setupMap = () => {
  const openStreetMaps = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attributions: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  })

  map = L.map('mapRoot', {
    renderer: L.canvas(),
    preferCanvas: true,
    layers: [openStreetMaps],
    markerZoomAnimation: true,
    attributionControl: false,
    zoomAnimation: false,
    fadeAnimation: true,
    zoomControl: true
    // minZoom: 5,
  }).setView([56.09349156919378, 9.678955078125002], 7)
  map.on('movestart', () => {
    document.getElementById('app').classList.add('moving')
  })
  map.on('moveend', () => {
    document.getElementById('app').classList.remove('moving')
  })
  Object.values(layers).forEach((group) => map.addLayer(group))
}

onMounted(() => {
  setupMap()
})
const selected = ref({
  zipcodes: [],
  municipalities: [],
  regions: [],
  police: [],
  courts: [],
  churches: [],
  elections: []
})
const loading = ref(false)
const onEntitySelectionChanged = async (entity, e) => {
  console.log(e)
  let endpoint
  let color
  switch (entity) {
    case 'zipcodes':
      endpoint = 'postnumre'
      color = '#4cd07d'
      break
    case 'municipalities':
      endpoint = 'kommuner'
      color = '#eec137'
      break
    case 'regions':
      endpoint = 'regioner'
      color = '#f06bac'
      break
    case 'police':
      endpoint = 'politikredse'
      color = '#609af8'
      break
    case 'courts':
      endpoint = 'retskredse'
      color = '#ff6259'
      break
    case 'churches':
      endpoint = 'sogne'
      color = '#35c4dc'
      break
    case 'elections':
      endpoint = 'storkredse'
      color = '#8183f4'
      break
    default:
      throw new Error('Unexpected entity')
  }
  loading.value = true
  layers[entity].clearLayers()
  for (const id of selected.value[entity]) {
    const geojson = await fetchGeoJson(endpoint, id)
    const layer = L.geoJSON(geojson, {
      style: {
        color: color,
        fillOpacity: 0.5
      }
    })
    layers[entity].addLayer(layer)
  }
  loading.value = false
  fitToBounds()
}
const fitToBounds = () => {
  let bounds
  Object.values(layers).forEach((group) => {
    group.eachLayer((layer) => {
      if (bounds) {
        bounds.extend(layer.getBounds())
      } else {
        bounds = layer.getBounds()
      }
    })
  })
  if (bounds) {
    map.fitBounds(bounds)
  }
}
let mainHeight = ref(0)
const main = ref(null)
useResizeObserver(main, (entries) => {
  const entry = entries[0]
  mainHeight.value = entry.contentRect.height
})
</script>

<style scoped>
.layout-main-container {
  padding-top: 1rem;
  padding-bottom: 1rem;
  padding-right: 1rem;
  padding-left: 2rem;
}

.layout-sidebar {
  top: 1rem;
  left: 1rem;
  height: calc(100vh - 7rem);
  background-color: transparent;
  box-shadow: none;
  padding: 0;
}

#mapRoot {
  height: 100%;
  width: 100%;
}
</style>
