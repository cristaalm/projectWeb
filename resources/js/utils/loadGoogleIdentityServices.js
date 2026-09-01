let scriptPromise = null

/**
 * Carga el SDK de Google Identity Services (accounts.google.com/gsi/client)
 * una sola vez, cacheando la promesa. Sin precedente de carga dinámica de
 * scripts en el proyecto — se crea acá para no cargar el SDK en todo el
 * sitio (application.blade.php), solo donde se use el botón de Google.
 */
export function loadGoogleIdentityServices() {
  if (window.google?.accounts?.id) return Promise.resolve()

  if (!scriptPromise) {
    scriptPromise = new Promise((resolve, reject) => {
      const script = document.createElement('script')

      script.src = 'https://accounts.google.com/gsi/client'
      script.async = true
      script.defer = true
      script.onload = () => resolve()
      script.onerror = () => reject(new Error('No se pudo cargar Google Identity Services'))
      document.head.appendChild(script)
    })
  }

  return scriptPromise
}
