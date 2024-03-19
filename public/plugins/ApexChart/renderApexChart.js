function renderCharts(el, data, chartType, formatterTitle = "") {
    let colorListA = {red: "#fe7773", purple: "#3A3866", green: "#569C75"},
        colorListB = ["#fe7773","#031927","#87ceeb","#ffd8d8","#569C75","#4500F0","#E57BFF"];
    data.color_list = colorListB;
    switch (chartType) {
        default: return false;
        case "line":
            lineChart( el, data );
            break;
        case "bar":
            barChart( el, data, formatterTitle);
            break;
        case "donut":
            pieChart( el, data, chartType, formatterTitle);
            break;
        case "pie":
            pieChart( el, data, chartType, formatterTitle);
            break;
        case "stackedBar":
            stackedBar( el, data);
            break;

    }
}




function lineChart(element, opt) {
    if(!Object.keys(opt).includes("series") || !Object.keys(opt).includes("labels") || !Object.keys(opt).includes("color_list")
        || !Object.keys(opt).includes("title") ) return false;

    if(Object.keys(opt.series).includes("data")) opt.series = [opt.series];

    let options = {
        chart: {
            type: 'line',
            width: '100%',
            height: '400px',
            background: "transparent",
            toolbar: {
                show: false
            }
        },
        colors: opt.color_list,
        series: opt.series,
        xaxis: {
            categories: opt.labels,
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            },
            labels: {
                rotate: -10, // no need to rotate since hiding labels gives plenty of room
                hideOverlappingLabels: true,  // all labels must be rendered
            }
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                horizontal: true
            }
        },
        title: {
            text: opt.title,
            align: "center",
            style: {
                fontWeight: "400",
                fontSize: "16px",
            }
        },
        stroke: {
            curve: "smooth"
        },
        tooltip: {
            y: {
                formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                    return shortNumbByT(value,true,true);
                }
            }
        }
    };

    if($(element).find(".apexcharts-canvas").length) {
        let el = $(element), resizeEl = el.siblings(".resize-triggers").first();
        if(resizeEl.length) resizeEl.remove();
        el.empty();
    }

    (new ApexCharts(element, options)).render();
}


function pieChart(element, opt, type, formatterTitle = "") {
    if(!Object.keys(opt).includes("series") || !Object.keys(opt).includes("labels") || !Object.keys(opt).includes("color_list")) return false;

    let legendPosition = "right", height = "200", width = false, title = ""
    if(Object.keys(opt).includes("legend_position")) legendPosition = opt.legend_position;
    if(Object.keys(opt).includes("height")) height = opt.height;
    if(Object.keys(opt).includes("width")) width = opt.width;
    if(Object.keys(opt).includes("title")) title = opt.title;

    let chart = {type};
    if(height !== false) chart.height = height;
    if(width !== false) chart.width = width;


    let options = {
        chart,
        series: opt.series,
        labels: opt.labels,
        colors: opt.color_list,
        legend:{
            show:true,
            position: legendPosition
        },
        tooltip: {
            y: {
                formatter: (value) => {
                    return (formatterTitle === "number_format" ? numberFormatting(value) : value + formatterTitle)
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '55%'
                }
            }
        },
        title: {
            text: title,
            align: "left",
            style: {
                fontWeight: "600",
                fontSize: "16px",
            }
        }
    };

    if($(element).find(".apexcharts-canvas").length) {
        let el = $(element), resizeEl = el.siblings(".resize-triggers").first();
        if(resizeEl.length) resizeEl.remove();
        el.empty();
    }
    (new ApexCharts(element, options)).render();
}


function barChart(element, opt, formatterTitle = "") {
    if(!Object.keys(opt).includes("series") || !Object.keys(opt).includes("labels") || !Object.keys(opt).includes("color_list")) return false;

    if(Object.keys(opt.series).includes("data")) {
        if(Object.keys(opt.series.data).length > 0 && typeof opt.series.data[(Object.keys(opt.series.data)[0])] !== "object")
            opt.series = [{data: opt.series.data, name: opt.series.name}];
    }
    else {
        if(Object.keys(opt.series).length > 0 && typeof opt.series[(Object.keys(opt.series)[0])] !== "object") {
            opt.series =  [{data: opt.series, name: opt.title}];
        }
    }





    let chart = {
        type: "bar",
        toolbar: {
            show: false
        }
    },
    plotOptions = {
        bar: {
            borderRadius: 5,
            horizontal: false,
            // barHeight: "80%",
            // columnWidth: '70%',
            // distributed: false,
            // rangeBarOverlap: true,
            // rangeBarGroupRows: false,
            // dataLabels: {
            //     position: 'top',
            //     maxItems: 12,
            //     hideOverflowingLabels: true,
            //     orientation: "horizontal"
            // }
        }
    },
    yaxis = {};

    let orientation = "vertical", yaxisDirection = "normal", height = "400", width = "100%", title = "";
    if(Object.keys(opt).includes("orientation")) orientation = opt.orientation;
    if(Object.keys(opt).includes("yaxis_direction")) yaxisDirection = opt.yaxis_direction;
    if(Object.keys(opt).includes("height")) height = opt.height;
    if(Object.keys(opt).includes("width")) width = opt.width;
    if(Object.keys(opt).includes("title")) title = opt.title;

    if(height !== false) chart.height = height;
    if(width !== false) chart.width = width;

    if(orientation === "horizontal") plotOptions.bar.horizontal = true;
    if(yaxisDirection === "reversed") yaxis.reversed = true;

    let options = {
        chart,
        colors: opt.color_list,
        grid: {
            show: true
        },
        title: {
            text: title,
            align: "left",
            style: {
                fontWeight: "600",
                fontSize: "16px",
            }
        },
        series: opt.series,
        xaxis: {
            categories: opt.labels,
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true,
            },
            labels: {
                show: true,
            }
        },
        yaxis,
        plotOptions,
        // legend: {
        //     show: false
        // },
        // options: {
        //     scales: {
        //         y: {
        //             beginAtZero: true
        //         }
        //     }
        // },
        dataLabels: {
            enabled: false,
            // position: 'bottom',
            // textAnchor: 'start',
            // style: {
            //     colors: ['#000']
            // },
            // offsetX: 0,
            // dropShadow: {
            //     enabled: false
            // },
            // formatter: function (value, formOpt) {
            //     return (formatterTitle === "number_format" ? numberFormatting(value) : value + formatterTitle)
            // },
        },
        tooltip: {
            y: {
                formatter: function(value, formOpt) {
                    return (formatterTitle === "number_format" ? numberFormatting(value) : value + formatterTitle)
                }
            },
            shared: true,
            enabled: true,
            intersect: false
        }
    };

    if($(element).find(".apexcharts-canvas").length) {
        let el = $(element), resizeEl = el.siblings(".resize-triggers").first();
        if(resizeEl.length) resizeEl.remove();
        el.empty();
    }

    (new ApexCharts(element, options)).render();
}



function areaChart(element, chartColor) {
    let dataList = [[30, 55, 45, 50],[90, 58, 60, 50],[3, 5, 8, 2]],
        random = Math.floor((Math.random() * 3)),
        chosenData = dataList[random];

    var options = {
        chart: {
            height: "100%",
            type: "area",
            toolbar: {
                show: false
            }
        },
        stroke: {
            width: 1
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        tooltip: {
            enabled: false
        },
        grid: {
            show: false
        },
        colors: [chartColor],
        series: [
            {
                data: chosenData
            }
        ],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.9,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            tooltip: { enabled: false },
            categories: [
                0,
                1,
                2,
                3
            ],
            labels: {
                show: false
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            }
        },
        yaxis: {
            labels: {
                show: false
            }
        }
    };

    if($(element).find(".apexcharts-canvas").length) {
        let el = $(element), resizeEl = el.siblings(".resize-triggers").first();
        if(resizeEl.length) resizeEl.remove();
        el.empty();
    }
    (new ApexCharts(element, options)).render();
}



function stackedBar(element, opt) {
    if(!Object.keys(opt).includes("series") || !Object.keys(opt).includes("color_list")) return false;

    // {title: "some title", data: [["item 1", 25], ["item 2", 75]]}


    let stackedBarContainer = $('<div class="stacked-bar-chart w-100"></div>');

    for(let item of opt.series) {
        if(!Object.keys(item).includes("title") || !Object.keys(item).includes("total") || !Object.keys(item).includes("data") || empty(item.data)) continue;

        let barElement = $('<div class="row mb-2 flex-align-center"></div>'),
            dataColumn = $('<div class="col"></div>'), dataRow = $('<div class="row"></div>');

        if(!("include_title" in opt) || opt.include_title !== false)
            barElement.append('<div class="col-1 align-self-center">' + prepareProperNameString(item.title) + '</div>');

        let barColorKey = 0;
        for(let i in item.data) {
            let dataItem = item.data[i];
            // let percentage = Math.round(dataItem[1] / item.total * 100);
            let percentage = parseFloat((dataItem[1] / item.total * 100).toFixed(2));
            if(percentage < 3) continue;

            if(!(percentage > 0)) continue;
            if(barColorKey > (opt.color_list.length - 1)) barColorKey = 0;

            let itemCol = $('<div class="col-auto text-center stacked-data-bar"></div>'),
                style = 'width: ' + percentage + '%; background: ' + opt.color_list[barColorKey] + '; padding: 5px;';

            itemCol.text(prepareProperNameString(dataItem[0]) + " (" + percentage + "%)");
            itemCol.attr("style", style);

            if(!("tooltip" in opt) || opt.tooltip !== false) {
                itemCol.attr("data-toggle", "tooltip");
                itemCol.attr("data-placement", "top");
                itemCol.attr("title", prepareProperNameString(dataItem[0]) + ": " +percentage + "%");
                itemCol.tooltip();
            }

            dataRow.append(itemCol);
            barColorKey++;
        }

        dataColumn.append(dataRow);
        barElement.append(dataColumn);
        stackedBarContainer.append(barElement);
    }

    $(element).html(stackedBarContainer);
}