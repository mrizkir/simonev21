<template>
  <chart :datagrafik="chartData" v-if="chartLoaded" />
</template>
<script>
  import ChartTargetRealisasi from "@/components/chart/ChartTargetRealisasi";
  export default {
    name: "MobileChartMurniKeuangan",
    created() {
      this.ta = this.$route.params.ta;
      this.bulan = this.$route.params.bulan;
    },
    mounted() {
      this.initialize();
    },
    data() {
      return {
        ta: null,
        bulan: null,
        chartLoaded: false,
        chartData: [[], []],
      };
    },
    methods: {
      async initialize() {
        await this.$ajax
          .post("/dashboard/front", {
            ta: this.ta,
            bulan_realisasi: this.bulan,
          })
          .then(({ data }) => {
            this.chartData[0] = data.chart_keuangan_murni[0];
            this.chartData[1] = data.chart_keuangan_murni[1];
            this.chartLoaded = true;
          });
      },
    },
    components: {
      chart: ChartTargetRealisasi,
    },
  };
</script>
