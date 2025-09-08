import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    expiresAt: localStorage.getItem('expires_at') || null,
    accessToken: localStorage.getItem('access_token') || null,
  }),
  actions: {
    setAccessToken(accessToken) {
      this.accessToken = accessToken
      localStorage.setItem('access_token', accessToken)
    },
    getAccessToken() {
      return this.accessToken
    },
    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },
    getUser() {
      return this.user
    },
    setExpiresAt(expiresAt) {
      this.expiresAt = expiresAt
      localStorage.setItem('expires_at', expiresAt)
    },
    getExpiresAt() {
      return this.expiresAt
    },
    logout() {
      this.user = null
      this.expiresAt = null
      this.accessToken = null
      localStorage.removeItem('user')
      localStorage.removeItem('expires_at')
      localStorage.removeItem('access_token')
    },
  },
})
