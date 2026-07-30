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

      this.value = this.panelMenu().filter(
        item => !item.roles || item.roles.includes(user.role.name),
      )
    },
    panelMenu() {
      return [
        { title: 'Panel', icon: 'bx-bxs-dashboard', to: '/panel' },
        { title: 'Usuarios', icon: 'bx-user', to: '/usuarios', roles: ['superadmin', 'moderador'] },
      ]
    },
  },
})

export default useMenuStore
