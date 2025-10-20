import { differenceInYears, format, isBefore, parseISO } from 'date-fns'
import dayjs from 'dayjs'

export function daysLeft(date) {
  const deletedAt = dayjs(date)
  const deletionDate = deletedAt.add(30, 'day') // Fecha de eliminación permanente
  const now = dayjs()

  const diffMs = deletionDate.valueOf() - now.valueOf()

  // Si ya se pasó la fecha
  if (diffMs <= 0) {
    return 'Eliminado'
  }

  const hours = Math.floor(diffMs / (1000 * 60 * 60))
  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  // Menos de 1 hora
  if (hours < 1) {
    return 'Falta menos de 1 hora'
  }

  // Menos de 24 horas → mostrar horas
  if (hours < 24) {
    return `${hours} hora${hours !== 1 ? 's' : ''}`
  }

  // 30 días o más → meses
  if (days >= 30) {
    const months = Math.floor(days / 30)
    
    return `${months} mes${months > 1 ? 'es' : ''}`
  }

  // 7 días o más → semanas
  if (days >= 7) {
    const weeks = Math.floor(days / 7)
    
    return `${weeks} semana${weeks > 1 ? 's' : ''}`
  }

  // 1 día o más → días
  return `${days} dia${days !== 1 ? 's' : ''}`
}

// Formatea una fecha de formato dd/MM/yyyy a formato ISO
export const formatDateToISO = dateString => {
  if (!dateString) return ''

  const [day, month, year] = dateString.split('/')
  
  return `${year}-${month}-${day}`
}

// Formatea una fecha en formato ISO a dd/MM/yyyy
export const formatDateToDDMMYYYY = dateString => {
  if (!dateString) return ''

  return format(parseISO(dateString), 'dd/MM/yyyy')
}

export function getFullYearsSince(isoDateString) {
  const targetDate = parseISO(isoDateString)
  const now = new Date()

  if (isBefore(now, targetDate)) {
    // Fecha futura → devolvemos negativo
    return -getFullYearsSinceTo(targetDate, now)
  }

  return getFullYearsSinceTo(targetDate, now)
}

function getFullYearsSinceTo(from, to) {
  let years = differenceInYears(to, from)

  // Verificar si el aniversario ya pasó este año
  const anniversaryThisYear = new Date(to.getFullYear(), from.getMonth(), from.getDate())
  if (isBefore(to, anniversaryThisYear)) {
    years -= 1
  }

  return years
}
