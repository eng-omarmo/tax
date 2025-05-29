function initTaxTrendChart(selector, data) {
    const quarters = data.map(item => item.quarter);
    const billed = data.map(item => item.billed);
    const collected = data.map(item => item.collected);
    const outstanding = data.map(item => item.outstanding);

    const options = {
        series: [
            {
                name: 'Tax Billed',
                type: 'line',
                data: billed
            },
            {
                name: 'Tax Collected',
                type: 'column',
                data: collected
            },
            {
                name: 'Outstanding',
                type: 'column',
                data: outstanding
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            stacked: false,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        stroke: {
            width: [3, 0, 0],
            curve: 'smooth'
        },
        colors: ['#4F46E5', '#10B981', '#F59E0B'],
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        fill: {
            opacity: [1, 0.85, 0.85],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55,
            }
        },
        labels: quarters,
        markers: {
            size: 0
        },
        xaxis: {
            type: 'category'
        },
        yaxis: {
            title: {
                text: 'Amount ($)',
            },
            labels: {
                formatter: function (val) {
                    return '$' + (val / 1000).toFixed(0) + 'k';
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return "$" + y.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    return y;
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector(selector), options);
    chart.render();
}
