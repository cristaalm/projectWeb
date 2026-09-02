<script setup>
import AvatarCard from '@/views/Profile/AvatarCard.vue'
import EmailCard from '@/views/Profile/EmailCard.vue'
import PasswordCard from '@/views/Profile/PasswordCard.vue'
import PersonalInfoCard from '@/views/Profile/PersonalInfoCard.vue'
import SettingsNav from '@/views/Profile/SettingsNav.vue'
import SocialAccountsCard from '@/views/Profile/SocialAccountsCard.vue'
import TwoFactorCard from '@/views/Profile/TwoFactorCard.vue'
import { useScrollSpy } from '@/composables/useScrollSpy'

const sections = [
  { id: 'avatar', label: 'Avatar' },
  { id: 'personal-info', label: 'Información personal' },
  { id: 'email', label: 'Correo' },
  { id: 'password', label: 'Contraseña' },
  { id: 'two-factor', label: 'Autenticación de dos factores' },
  { id: 'social-accounts', label: 'Cuentas vinculadas' },
]

const { activeId, scrollTo } = useScrollSpy(sections.map(section => section.id))
</script>

<template>
  <VContainer max-width="1100">
    <VRow>
      <VCol
        cols="12"
        md="3"
      >
        <SettingsNav
          :sections="sections"
          :active-id="activeId"
          @navigate="scrollTo"
        />
      </VCol>
      <VCol
        cols="12"
        md="9"
      >
        <div class="d-flex flex-column gap-6">
          <div id="avatar">
            <AvatarCard />
          </div>
          <div id="personal-info">
            <PersonalInfoCard />
          </div>
          <div id="email">
            <EmailCard />
          </div>
          <div id="password">
            <PasswordCard />
          </div>
          <div id="two-factor">
            <TwoFactorCard />
          </div>
          <div id="social-accounts">
            <SocialAccountsCard />
          </div>
        </div>
      </VCol>
    </VRow>
  </VContainer>
</template>
