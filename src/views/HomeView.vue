<template>
  <div class="relative w-full h-full">
    <map-container />
<!--    <div class="absolute top-0 right-0 p-2" style="z-index: 9999">-->
<!--      <multi-select-->
<!--        v-model="selectedZips"-->
<!--        :options="zipOptions"-->
<!--        :max-selected-labels="3"-->
<!--        filter-->
<!--        auto-filter-focus-->
<!--        reset-filter-on-hide-->
<!--        display="chip"-->
<!--        :show-toggle-all="false"-->
<!--        option-label="label"-->
<!--        option-value="value"-->
<!--        placeholder="Zipcodes"-->
<!--        @change="onZipsChanged"-->
<!--      />-->
<!--    </div>-->
  </div>
</template>

<script setup>
import MapContainer from '@/components/MapContainer.vue'
// import MultiSelect from 'primevue/multiselect';
// import { ref } from 'vue'
// import { zips } from '@/zipcodes'
// import { useMapStore } from '@/stores/map'
// import { fetchZip } from '@/dawa';

// const mapStore = useMapStore()
// const zipCodeLayers = {};
// const selectedZips = ref([])
// const zipOptions = ref(zips)
// const onZipsChanged = async () => {
//     Object.keys(zipCodeLayers).forEach(zip => {
//         if (selectedZips.value.indexOf(zip) === -1) {
//             delete zipCodeLayers[zip];
//         }
//     });
//     for (const zip of selectedZips.value) {
//         if (!zipCodeLayers[zip]) {
//             const geojson = await fetchZip(zip);
//             zipCodeLayers[zip] = L.geoJSON(geojson);
//         }
//     }
//     mapStore.mapLayers.ZipCodesLayer.clearLayers();
//     Object.values(zipCodeLayers).forEach(layer => mapStore.mapLayers.ZipCodesLayer.addLayer(layer));
//     fitToBounds();
// }
// const fitToBounds = () => {
//     let bounds;
//     mapStore.mapLayers.ZipCodesLayer.eachLayer(l => {
//         if (bounds) {
//             bounds.extend(l.getBounds())
//         } else {
//             bounds = l.getBounds();
//         }
//     });
//     if (bounds) {
//         mapStore.SAMap.fitBounds(bounds);
//     }
// };
</script>

<style scoped></style>
