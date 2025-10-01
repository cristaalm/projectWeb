<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import Lucide from '@/components/Base/Lucide/'
import useLogin from '@/hooks/Auth/useLogin'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  email: '',
  pass: '',
  remember: false,
})

const {
  user,
  success,
  error,
  loading,
  loginUser,
} = useLogin()

const isPasswordVisible = ref(false)

const goToHome = () => {
  router.push({ name: 'panel' })
}
</script>

<template>
  <!-- <div class="justify-center bg-gradient-to-b from-purple-500 to-blue-300 auth-wrapper d-flex align-center pa-4"> -->
  <div class="justify-center lg:bg-gradient-to-b bg-gradient-to-r  from-[#CFFFE0] to-[#8BE6AE] auth-wrapper d-flex align-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VImg
        :src="authV1TopShape"
        class="text-primary auth-v1-top-shape !absolute d-none d-sm-block"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        class="text-primary auth-v1-bottom-shape !absolute d-none d-sm-block"
      />

      <!-- Auth Card -->
      <!-- cuando success sea true, agregamos la clase animate-scaleUp -->
      <VCard
        class="auth-card !bg-white/30 !shadow-2xl"
        :class="[success ? 'animate-scaleDown mt-[120px]' : 'animate-scaleUp', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
        max-width="480"
      >
        <VCardItem class="justify-center">
          <span
            class="app-logo"
            @click="goToHome"
          >
            <!-- eslint-disable vue/no-v-html -->
            <div class="d-flex">
              <img
                src="/images/LogoLetra.png"
                alt="RENOVA"
                class="!w-[200px] mr-2"  
              >
            </div>
          </span>
        </VCardItem>

        <VCardText>
          <h4 class="font-poppins relative mb-1 text-h4">
            ¡RENOVA le da la bienvenida! <span class="absolute md:inline-block hidden -top-2 ml-2 text-4xl transition-all duration-200 transform animate-wave">👋🏻</span>
          </h4>
          <p class="mb-0 font-poppins">
            Inicie sesión en su cuenta y comience con su día.
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="$router.push('/')">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Correo Electrónico"
                  type="email"
                  placeholder="johndoe@email.com"
                  class="font-poppins"
                  :error="error"
                  @input="error = false"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.pass"
                  label="Contraseña"
                  placeholder="**********"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :error="error"
                  autocomplete="password"
                  :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                  class="font-poppins"
                  @input="error = false"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <!-- remember me checkbox -->
                <div class="flex-wrap my-6 d-flex align-center justify-space-between">
                  <VCheckbox
                    v-model="form.remember"
                    class="font-poppins"
                    label="Mantener sesión"
                  />

                  <a
                    class="text-primary font-poppins"
                    href="./forgot-password"
                    @click.prevent="router.push('/forgot-password')"
                  >
                    Olvidé mi contraseña
                  </a>
                </div>

                <!-- login button -->
                <VBtn
                  block
                  type="submit"
                  class="hover:!bg-[#08b662] font-poppins"
                  :disabled="loading || !form.email || !form.pass || success || error"
                  @click="loginUser(form)"
                >
                  <span v-if="loading">
                    <LoadingIcon icon="three-dots" />
                  </span>
                  <span v-else>
                    Iniciar Sesión
                  </span>
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
      <VCard
        class="auth-card !bg-white/30 !shadow-2xl"
        :class="[success ? 'animate-scaleUp' : '!hidden', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
        max-width="460"
      >
        <div class="text-center pa-6">
          <VAvatar
            size="80"
            color="primary"
            class="p-3 mb-4"
          >
            <Lucide
              icon="CircleCheckBig"
              class="w-full h-full"
            />
          </VAvatar>
    
          <h2 class="mb-2 !text-3xl !font-bold text-h5">
            ¡Bienvenido, {{ user?.name || "" }}!
          </h2>
    
          <p class="mb-6 text-body-1 !text-xl">
            Has iniciado sesión con éxito.
            <br>
            ¡Disfruta de tu día!
          </p>
        </div>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
