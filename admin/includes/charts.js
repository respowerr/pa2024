//PARITY CHART

var xValues = ["Man", "Women"];
var yValues = [55, 49];
var barColors = [
  "#b91d47",
  "#00aba9"
];

new Chart("parity", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Gender parity"
    }
  }
});