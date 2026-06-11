import { defineAsyncComponent } from 'vue'

export default function (app) {
  app.component('apexchart', defineAsyncComponent(() => import('vue3-apexcharts')))
}
