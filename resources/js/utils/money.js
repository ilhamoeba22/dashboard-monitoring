const exactRupiahFormatter = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0,
  maximumFractionDigits: 0,
})

const exactNumberFormatter = new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 0,
})

function truncateNumber(value, decimals = 0) {
    if (value === null || value === undefined || value === '') return 0
    const num = Number(value)
    if (!Number.isFinite(num)) return 0

    const factor = 10 ** decimals
    return Math.trunc(num * factor) / factor
}

/**
 * Truncate scale: Used for charts where labels MUST be short.
 * Unlike toFixed(), this just scales the number for visual guidance without claiming to be the exact final rupiah.
 * We use 2 decimals as a guidance scale.
 */
export function formatScale(value, divisor, unit = '') {
    if (value === null || value === undefined) return '0'
    const num = Number(value)
    if (!Number.isFinite(num)) return '0'
    return truncateNumber(num / divisor, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + unit
}

/**
 * Banking Truncate (6 Digits Rule):
 * Menampilkan 6 digit awal dari nominal (dalam jutaan) tanpa pembulatan.
 * e.g. 228.996.525.219 -> 228.996
 */
export function formatBanking6(value) {
    if (value === null || value === undefined || value === 0) return '0'
    const num = Number(value)
    if (!Number.isFinite(num)) return '0'
    
    // Sesuai instruksi: Tampilkan 6 digit awal (dalam skala jutaan) tanpa pembulatan
    const millions = Math.floor(num / 1000000)
    return new Intl.NumberFormat('id-ID').format(millions)
}

/**
 * Compact Rupiah (Singkatan M/Jt)
 */
export function formatCompactRupiah(value, fallback = 'Rp 0') {
    if (value === null || value === undefined || value === '') return fallback
    const num = Number(value)
    if (!Number.isFinite(num)) return fallback
  
    const abs = Math.abs(num)
    const sign = num < 0 ? '-' : ''
  
    if (abs >= 1e12) return `${sign}Rp ${truncateNumber(abs / 1e12, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} T`
    if (abs >= 1e9)  return `${sign}Rp ${truncateNumber(abs / 1e9, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} M`
    if (abs >= 1e6)  return `${sign}Rp ${truncateNumber(abs / 1e6, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Jt`
  
    return formatExactRupiah(num, fallback)
}

/**
 * Compact Number (Singkatan M/Jt)
 */
export function formatCompactNumber(value, fallback = '0') {
    if (value === null || value === undefined || value === '') return fallback
    const num = Number(value)
    if (!Number.isFinite(num)) return fallback
  
    const abs = Math.abs(num)
    const sign = num < 0 ? '-' : ''
  
    if (abs >= 1e12) return `${sign}${truncateNumber(abs / 1e12, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} T`
    if (abs >= 1e9)  return `${sign}${truncateNumber(abs / 1e9, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} M`
    if (abs >= 1e6)  return `${sign}${truncateNumber(abs / 1e6, 2).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Jt`
  
    return formatExactNumber(num, fallback)
}

/**
 * Strict Truncation: Tampilkan angka dengan 2 desimal tanpa pembulatan (Banking Rule).
 * e.g. 6.9608... -> 6.96
 * e.g. 6.9699... -> 6.96
 */
export function formatTruncatedPercentage(value) {
    if (value === null || value === undefined) return '0%'
    const num = Number(value)
    if (!Number.isFinite(num)) return '0%'
    
    // Truncate to 2 decimals: geser koma 2 digit, potong, geser balik.
    const truncated = truncateNumber(num, 2)
    return truncated.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%'
}

export function toFiniteNumber(value) {
  if (value === null || value === undefined || value === '') return 0
  const number = Number(value)
  return Number.isFinite(number) ? number : 0
}

/**
 * Format Exact Rupiah: NO ROUNDING.
 * Displays all decimals available.
 */
export function formatExactRupiah(value, fallback = 'Rp 0') {
  if (value === null || value === undefined || value === '') return fallback
  const number = Number(value)
  if (!Number.isFinite(number)) return fallback

  return exactRupiahFormatter.format(truncateNumber(number, 0))
}

/**
 * Format Exact Number: NO ROUNDING.
 */
export function formatExactNumber(value, fallback = '0') {
  if (value === null || value === undefined || value === '') return fallback
  const number = Number(value)
  if (!Number.isFinite(number)) return fallback

  return exactNumberFormatter.format(truncateNumber(number, 0))
}
