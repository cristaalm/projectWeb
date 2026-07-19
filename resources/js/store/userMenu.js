import { defineStore } from 'pinia'
import { router } from '@/plugins/router'

const useMenuStore = defineStore('menu', {
  state: () => ({
    value: [],
  }),
  actions: {
    async loadMenu(user) {
      if (!user || !user.role) {
        router.push({ name: 'logout' })

        return
      }

      this.value = this.panelMenu()
    },
    panelMenu() {
      return [
        { title: 'Panel', icon: 'bx-bxs-dashboard', to: '/panel' },
      ]
    },
  },
})

export default useMenuStore
