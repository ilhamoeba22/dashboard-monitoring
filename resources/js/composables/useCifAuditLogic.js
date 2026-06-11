export const cifAuditHeaders = [
  { title: 'NO', key: 'id', align: 'center', minWidth: 60, width: 60, fixed: true },
  { title: 'NO CIF', key: 'nocif', align: 'center', minWidth: 120, width: 120, fixed: true },
  { title: 'NAMA NASABAH', key: 'namanasabah', align: 'left', minWidth: 250, width: 250, fixed: true },
  { title: 'NPWP', key: 'npwp', align: 'center', width: '180px' },
  { title: 'NOMOR KTP', key: 'noktp', align: 'center', width: '160px' },
  { title: 'CEK NIK', key: 'ceknik', align: 'center', width: '100px' },
  { title: 'J/K', key: 'jk', align: 'center', width: '60px' },
  { title: 'TEMPAT LAHIR', key: 'tempat_lahir', align: 'left', width: '150px' },
  { title: 'TGL LAHIR KTP', key: 'tgllhr_ktp', align: 'center', width: '130px' },
  { title: 'TGL LAHIR CIF', key: 'tgllhr', align: 'center', width: '130px' },
  { title: 'USIA', key: 'usia', align: 'center', width: '80px' },
  { title: 'NOMOR HP', key: 'nohp', align: 'center', width: '140px' },
  { title: 'SANDI DATI', key: 'sandi_dati', align: 'center', width: '100px' },
  { title: 'NAMA IBU KANDUNG', key: 'nama_ibu', align: 'left', width: '200px' },
  { title: 'STATUS KAWIN', key: 'ket_stskawin', align: 'center', width: '130px' },
  { title: 'HUBUNGAN PASANGAN', key: 'ket_kdhub', align: 'center', width: '160px' },
  { title: 'NAMA PASANGAN', key: 'nama_pasangan', align: 'left', width: '200px' },
  { title: 'NIK PASANGAN', key: 'nik_pasangan', align: 'center', width: '160px' },
  { title: 'HP PASANGAN', key: 'hp_pasangan', align: 'center', width: '140px' },
  { title: 'TGL LAHIR PASANGAN', key: 'tgllhr_pasangan', align: 'center', width: '160px' },
  { title: 'USIA PASANGAN', key: 'usia_pasangan', align: 'center', width: '130px' },
  { title: 'STATUS CIF', key: 'status_cif', align: 'center', width: '140px' },
  { title: 'ALAMAT LENGKAP', key: 'alamat', align: 'left', width: '300px' },
  { title: 'KELURAHAN', key: 'kelurahan', align: 'left', width: '150px' },
  { title: 'KECAMATAN', key: 'kecamatan', align: 'left', width: '150px' },
  { title: 'KOTA', key: 'kota', align: 'center', width: '150px' },
  { title: 'KODE POS', key: 'kodepos', align: 'center', width: '100px' },
  { title: 'NAMA MARKETING', key: 'nama_marketing', align: 'left', width: '180px' },
  { title: 'CABANG', key: 'cabang', align: 'left', width: '180px' },
]

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

  const exportToExcel = async (data, type = 'Pembiayaan') => {
    if (!data || !data.length) return
    const XLSX = await import('xlsx')

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

  const exportToPdf = async (data, headers, title = 'Audit CIF', type = 'Individu') => {
    if (!data || !data.length) return
    const { default: jsPDF } = await import('jspdf')
    const { default: autoTable } = await import('jspdf-autotable')

    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' })
    doc.setFillColor(15, 23, 42)
    doc.rect(0, 0, 297, 22, 'F')
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(14)
    doc.text(title, 14, 13)
    doc.setFontSize(9)
    doc.text(`Tipe: ${type} | Jumlah Data: ${data.length} | Tanggal Export: ${new Date().toLocaleDateString('id-ID')}`, 14, 19)
    doc.setTextColor(15, 23, 42)

    autoTable(doc, {
      startY: 30,
      head: [headers.map(header => header.title)],
      body: data.map(row => headers.map(header => {
        if (header.key === 'status_cif') return getCifStatus(row)
        if (header.key === 'ceknik') {
          const length = String(row.noktp || '').replace(/\D/g, '').length
          return length === 16 ? 'Valid (16)' : `Invalid (${length})`
        }
        return row[header.key] ?? '-'
      })),
      styles: { fontSize: 6.5, cellPadding: 1.2, overflow: 'linebreak' },
      headStyles: { fillColor: [15, 23, 42], textColor: 255, halign: 'center', fontSize: 6.5 },
      alternateRowStyles: { fillColor: [248, 250, 252] },
      margin: { left: 8, right: 8 },
    })

    const safeType = `${title}_${type}`.replace(/[^a-zA-Z0-9]+/g, '_')
    doc.save(`Export_${safeType}_${new Date().toISOString().split('T')[0]}.pdf`)
  }

  return {
    getCifStatus,
    isNikAnomaly,
    isNamaAnomaly,
    isHpAnomaly,
    isIbuAnomaly,
    exportToExcel,
    exportToPdf
  }
}
