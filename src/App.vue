<template>
  <div class="layout-wrapper layout-static">
    <div class="layout-sidebar">
      <div class="flex flex-column gap-3">
        <template v-for="(entity, i) in entities" :key="i">
          <multi-select
            v-model="selected[entity.name]"
            :options="options[entity.name]"
            :max-selected-labels="0"
            :selected-items-label="`${entity.placeholder} - {0} valgt`"
            filter
            class="w-full"
            auto-filter-focus
            reset-filter-on-hide
            :show-toggle-all="false"
            option-label="label"
            option-value="value"
            :placeholder="entity.placeholder"
            @change="onEntitySelectionChanged(entity.name)"
            :style="`--color: ${entity.color}`"
          />
        </template>
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
  <div class="bg-green-200" style="display: none"></div>
</template>

<script setup>
import L from 'leaflet';
import { onMounted, ref } from 'vue';
import 'leaflet/dist/leaflet.css';
import { fetchGeoJson, options } from '@/dawa';
import MultiSelect from 'primevue/multiselect';
import ProgressSpinner from 'primevue/progressspinner';

let map;
const entities = [
  { name: 'zipcodes', placeholder: 'Postnumre', color: '#4cd07d', endpoint: 'postnumre' },
  { name: 'municipalities', placeholder: 'Kommuner', color: '#eec137', endpoint: 'kommuner' },
  { name: 'regions', placeholder: 'Regioner', color: '#f06bac', endpoint: 'regioner' },
  { name: 'police', placeholder: 'Politikredse', color: '#609af8', endpoint: 'politikredse' },
  { name: 'courts', placeholder: 'Retskredse', color: '#ff6259', endpoint: 'retskredse' },
  { name: 'churches', placeholder: 'Sogne', color: '#35c4dc', endpoint: 'sogne' },
  { name: 'elections', placeholder: 'Storkredse', color: '#8183f4', endpoint: 'storkredse' }
];
const colors = [];
const layers = {
  zipcodes: L.layerGroup(),
  municipalities: L.layerGroup(),
  regions: L.layerGroup(),
  police: L.layerGroup(),
  courts: L.layerGroup(),
  churches: L.layerGroup(),
  elections: L.layerGroup()
};
const selected = ref({
  zipcodes: [],
  municipalities: [],
  regions: [],
  police: [],
  courts: [],
  churches: [],
  elections: []
});
const loading = ref(false);

const setupMap = () => {
  const openStreetMaps = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attributions: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  });

  map = L.map('mapRoot', {
    renderer: L.canvas(),
    preferCanvas: true,
    layers: [openStreetMaps],
    markerZoomAnimation: true,
    attributionControl: false,
    zoomAnimation: false,
    fadeAnimation: true,
    zoomControl: true
  }).setView([56.09349156919378, 9.678955078125002], 7);
  Object.values(layers).forEach((group) => map.addLayer(group));
};

const onEntitySelectionChanged = async (entityName) => {
  const entity = entities.find((e) => e.name === entityName);
  loading.value = true;
  layers[entity.name].clearLayers();
  for (const id of selected.value[entity.name]) {
    const geojson = await fetchGeoJson(entity.endpoint, id);
    console.log(`var(--${entity.color}-400)`);
    const layer = L.geoJSON(geojson, {
      style: {
        color: entity.color,
        fillOpacity: 0.5
      }
    });
    layers[entity.name].addLayer(layer);
  }
  loading.value = false;
  fitToBounds();
};

const fitToBounds = () => {
  let bounds;
  Object.values(layers).forEach((group) => {
    group.eachLayer((layer) => {
      if (bounds) {
        bounds.extend(layer.getBounds());
      } else {
        bounds = layer.getBounds();
      }
    });
  });
  if (bounds) {
    map.fitBounds(bounds);
  }
};

onMounted(() => {
  setupMap();
});
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
  height: calc(100vh - 28px);
  width: 100%;
}
</style>
