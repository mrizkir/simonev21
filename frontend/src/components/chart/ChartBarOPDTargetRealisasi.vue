<script>
  import { HorizontalBar } from "vue-chartjs";

  export default {
    name: "ChartBarOPDTargetRealisasi",
    extends: HorizontalBar,

    props: {
      labels: {
        type: Array,
        required: true,
      },
      target: {
        type: Array,
        required: true,
      },
      realisasi: {
        type: Array,
        required: true,
      },
    },
    mounted() {      
      this.renderChart(this.chartdata, this.options);
    },
    data() {
      return {
        chartdata: {   
          labels: this.labels,         
          datasets: [
            {
              label: 'Target',
              data: this.target,
              fill: false,
              backgroundColor: [],
              borderWidth: 1,
            },
            {
              label: 'Realisasi',
              data: this.realisasi,
              fill: false,
              backgroundColor: [],            
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,        
          // maintainAspectRatio: false,
        },      
      };
    },
  };
</script>
