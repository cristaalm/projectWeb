import { reactive } from 'vue'

// Trackea qué campos el usuario ya "tocó" (perdió el foco), para no mostrar
// errores de validación antes de que interactúe con el campo. `touch` acepta
// varios nombres a la vez para el caso de campos relacionados (ej. al salir
// de "confirmar contraseña" también se revela el error de "contraseña nueva"
// si está vacía, aunque ese campo nunca se haya tocado directamente).
export function useFieldTouch(fields) {
  const touched = reactive(Object.fromEntries(fields.map(field => [field, false])))

  function touch(...names) {
    names.forEach(name => { touched[name] = true })
  }

  function reset() {
    Object.keys(touched).forEach(key => { touched[key] = false })
  }

  return { touched, touch, reset }
}
