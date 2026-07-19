import useAuthToken from '@/hooks/Auth/useAuthToken'
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_BASE_URL),
  routes,
})

router.beforeEach(async to => {
  if (to.name === 'logout') return true

  const needsCheck = to.matched.some(record => record.meta.requiresAuth || record.meta.guestOnly)
  if (!needsCheck) return true

  const { checkSession, twoFactor } = useAuthToken()
  const authenticated = await checkSession()

  if (to.meta.requiresAuth && !authenticated) return { name: 'login' }

  if (authenticated && twoFactor.value && to.name !== 'verify2FA') return { name: 'verify2FA' }

  if (to.meta.guestOnly && authenticated && !twoFactor.value) return { name: 'panel' }

  return true
})

export default function (app) {
  app.use(router)
}

export { router }
