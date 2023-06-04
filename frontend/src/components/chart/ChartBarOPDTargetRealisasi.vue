<script>
  import { HorizontalBar } from "vue-chartjs";
  import ChartDataLabels from "chartjs-plugin-datalabels";
  // import ShortGridLines from "@/components/chart/chartjs-plugin-short-gridlines";
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
      this.addPlugin(ChartDataLabels);
      let shortgridlines = {
        id: "shortgridlines",
        beforeDatasetsDraw: function(chart, args) {
          args;
          const { ctx, scales } = chart;

          ctx.save();
          ctx.beginPath();
          ctx.lineWidth = 1;
          ctx.strokeStyle = "yellow";
          scales["y-axis-1"]._gridLineItems[5].color = "red";
          scales["y-axis-1"]._gridLineItems[5].width = 4;
          scales["y-axis-1"]._gridLineItems[10].color = "red";
          scales["y-axis-1"]._gridLineItems[10].width = 4;
          scales["y-axis-1"]._gridLineItems[15].color = "red";
          scales["y-axis-1"]._gridLineItems[15].width = 4;
          scales["y-axis-1"]._gridLineItems[20].color = "red";
          scales["y-axis-1"]._gridLineItems[20].width = 4;
          ctx.stroke();
          ctx.closePath();
        },
      };
      this.addPlugin(shortgridlines);
      this.renderChart(this.chartdata, this.options);
    },
    data() {
      return {
        chartdata: {
          labels: this.labels,
          datasets: [
            {
              label: "Realisasi",
              data: this.realisasi,
              fill: false,
              backgroundColor: "green",
              borderWidth: 1,
            },
            {
              label: "Target",
              data: this.target,
              fill: false,
              backgroundColor: "blue",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            datalabels: {
              color: "white",
              textAlign: "center",
              font: {
                weight: "bold",
                size: 8,
              },
              formatter: value => {
                if (value > 2.0) {
                  return value + "%";
                } else {
                  return "";
                }
              },
            },
          },
          scales: {
            xAxes: [
              {
                ticks: {
                  min: 0,
                  max: 100,
                  stepSize: 5,
                  callback: function(value) {
                    return ((value / this.max) * 100).toFixed(0) + "%";// convert it to percentage
                  },
                },
                type: "linear",
                display: true,
                position: "left",
                id: "y-axis-1",
                gridLines: {
                  drawOnArea: false,
                  borderDash: [8, 4],
                  color: "#348632",
                },
              },
            ],
            yAxes: [
              {
                gridLines: {
                  drawBorder: true,
                  display: false,
                },
              },
            ],
          },
          borderWidth: 1,
        },
      };
    },
  };
</script>
