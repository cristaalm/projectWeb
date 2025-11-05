import { defineStore } from 'pinia'
import { router } from '@/plugins/router'

const useMenuStore = defineStore('menu', {
  state: () => ({
    value: [],
  }),
  actions: {
    async loadMenu(user) {
      if (!user || !user.role ) router.push({ name: 'logout' })
        
      const acceptRoles = ['admin', 'moderator'] 

      if (!acceptRoles.includes(user.role.name)) router.push({ name: 'logout' })

      this.value = user.role.name == "admin" ? this.adminMenu() : this.moderatorMenu()
    },
    moderatorMenu() {
      return [
        { title: 'Panel', icon: 'bx-bxs-dashboard', to: '/panel' },
        { title: 'Usuarios', icon: 'bx-user', to: '/users' },
        { title: 'Contenedores', icon: 'bx-purchase-tag-alt', to: '/containers' },
        { title: 'Comercios', icon: 'bx-building-house', to: '/shops' },
        { title: 'Recompensas', icon: 'bx-gift', to: '/rewards' },
        { title: 'Insignias', icon: 'bx-bell', to: '/badges' },
      ]
    },
    adminMenu() {
      return [
        { title: 'Panel', icon: 'bx-bxs-dashboard', to: '/panel' },
        { title: 'Usuarios', icon: 'bx-user', to: '/users' },
        { title: 'Contenedores', icon: 'bx-purchase-tag-alt', to: '/containers' },
        { title: 'Comercios', icon: 'bx-building-house', to: '/shops' },
        { title: 'Recompensas', icon: 'bx-bell', to: '/rewards' },
        { title: 'Insignias', icon: 'bx-bell', to: '/badges' },
      ]
    },
  },
})

export default useMenuStore
