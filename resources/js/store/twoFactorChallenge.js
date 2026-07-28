import { defineStore } from 'pinia'

// A propósito NO persistido en localStorage (a diferencia de store/auth.js):
// el challenge es de vida corta (5 min) y sensible, no debe sobrevivir un
// refresh de página — si se pierde, el usuario simplemente vuelve a loguearse.
export const useTwoFactorChallengeStore = defineStore('twoFactorChallenge', {
  state: () => ({
    challengeToken: null,
    expiresAt: null,
  }),
  actions: {
    setChallenge(challengeToken, expiresAt) {
      this.challengeToken = challengeToken
      this.expiresAt = expiresAt
    },
    clear() {
      this.challengeToken = null
      this.expiresAt = null
    },
  },
})
