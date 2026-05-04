// Tema MCI Dashboard — Hijau Zamrud + Emas
// HIGH CONTRAST — tidak ada opacity pada text warna utama

export const staticPrimaryColor = '#059669'
export const staticPrimaryDarkenColor = '#047857'

export const themes = {
  light: {
    dark: false,
    colors: {
      'primary':            '#059669',
      'on-primary':         '#FFFFFF',
      'primary-darken-1':   '#047857',
      'secondary':          '#B45309',   // Amber-700 — lebih gelap agar kontras
      'secondary-darken-1': '#92400E',
      'on-secondary':       '#FFFFFF',
      'success':            '#047857',   // Emerald-700 (lebih gelap untuk light mode)
      'success-darken-1':   '#065F46',
      'on-success':         '#FFFFFF',
      'info':               '#0369A1',   // Sky-700 (lebih gelap)
      'info-darken-1':      '#075985',
      'on-info':            '#FFFFFF',
      'warning':            '#B45309',   // Amber-700
      'warning-darken-1':   '#92400E',
      'on-warning':         '#FFFFFF',
      'error':              '#B91C1C',   // Red-700 (lebih gelap)
      'error-darken-1':     '#991B1B',
      'on-error':           '#FFFFFF',
      'background':         '#F0FDF4',
      'on-background':      '#052E16',   // Emerald-950 — sangat gelap untuk kontras max
      'surface':            '#FFFFFF',
      'on-surface':         '#052E16',   // Emerald-950
      'grey-50':            '#FAFAFA',
      'grey-100':           '#F5F5F5',
      'grey-200':           '#EEEEEE',
      'grey-300':           '#D1D5DB',
      'grey-400':           '#9CA3AF',
      'grey-500':           '#6B7280',
      'grey-600':           '#4B5563',
      'grey-700':           '#374151',
      'grey-800':           '#1F2937',
      'grey-900':           '#111827',
      'perfect-scrollbar-thumb': '#A7F3D0',
      'skin-bordered-background': '#FFFFFF',
      'skin-bordered-surface':    '#FFFFFF',
      'expansion-panel-text-custom-bg': '#F0FDF4',
      'track-bg':    '#ECFDF5',
      'chat-bg':     '#F0FDF4',
    },
    variables: {
      'code-color':               '#059669',
      'overlay-scrim-background': '#052E16',
      'tooltip-background':       '#052E16',
      'overlay-scrim-opacity':    0.6,
      'hover-opacity':            0.06,
      'focus-opacity':            0.12,
      'selected-opacity':         0.10,
      'activated-opacity':        0.18,
      'pressed-opacity':          0.16,
      'dragged-opacity':          0.12,
      'disabled-opacity':         0.38,
      'border-color':             '#052E16',
      'border-opacity':           0.12,
      'table-header-color':       '#ECFDF5',
      // HIGH CONTRAST — opacity penuh
      'high-emphasis-opacity':    1.0,
      'medium-emphasis-opacity':  0.72,
      'shadow-key-umbra-color':   '#052E16',
      'shadow-xs-opacity':        '0.08',
      'shadow-sm-opacity':        '0.10',
      'shadow-md-opacity':        '0.12',
      'shadow-lg-opacity':        '0.14',
      'shadow-xl-opacity':        '0.16',
    },
  },

  dark: {
    dark: true,
    colors: {
      'primary':            '#34D399',   // Emerald-400 — cerah di atas gelap
      'on-primary':         '#022C22',
      'primary-darken-1':   '#10B981',
      'secondary':          '#FCD34D',   // Amber-300 — cerah di atas gelap
      'secondary-darken-1': '#F59E0B',
      'on-secondary':       '#1C0A00',
      'success':            '#6EE7B7',   // Emerald-300
      'success-darken-1':   '#34D399',
      'on-success':         '#022C22',
      'info':               '#7DD3FC',   // Sky-300
      'info-darken-1':      '#38BDF8',
      'on-info':            '#082F49',
      'warning':            '#FDE68A',   // Amber-200
      'warning-darken-1':   '#FCD34D',
      'on-warning':         '#1C0A00',
      'error':              '#FCA5A5',   // Red-300
      'error-darken-1':     '#F87171',
      'on-error':           '#450A0A',
      'background':         '#071A0E',   // Very dark emerald
      'on-background':      '#ECFDF5',   // Emerald-50 — sangat terang
      'surface':            '#0D2818',   // Dark emerald surface
      'on-surface':         '#ECFDF5',   // Emerald-50 — sangat terang
      'grey-50':            '#0F2E1A',
      'grey-100':           '#133522',
      'grey-200':           '#1C4731',
      'grey-300':           '#275A40',
      'grey-400':           '#3D7A57',
      'grey-500':           '#5A9E74',
      'grey-600':           '#7DC49A',
      'grey-700':           '#A7DDB9',
      'grey-800':           '#C9EDDA',
      'grey-900':           '#E8F8EE',
      'perfect-scrollbar-thumb': '#1E5C35',
      'skin-bordered-background': '#0D2818',
      'skin-bordered-surface':    '#0D2818',
      'expansion-panel-text-custom-bg': '#071A0E',
      'track-bg':   '#133522',
      'chat-bg':    '#0D2818',
    },
    variables: {
      'code-color':               '#34D399',
      'overlay-scrim-background': '#000000',
      'tooltip-background':       '#ECFDF5',
      'overlay-scrim-opacity':    0.7,
      'hover-opacity':            0.08,
      'focus-opacity':            0.14,
      'selected-opacity':         0.12,
      'activated-opacity':        0.20,
      'pressed-opacity':          0.18,
      'dragged-opacity':          0.14,
      'disabled-opacity':         0.38,
      'border-color':             '#ECFDF5',
      'border-opacity':           0.15,
      'table-header-color':       '#133522',
      // HIGH CONTRAST — opacity penuh
      'high-emphasis-opacity':    1.0,
      'medium-emphasis-opacity':  0.80,
      'shadow-key-umbra-color':   '#000000',
      'shadow-xs-opacity':        '0.30',
      'shadow-sm-opacity':        '0.35',
      'shadow-md-opacity':        '0.40',
      'shadow-lg-opacity':        '0.45',
      'shadow-xl-opacity':        '0.50',
    },
  },
}

export default themes
