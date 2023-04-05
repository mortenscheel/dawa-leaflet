import { shallowRef, markRaw } from 'vue'
import L from 'leaflet'
import { defineStore } from 'pinia'

export const useMapStore = defineStore('map-store', {
  state: () => {
    return {
      SAMap: shallowRef(null),
      mapLayers: markRaw({
        ZipCodesLayer: L.layerGroup()
      })
    }
  },

  actions: {
    setSAMap(map) {
      this.SAMap = map
    }
  }
})
