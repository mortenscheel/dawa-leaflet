<template>
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
      @change="onEntitySelectionChanged('zipcodes')"
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
      placeholder="Politikreds"
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
</template>

<script setup>
import { ref } from 'vue'

import MultiSelect from 'primevue/multiselect'

import { fetchGeoJson, options } from '@/dawa'
import { useMapStore } from '@/stores/map'

const mapStore = useMapStore()
const groups = mapStore.layerGroups
const selected = ref({
  zipcodes: [],
  municipalities: [],
  regions: [],
  police: [],
  courts: [],
  churches: [],
  elections: []
})
const onEntitySelectionChanged = async (entity) => {
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
  mapStore.layerGroups[entity].clearLayers()
  for (const id of selected.value[entity]) {
    const geojson = await fetchGeoJson(endpoint, id)
    const layer = L.geoJSON(geojson, {
      style: {
        color: color,
          fillOpacity: 0.5
      }
    })
    mapStore.layerGroups[entity].addLayer(layer)
  }
  fitToBounds()
}
const fitToBounds = () => {
  let bounds
  Object.values(groups).forEach((group) => {
    group.eachLayer((layer) => {
      if (bounds) {
        bounds.extend(layer.getBounds())
      } else {
        bounds = layer.getBounds()
      }
    })
  })
  if (bounds) {
    mapStore.map.fitBounds(bounds)
  }
}
</script>

<style lang="scss" scoped>
* {
  font-weight: bold;
}
</style>
