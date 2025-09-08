import useAuthToken from '@/hooks/Auth/useAuthToken'
import { useAuthStore } from '@/store/auth'
import { ref } from 'vue'

const flagAuth = ref(false)

export const routes = [
  { path: '/', redirect: { name: 'login' } },
  {
    path: '/logout',
    name: 'logout',
    redirect: { name: 'login' },
    beforeEnter: async (to, from, next) => {
      // deslogueamos al usuario
      useAuthStore().logout()
      flagAuth.value = false
      
      next()
    },
  },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    beforeEnter: async (to, from, next) => {
      const accessToken = useAuthStore().getAccessToken()

      if (!accessToken) {
        next({ name: 'login' })
        flagAuth.value = false
        useAuthStore().logout()

        return
      } else if (flagAuth.value) {
        flagAuth.value = false
        next()

        return
      }

      // si existe verificamos si el token es válido
      const { authToken } = useAuthToken()
      const isValid = await authToken()
      if (!isValid) {
        useAuthStore().logout()
        flagAuth.value = false
        next({ name: 'login' })
        
        return
      }
      flagAuth.value = true
      next()
    },
    children: [
      {
        path: 'Panel',
        name: 'panel',
        component: () => import('@/pages/Dashboard/Panel/'),
      },
      
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    beforeEnter: async (to, from, next) => {
      const accessToken = useAuthStore().getAccessToken()
  
      if (!accessToken) {
        flagAuth.value = false
        useAuthStore().logout()
        next()
      }
  
      const { authToken } = useAuthToken()
      const isValid = await authToken()
      if (!isValid) {
        useAuthStore().logout()
        flagAuth.value = false
        next()
          
        return
      }
      
      flagAuth.value = true
      next({ name: 'panel' })
    },
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/Auth/login.vue'),
      },
      {
        path: 'forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/Auth/forgotPass.vue'),
      },
      {
        path: 'reset-password', // example: 
        name: 'reset-password',
        component: () => import('@/pages/Auth/resetPass.vue'),
        props: route => ({ token: route.query.token, email: route.query.email }),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
