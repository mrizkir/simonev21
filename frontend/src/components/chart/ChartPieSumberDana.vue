<script>
  import { Pie, mixins } from "vue-chartjs";
  export default {
    name: "ChartPieSumberDana",
    extends: Pie,
    mixins: [mixins.reactiveProp],
    data: () => ({
    }),
    mounted () {
      this.renderChart(this.chartData, this.chartData.options)
    }    
  };
</script>
