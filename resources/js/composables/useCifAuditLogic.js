import * as XLSX from 'xlsx'

export function useCifAuditLogic() {
  /**
   * Status Kelengkapan CIF
   * 1. Jika Kawin: Cek NIK Pasangan (16 digit, bukan angka kembar), Nama Pasangan, Hubungan, Tgl Lahir
   * 2. Jika Tidak Kawin (tapi ada status): Cek Ulang atau Belum Lengkap
   * 3. Jika Status kosong: Belum Lengkap
   * @param {Object} row - Data baris CIF
   * @returns {String} 'Lengkap', 'Belum Lengkap', 'Cek Ulang'
   */
  const getCifStatus = (row) => {
    const sttsK = (row.ket_stskawin || '').toUpperCase().trim()
    const isKawin = sttsK === 'KAWIN'
    const isEmpty = !sttsK || sttsK === '-' || sttsK === 'NULL'
    
    if (isKawin) {
      const nikP = (row.nik_pasangan || '').replace(/\D/g, '')
      // Cek validasi NIK (16 digit & tidak boleh karakter berulang semua cth: 1111111111111111)
      const isNikValid = nikP.length === 16 && !(/^(\d)\1{15}$/.test(nikP))
      
      const isDataValid = !!(row.nama_pasangan && row.ket_kdhub && row.tgllhr_pasangan && row.nama_pasangan !== '-' && row.ket_kdhub !== '-' && row.tgllhr_pasangan !== '-')
      
      return (isNikValid && isDataValid) ? 'Lengkap' : 'Belum Lengkap'
    }
    
    if (isEmpty) return 'Belum Lengkap'
    
    return 'Cek Ulang'
  }

  const isNikAnomaly = (nik) => {
    if (!nik) return true
    const cleanNik = String(nik).replace(/\D/g, '')
    return cleanNik.length !== 16
  }

  const isNamaAnomaly = (nama) => {
    if (!nama) return true
    return String(nama).includes("'")
  }

  const isHpAnomaly = (hp) => {
    if (!hp || hp === '-' || hp === 'NULL') return true
    const cleanHp = String(hp).replace(/\D/g, '')
    return cleanHp.length < 9
  }

  const isIbuAnomaly = (ibu, namaNasabah, kota) => {
    if (!ibu || ibu === '-' || ibu === 'NULL') return true
    const ibuUpper = String(ibu).toUpperCase().trim()
    const nasabahUpper = String(namaNasabah || '').toUpperCase().trim()
    const kotaUpper = String(kota || '').toUpperCase().trim()

    if (ibuUpper === nasabahUpper) return true
    if (ibuUpper.includes('BINTI')) return true
    if (kotaUpper && ibuUpper === kotaUpper) return true
    
    return false
  }

  const exportToExcel = (data, type = 'Pembiayaan') => {
    if (!data || !data.length) return

    const formattedData = data.map((row, index) => {
      const noktpClean = String(row.noktp || '').replace(/\D/g, '')
      const isNikValid = noktpClean.length === 16 ? 'Valid (16)' : `Invalid (${noktpClean.length})`
      
      const mappedRow = {
        'NO': index + 1,
        'NO CIF': { t: 's', v: String(row.nocif || '') },
        'NAMA NASABAH': row.namanasabah || ''
      }

      // Automatically add NPWP if available or if type specifies Badan Hukum
      if ('npwp' in row || String(type).toLowerCase().includes('badan hukum')) {
        mappedRow['NPWP'] = { t: 's', v: String(row.npwp || '') }
      }

      Object.assign(mappedRow, {
        'NOMOR KTP': { t: 's', v: String(row.noktp || '') },
        'CEK NIK': isNikValid,
        'J/K': row.jk || '',
        'TEMPAT LAHIR': row.tempat_lahir || '',
        'TGL LAHIR KTP': row.tgllhr_ktp || '',
        'TGL LAHIR CIF': row.tgllhr || '',
        'USIA': row.usia || '',
        'NOMOR HP': { t: 's', v: String(row.nohp || '') },
        'SANDI DATI': { t: 's', v: String(row.sandi_dati || '') },
        'NAMA IBU KANDUNG': row.nama_ibu || '',
        'STATUS KAWIN': row.ket_stskawin || '',
        'HUBUNGAN PASANGAN': row.ket_kdhub || '',
        'NAMA PASANGAN': row.nama_pasangan || '',
        'NIK PASANGAN': { t: 's', v: String(row.nik_pasangan || '') },
        'HP PASANGAN': { t: 's', v: String(row.hp_pasangan || '') },
        'TGL LAHIR PASANGAN': row.tgllhr_pasangan || '',
        'USIA PASANGAN': row.usia_pasangan || '',
        'STATUS CIF': getCifStatus(row),
        'ALAMAT LENGKAP': row.alamat || '',
        'KELURAHAN': row.kelurahan || '',
        'KECAMATAN': row.kecamatan || '',
        'KOTA': row.kota || '',
        'KODE POS': { t: 's', v: String(row.kodepos || '') },
        'NAMA MARKETING': row.nama_marketing || '',
        'CABANG': row.cabang || ''
      })

      return mappedRow
    })

    const worksheet = XLSX.utils.json_to_sheet(formattedData)
    const workbook = XLSX.utils.book_new()
    
    // Set approx column widths
    const wscols = Object.keys(formattedData[0] || {}).map(k => ({ wch: Math.max(k.length, 12) }))
    worksheet['!cols'] = wscols

    const safeType = type.replace(/\s/g, '_')
    XLSX.utils.book_append_sheet(workbook, worksheet, `Data_CIF_${safeType}`.substring(0, 31))
    XLSX.writeFile(workbook, `Export_CIF_${safeType}_${new Date().toISOString().split('T')[0]}.xlsx`)
  }

  return {
    getCifStatus,
    isNikAnomaly,
    isNamaAnomaly,
    isHpAnomaly,
    isIbuAnomaly,
    exportToExcel
  }
}
