import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth } from './middleware/auth'
import { routes } from './routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_BASE_URL),
  routes,
})

router.beforeEach(async (to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    await requireAuth(to, from, next)
  } else {
    next()
  }
})

export default function (app) {
  app.use(router)
}

export { router }
