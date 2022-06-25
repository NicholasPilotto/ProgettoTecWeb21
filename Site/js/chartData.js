function loading()
{
    var xValues = JSON.parse(document.getElementById("jsondataX").value);
    var yValues = JSON.parse(document.getElementById("jsondataY").value);

    var ctx = document.getElementById("myChart").getContext("2d");
    
    var config = {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                fill: true,
                borderColor: "#146e53",
                data: yValues
            }]
        },
        options: {
            maintainAspectRatio: false,
            title: {
                display: true,
                text: "Guadagni ultimo semestre",
                fontSize: 16
            },
            legend: {display: false},
            scales: {
                yAxes: [{
                ticks: {
                    min:0,
                    callback: function(value, index, ticks) {
                        return '€' + value;
                    }
                }
            }],
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                            return '€' + tooltipItem.yLabel;
                    }
                }
            }
        }
    };

    new Chart(ctx, config);
}

window.addEventListener('load', function () {
    loading();
});