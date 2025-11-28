// @/hooks/Shops/useAllianceMetrics.js
import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'

export const useAllianceMetrics = () => {
  const getMetrics = async allianceId => {
    try {
      const token = useAuthStore().getAccessToken()
      if (!token) {
        throw new Error('No se encontró token de autenticación.')
      }
      
      const [cashCutRes, statsRes, activityRes, topRewardsRes] = await Promise.all([
        requestGet({ url: `alianzas/cashCut/${allianceId}`, params: { only_return: true }, token }),
        requestGet({ url: `alianzas/stats/${allianceId}`, token }),
        requestGet({ url: `alianzas/activityByDayOfWeek/${allianceId}`, token }),
        requestGet({ url: `alianzas/top-rewards/${allianceId}`, token }),
      ])

      // --- DEBUG: Ver la respuesta real de la API ---
      console.log('useAllianceMetrics: Respuestas de la API:', {
        cashCutRes: JSON.parse(JSON.stringify(cashCutRes)),
        statsRes: JSON.parse(JSON.stringify(statsRes)),
        activityRes: JSON.parse(JSON.stringify(activityRes)),
        topRewardsRes: JSON.parse(JSON.stringify(topRewardsRes)),
      })

      if (!cashCutRes.success || !statsRes.success || !activityRes.success || !topRewardsRes.success) {
        const errors = []
        if (!cashCutRes.success) errors.push('cashCutRes')
        if (!statsRes.success) errors.push('statsRes')
        if (!activityRes.success) errors.push('activityRes')
        if (!topRewardsRes.success) errors.push('topRewardsRes')

        console.error('Una o más peticiones de métricas fallaron. Revisa el backend.', {
          failedRequests: errors,
        })
        throw new Error(`Fallo al cargar métricas: ${errors.join(', ')}`) 
      }
      
      // --- ¡CAMBIO IMPORTANTE! Accedemos a response.data (no response.data.data) ---
      const cashCutData = cashCutRes.data || {}
      const statsData = statsRes.data || {}
      const activityData = activityRes.data || {}
      const topRewardsData = topRewardsRes.data || [] // Este endpoint devuelve un array
      // --- FIN DEL CAMBIO ---

      // Formatear actividad semanal
      // Asumiendo que `activityData` (response.data) es el objeto que tiene `statsToWeek`
      const actividad = (activityData.statsToWeek || []).map(day => ({
        dia: day.day,
        fecha: day.date,
        total: day.total_activity,
        puntos: 0,
      }))

      // Formatear top recompensas
      // Asumiendo que `topRewardsData` (response.data) es el array
      const recompensasTop = (topRewardsData || []).map(reward => ({
        name: reward.reward_name,
        redemptions: reward.total_claimed,
      }))

      const fechaCorte = cashCutData.cash_out_date
        ? new Date(cashCutData.cash_out_date)
        : new Date()

      return {
        corte: {
          total: cashCutData.cash_out 
            ? `$${cashCutData.cash_out.toFixed(2)}` 
            : '$0.00',
          puntos: cashCutData.total_points || 0,
          fecha: fechaCorte.toLocaleString('es-MX', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
          }),
        },
        estadisticas: {
          ingresoTotal: statsData.total_income 
            ? `$${statsData.total_income.toFixed(2)}` 
            : '$0.00',
          puntosCanjeados: statsData.total_points_awarded || 0,
          promedioIngreso: statsData.average_total_income 
            ? `$${statsData.average_total_income.toFixed(2)}` 
            : '$0.00',
          clientesAtendidos: statsData.total_customers_served || 0,
          puntosGenerados: cashCutData.total_points || 0,
          transaccionesHoy: 0, 
          transaccionesSemana: activityData.totalSales || 0,
          transaccionesMes: 0, 
        },
        actividad,
        semanaAnterior: {
          ventas: activityData.totalSales || 0,
          puntos: activityData.totalPoints || 0,
        },
        recompensasTop,
      }
    } catch (error) {
      console.error('Error obteniendo métricas (catch):', error.message) 
      if (error.response) {
        console.error('Respuesta del servidor:', error.response.data)
      }
      
      // Devuelve ceros si falla
      return {
        corte: { total: '$0.00', puntos: 0, fecha: 'N/A' },
        estadisticas: {
          ingresoTotal: '$0.00',
          puntosCanjeados: 0,
          promedioIngreso: '$0.00',
          clientesAtendidos: 0,
          puntosGenerados: 0,
          transaccionesHoy: 0,
          transaccionesSemana: 0,
          transaccionesMes: 0,
        },
        actividad: [],
        semanaAnterior: { ventas: 0, puntos: 0 },
        recompensasTop: [],
      }
    }
  }

  return { getMetrics }
}