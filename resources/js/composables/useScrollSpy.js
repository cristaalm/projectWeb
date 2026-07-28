import { onBeforeUnmount, onMounted, ref } from 'vue'

// Marca como "activa" la sección más arriba entre las que están cruzando el
// primer 30% del viewport — el mismo truco de rootMargin negativo que usan
// la mayoría de los scrollspy (evita que dos secciones peleen por el estado
// activo cuando ambas son parcialmente visibles a la vez).
export function useScrollSpy(ids, { rootMargin = '0px 0px -70% 0px' } = {}) {
  const activeId = ref(ids[0] ?? null)
  let observer = null

  // Si la última sección es más corta que el 70% del viewport, nunca llega a
  // cruzar el rootMargin de arriba — sin este resguardo se queda sin marcar
  // como activa aunque ya hiciste scroll hasta el final de la página.
  function activateLastIfAtBottom() {
    const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2

    if (atBottom) activeId.value = ids[ids.length - 1]
  }

  onMounted(() => {
    const elements = ids.map(id => document.getElementById(id)).filter(Boolean)

    observer = new IntersectionObserver(entries => {
      const visible = entries.filter(entry => entry.isIntersecting)

      if (visible.length > 0) {
        const topMost = visible.reduce((a, b) => (a.boundingClientRect.top < b.boundingClientRect.top ? a : b))

        activeId.value = topMost.target.id
      }

      activateLastIfAtBottom()
    }, { rootMargin, threshold: 0 })

    elements.forEach(el => observer.observe(el))
    window.addEventListener('scroll', activateLastIfAtBottom, { passive: true })
  })

  onBeforeUnmount(() => {
    observer?.disconnect()
    window.removeEventListener('scroll', activateLastIfAtBottom)
  })

  function scrollTo(id) {
    activeId.value = id
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }

  return { activeId, scrollTo }
}
