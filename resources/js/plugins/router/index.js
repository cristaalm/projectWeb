import useAuthToken from '@/hooks/Auth/useAuthToken'
import { useTwoFactorChallengeStore } from '@/store/twoFactorChallenge'
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_BASE_URL),
  routes,
})

router.beforeEach(async to => {
  if (to.name === 'logout') return true

  // verify-2fa no depende de sesión/token (no existe ninguno todavía) sino de
  // tener un challenge de login pendiente — sin eso no hay nada que verificar.
  if (to.meta.twoFactorChallenge) {
    return useTwoFactorChallengeStore().challengeToken ? true : { name: 'login' }
  }

  const needsCheck = to.matched.some(record => record.meta.requiresAuth || record.meta.guestOnly)
  if (!needsCheck) return true

  const { checkSession } = useAuthToken()
  const authenticated = await checkSession()

  if (to.meta.requiresAuth && !authenticated) return { name: 'login' }

  if (to.meta.guestOnly && authenticated) return { name: 'panel' }

  return true
})

export default function (app) {
  app.use(router)
}

export { router }
