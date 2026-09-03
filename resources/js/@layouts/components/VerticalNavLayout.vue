<script>
import { useDisplay } from 'vuetify'
import VerticalNav from '@layouts/components/VerticalNav.vue'

export default defineComponent({
  setup(props, { slots }) {
    const isOverlayNavActive = ref(false)
    const isLayoutOverlayVisible = ref(false)
    const toggleIsOverlayNavActive = useToggle(isOverlayNavActive)
    const route = useRoute()
    const { mdAndDown } = useDisplay()

    // La barra superior (bienvenida + avatar) solo tiene contenido en el
    // panel principal — ver DefaultLayoutWithVerticalNav.vue, que mueve esas
    // acciones al pie del menú lateral (UserProfile variant="sidebar") para
    // el resto de las vistas. Fuera del panel se colapsa a 0 siempre
    // (desktop y mobile) para recuperar el espacio; en mobile el botón
    // hamburguesa que vivía aquí se reubica como botón flotante independiente
    // (ver DefaultLayoutWithVerticalNav.vue), ya que la barra colapsada deja
    // de ser un contenedor visible donde tenga sentido mostrarlo.
    const isPanelRoute = computed(() => route.name === 'panel')

    // ℹ️ This is alternative to below two commented watcher
    // We want to show overlay if overlay nav is visible and want to hide overlay if overlay is hidden and vice versa.
    syncRef(isOverlayNavActive, isLayoutOverlayVisible)

    return () => {
      // 👉 Vertical nav
      const verticalNav = h(VerticalNav, { isOverlayNavActive: isOverlayNavActive.value, toggleIsOverlayNavActive }, {
        'nav-header': () => slots['vertical-nav-header']?.({ toggleIsOverlayNavActive }),
        'before-nav-items': () => slots['before-vertical-nav-items']?.(),
        'default': () => slots['vertical-nav-content']?.(),
        'after-nav-items': () => slots['after-vertical-nav-items']?.(),
      })

      const collapseNavbar = !isPanelRoute.value

      // 👉 Navbar
      const navbar = h('header', {
        class: [
          'layout-navbar',

          // navbar-blur aplica backdrop-filter (%blurry-bg, ver
          // _default-layout-w-vertical-nav.scss), que por spec crea un
          // containing block para descendientes position:fixed — el botón
          // flotante de menú (ver DefaultLayoutWithVerticalNav.vue) quedaba
          // posicionado relativo a este header colapsado (fuera de pantalla)
          // en vez de al viewport. Colapsado no hay nada que difuminar, así
          // que se omite la clase en ese caso.
          collapseNavbar ? '' : 'navbar-blur',

          // pointer-events-none se hereda a los pseudo-elementos del header
          // (p. ej. algún ::after de blur), evitando que un header colapsado
          // a 0 pero con overlays propios intercepte clicks igual.
          collapseNavbar ? 'pointer-events-none' : '',
        ],
      }, [
        h('div', {
          class: [
            'navbar-content-container',
            collapseNavbar ? 'navbar-collapsed' : '',
          ],
        }, slots.navbar?.({
          toggleVerticalOverlayNavActive: toggleIsOverlayNavActive,
        })),
      ])

      const main = h('main', { class: 'layout-page-content' }, h('div', { class: 'page-content-container' }, slots.default?.()))


      // 👉 Footer
      const footer = h('footer', { class: 'layout-footer' }, [
        h('div', { class: 'footer-content-container' }, slots.footer?.()),
      ])


      // 👉 Overlay
      const layoutOverlay = h('div', {
        class: ['layout-overlay', { visible: isLayoutOverlayVisible.value }],
        onClick: () => { isLayoutOverlayVisible.value = !isLayoutOverlayVisible.value },
      })

      return h('div', {
        class: [
          'layout-wrapper layout-nav-type-vertical layout-navbar-sticky layout-footer-static layout-content-width-fluid',
          mdAndDown.value && 'layout-overlay-nav',
          collapseNavbar ? 'layout-navbar-collapsed' : '',
          route.meta.layoutWrapperClasses,
        ],
      }, [
        verticalNav,
        h('div', { class: 'layout-content-wrapper' }, [
          navbar,
          main,
          footer,
        ]),
        layoutOverlay,
      ])
    }
  },
})
</script>

<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/placeholders";
@use "@layouts/styles/mixins";

.layout-wrapper.layout-nav-type-vertical {
  // TODO(v2): Check why we need height in vertical nav & min-height in horizontal nav
  block-size: 100%;

  .layout-content-wrapper {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    min-block-size: 100dvh;
    transition: padding-inline-start 0.2s ease-in-out;
    will-change: padding-inline-start;

    @media screen and (min-width: 1280px) {
      padding-inline-start: variables.$layout-vertical-nav-width;
    }
  }

  .layout-navbar {
    z-index: variables.$layout-vertical-nav-layout-navbar-z-index;

    .navbar-content-container {
      block-size: variables.$layout-vertical-nav-navbar-height;

      // Mayor especificidad que la regla de arriba a propósito: sin esto,
      // .navbar-collapsed (clase plana, agregada en VerticalNavLayout.vue)
      // pierde el conflicto de especificidad y la barra no colapsa.
      &.navbar-collapsed {
        block-size: 0;
        overflow: hidden;
      }
    }

    @at-root {
      .layout-wrapper.layout-nav-type-vertical {
        .layout-navbar {
          @if variables.$layout-vertical-nav-navbar-is-contained {
            @include mixins.boxed-content;
          }

          // else
          @else {
            .navbar-content-container {
              @include mixins.boxed-content;
            }
          }
        }
      }
    }
  }

  &.layout-navbar-sticky .layout-navbar {
    @extend %layout-navbar-sticky;
  }

  &.layout-navbar-hidden .layout-navbar {
    @extend %layout-navbar-hidden;
  }

  // .layout-page-content trae un padding-block-start fijo de 1.5rem (ver
  // _default-layout.scss), pensado como separación visual respecto al header
  // con altura normal. Con el header colapsado a 0 (ver VerticalNavLayout.vue
  // arriba) ese padding queda como el único espacio visible sobre el
  // contenido — se reduce aquí para que no se sienta como un hueco suelto.
  &.layout-navbar-collapsed .layout-page-content {
    padding-block-start: 0.75rem;
  }

  // 👉 Footer
  .layout-footer {
    @include mixins.boxed-content;
  }

  // 👉 Layout overlay
  .layout-overlay {
    position: fixed;
    z-index: variables.$layout-overlay-z-index;
    background-color: rgb(0 0 0 / 60%);
    cursor: pointer;
    inset: 0;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease-in-out;
    will-change: opacity;

    &.visible {
      opacity: 1;
      pointer-events: auto;
    }
  }

  // Adjust right column pl when vertical nav is collapsed
  &.layout-vertical-nav-collapsed .layout-content-wrapper {
    @media screen and (min-width: 1280px) {
      padding-inline-start: variables.$layout-vertical-nav-collapsed-width;
    }
  }

  // 👉 Content height fixed
  &.layout-content-height-fixed {
    .layout-content-wrapper {
      max-block-size: 100dvh;
    }

    .layout-page-content {
      display: flex;
      overflow: hidden;

      .page-content-container {
        inline-size: 100%;

        > :first-child {
          max-block-size: 100%;
          overflow-y: auto;
        }
      }
    }
  }
}
</style>
