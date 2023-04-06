import { shallowRef, markRaw } from 'vue'
import L from 'leaflet'
import { defineStore } from 'pinia'

export const useMapStore = defineStore('map-store', {
  state: () => {
    return {
      map: shallowRef(null),
      layerGroups: markRaw({
        zipcodes: L.layerGroup(),
        municipalities: L.layerGroup(),
        regions: L.layerGroup(),
        police: L.layerGroup(),
        courts: L.layerGroup(),
        churches: L.layerGroup(),
        elections: L.layerGroup()
      })
    }
  },

  actions: {
    setMap(map) {
      this.map = map
    },
  }
});
