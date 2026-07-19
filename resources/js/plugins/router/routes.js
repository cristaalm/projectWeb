export const routes = [
  { path: '/', component: () => import('@/pages/Landing') },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    meta: { requiresAuth: true },
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
    meta: { guestOnly: true },
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/Auth/login.vue'),
      },
      {
        path: 'logout',
        name: 'logout',
        component: () => import('@/pages/Auth/logout.vue'),
      },
      {
        path: 'forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/Auth/forgotPass.vue'),
      },
      {
        path: 'reset-password',
        name: 'reset-password',
        component: () => import('@/pages/Auth/resetPass.vue'),
        props: route => ({ token: route.query.token, email: route.query.email }),
      },
      {
        path: 'verify-2fa',
        name: 'verify2FA',
        component: () => import('@/pages/Auth/verify2FA.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
