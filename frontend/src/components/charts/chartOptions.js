export const baseOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: { duration: 500 },
  plugins: {
    legend: {
      labels: {
        boxWidth: 12,
        boxHeight: 12,
        padding: 14,
        font: { size: 12 }
      }
    },
    tooltip: {
      padding: 10,
      bodySpacing: 6,
      titleSpacing: 6,
      cornerRadius: 8
    }
  },
  layout: { padding: 10 }
}