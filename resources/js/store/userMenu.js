import { defineStore } from 'pinia'

const useMenuStore = defineStore('menu', {
  state: () => ({
    value: [],
  }),
  actions: {
    async loadMenu(user) {
      if (!user || !user.role ) return []
        
      this.value = user.role.id === 2 ? this.adminMenu() : this.userMenu()
    },
    userMenu() {
      return [
        {
          title: 'Panel',
          icon: 'bx-bxs-dashboard',
          to: '/panel',
        },
        'Mi Perfil',
        {
          title: 'Mi información',
          icon: 'bx-user',
          to: '/perfil/informacion',
        },
        {
          title: 'Gestion de tiempo',
          icon: 'bx-time-five',
          to: '/perfil/tiempo',
        },
        {
          title: 'Tareas',
          icon: 'bx-task',
          to: '/perfil/tareas',
        },
        'Empresa',
        {
          title: 'Organigrama',
          icon: 'bx-building-house',
          to: '/empresa/organigrama',
        },
        {
          title: 'Directorio',
          icon: 'bx-user-pin',
          to: '/empresa/directorio',
        },
        {
          title: 'Comunicación',
          icon: 'bx-chat',
          to: '/empresa/comunicacion',
        },
        {
          title: 'Reclutamiento',
          icon: 'bx-user-plus',
          menu: [
            {
              title: 'Reclutamiento y seleccion',
              to: '/empresa/reclutamiento',
            },
            {
              title: 'Onboarding',
              to: '/empresa/onboarding',
            },
          ],
        },
        {
          title: 'Capacitaciones',
          icon: 'bx-book-reader',
          to: '/empresa/capacitacion',
        },
        {
          title: 'Clima Laboral',
          icon: 'bx-smile',
          to: '/empresa/clima',
        },
        {
          title: 'Evaluación de desempeño',
          icon: 'bx-star',
          to: '/empresa/evaluacion',
        },
        'Configuraciones',
        {
          title: 'Configuración',
          icon: 'bx-cog',
          menu: [
            {
              title: 'Empresa',
              to: '/configuraciones/empresa',
            },
            {
              title: 'Personal',
              to: '/configuraciones/personal',
            },
            {
              title: 'Tiempo',
              to: '/configuraciones/tiempo',
            },
          ],
        },
      ]
    },
    adminMenu() {
      return [
        {
          title: 'Panel',
          icon: 'bx-bxs-dashboard',
          to: '/panel',
        },
        {
          title: 'Usuarios',
          icon: 'bx-user',
          to: '/users',
        },
        {
          title: 'Contenedores',
          icon: 'bx-purchase-tag-alt',
          to: '/containers',
        },
        {
          title: 'Comercios',
          icon: 'bx-building-house',
          to: '/shops',
        },
        {
          title: 'Recompensas',
          icon: 'bx-bell',
          to: '/rewards',
        },
      ]
    },
  },
})

export default useMenuStore
