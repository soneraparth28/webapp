
// function registerEvent(params) {
//     $.ajax({
//         method: "post",
//         url: "/register-user-events",
//         data: params,
//         success: (res) => {
//             console.log(res);
//         },
//         error: (res) => {
//             console.log("result error: " + res.responseText);
//         }
//     })
// }


function startTransactionsFiltering(params) {
    $.ajax({
        method: "post",
        url: "/testing",
        data: params,
        success: (res) => {
            if(params.data_type === "chart") transactionFilterChart(res, params);
            else if(params.data_type === "table") transactionFilterTable(res, params);
        },
        error: (res) => {
            console.log("result error: " + res.responseText);
        }
    })
}


function transactionFilterTable(countryData) {
    let dataTable = $(document).find("table#transaction_table").first();
    let htmlObj = $("<tbody></tbody>");


    for(let countryName in countryData) {
        let data = countryData[countryName];
        htmlObj.append(
            "<tr>" +
                "<td>" + countryName + "</td>" +
                "<td>" + numberFormatting(data.net_sales, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
                "<td>" + numberFormatting(data.net_sales_excl_vat, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
                "<td>" + data.vat_percentage + "%</td>" +
                "<td>" + numberFormatting(data.vat, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
                "<td>" + shortNumbByT(data.total_sales) + "</td>" +
            "</tr>"
        );
    }

    dataTable.find("tbody").first().replaceWith(htmlObj);
    setDataTable(dataTable,[4, "desc"], false,[],100);
}


function transactionFilterChart(res, params) {
    let totalSalesCount = Object.keys(res).reduce(function (total, j) {return total + Object.keys(res[j]).length}, 0);
    let metaData = {stats: [], title: "Total sales : " + totalSalesCount, range_type: (params.month === "all" ? "year" : "month")};

    let chartData = {
        net_sales: {
            name: "net_sales",
            data: []
        },
        net_sales_excl_vat: {
            name: "net_sales_excl_vat",
            data: []
        },
        vat: {
            name: "vat",
            data: []
        },
        total_sales: {
            name: "total_sales",
            data: []
        },
    };
    for(let entryDate in res) {
        let items = res[entryDate];
        let amountInclVat = 0, amountExclVat = 0, amountVat = 0;
        if(Object.keys(items).length)
            // amountInclVat = parseFloat((Object.keys(items).reduce(function (total, i) {
            //     return total + items[i].amount;
            // }, amountInclVat).toFixed(2)))

        if(Object.keys(items).length) {
            for(let i in items) {
                let item = items[i];
                let amount = parseFloat(item.amount);
                amountExclVat += amount;
                // amountInclVat += amount;

                if(item.tax_details !== null) {
                    let taxPercentage = parseFloat(item.tax_details.percentage);
                    amountInclVat = parseFloat((amount * ((1 + (taxPercentage / 100)))).toFixed(2))
                    // let taxAmount = parseFloat((amount * ((taxPercentage / 100))).toFixed(2))

                    amountVat += (amountInclVat - amountExclVat)

                    // amountExclVat += parseFloat(((amount - taxAmount).toFixed(2)));
                    // amountVat += taxAmount
                }
                else amountInclVat += amount;
                // else amountExclVat += amount;
            }
        }

        chartData.total_sales.data.push(
            {
                x: new Date(entryDate).getTime(),
                y: Object.keys(items).length
            }
        );
        chartData.net_sales.data.push(
            {
                x: new Date(entryDate).getTime(),
                y: amountInclVat
            }
        );
        chartData.net_sales_excl_vat.data.push(
            {
                x: new Date(entryDate).getTime(),
                y: amountExclVat
            }
        );
        chartData.vat.data.push(
            {
                x: new Date(entryDate).getTime(),
                y: amountVat
            }
        );
    }

    drawChart(chartData, metaData);
}

let formSelects = $("#transaction_filter").find("select");
if(formSelects.length) {
    formSelects.each(function () {
        $(this).on("change", function () {

            let params = {
                format_by_date: true,
                country: document.getElementById("filter_country").value,
                year: document.getElementById("filter_year").value,
                month: document.getElementById("filter_month").value,
                _token: document.getElementById("token_item").value,
                data_type: "chart"
            };
            startTransactionsFiltering(params);
        })
        $(this).trigger("change");
    })
}



function isNormalInteger(str) {
    let n = Math.floor(Number(str));
    return n !== Infinity && String(n) === str && n >= 0;
}
function numberFormatting(number, opt = {}) {
    if(isNormalInteger(number)) number = parseFloat(number);
    return new Intl.NumberFormat('us-US', opt).format(number);
}
function ucFirst(string) {
    return empty(string) ? string : string[0].toUpperCase() +  string.slice(1);
}
function empty(variable) {
    return variable === null || variable === "" ||
        (typeof variable === "object" && Object.keys(variable).length === 0) ||
        typeof variable === "undefined" || variable === undefined;
}
function prepareProperNameString(string,UCA = true, UCF = true, ucExceptions = false) {
    if(empty(string)) return string;
    if(typeof string !== "string") string = string.toString();
    string = (string.trim()).replaceAll("_"," ");
    let response = [];
    if(UCA) {
        let words = string.split(" ");
        for(let word of words) {
            let wordV = word.toLowerCase()
            wordV = ucFirst(word);
            response.push(wordV);
        }
    }
    return UCA ? response.join(" ") : (UCF ? ucFirst(string) : string);
}

function shortNumbByT(number,shortM = true, shortK = false, includeCharSeparate = false) {
    number = typeof number !== "number" ? parseInt(number) : number;
    let mil = 1000000, kilo = 1000,m="M",k="K", response = "";
    if((number >= mil || number <= -mil)  && shortM) {
        let x = (number / mil).toFixed(1);
        response = includeCharSeparate ? {number: x,char:m} : x+m;
    } else if((number >= kilo || number <= -kilo) && shortK) {
        let x = (number / kilo).toFixed(1);
        response = includeCharSeparate ? {number: x,char:k} : x+k;
    } else
        response = number;
    return response;
}


function drawChart(chartData, metaData) {
    let title = metaData.title;
    let rangeType = metaData.range_type;

    var options = {
        chart: {
            type: 'area',
            height: "750px",
            toolbar: {
                show: false
            }
        },
        colors: ["#124dfd", "transparent", "transparent", "transparent"],
        series: [
            chartData.net_sales,
            chartData.net_sales_excl_vat,
            chartData.vat,
            chartData.total_sales,
        ],
        stroke: {
            curve: 'straight',
        },
        dataLabels: {
            enabled: false,
        },
        xaxis: {
            type: 'datetime',
            labels: {
                style: {
                    colors: "#fff",
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    cssClass: 'apexcharts-xaxis-label',
                },
                formatter: function (value, timestamp) {
                    let givenDate = new Date(timestamp);
                    if(rangeType === "month") {
                        let dayOfWeekName = givenDate.toLocaleString( 'default', {weekday: 'short'} );
                        let dayOfMonth = givenDate.getDate();
                        if(dayOfMonth < 10) dayOfMonth = "0" + dayOfMonth;
                        let monthOfYear = givenDate.toLocaleString( 'default', {month: 'short'} );
                        return[ dayOfWeekName, dayOfMonth + " " + monthOfYear];
                    }
                    if(rangeType === "year") return givenDate.toLocaleString( 'default', {month: 'short'} );
                    return value;
                },
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: "#fff",
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    cssClass: 'apexcharts-yaxis-label',
                },
                formatter: (value) => { return "€" + value },
            }
        },
        legend: {
            show: false
        },
        tooltip: {
            shared:true,
            color: "#000",
            x: {
                show: true,
                format: 'dd MMM',
                formatter: (timestamp, opt) => {
                    let givenDate = new Date(timestamp);
                    let year = givenDate.getFullYear();

                    if(rangeType === "month") {
                        let dayOfWeekName = givenDate.toLocaleString( 'default', {weekday: 'long'} );
                        let dayOfMonth = givenDate.getDate();
                        if(dayOfMonth < 10) dayOfMonth = "0" + dayOfMonth;
                        let monthOfYear = givenDate.toLocaleString( 'default', {month: 'long'} );
                        return dayOfWeekName + ", " + dayOfMonth + " " + monthOfYear + " "  + year;
                    }

                    let monthOfYear = givenDate.toLocaleString( 'default', {month: 'long'} );
                    return monthOfYear + " " + year
                },
            },
            y: {
                formatter: (value, opt) => {
                    let index = opt.seriesIndex;
                    if(Object.keys(chartData)[index] === "total_sales") return shortNumbByT(value);
                    return "€" + numberFormatting(value);
                },
                // style: {
                //     fontWeight: "normal"
                // },
                title: {
                    formatter: (value, opt) => {
                        return prepareProperNameString(value, false);
                    }
                }
            },
            marker: {
                show: false,
            },
            style: {
                fontWeight: "bold",
                fontSize: '13px',
            }
        },
        title: {
            text: title,
            align: 'left',
            margin: 10,
            offsetX: 0,
            offsetY: 0,
            floating: false,
            style: {
                fontSize:  '14px',
                fontWeight:  'normal',
                fontFamily:  undefined,
                color:  '#fff'
            },
        }
    };

    let element = document.querySelector("#transactionFilterChart");
    if($(element).find(".apexcharts-canvas").length) {
        let el = $(element), resizeEl = el.siblings(".resize-triggers").first();
        if(resizeEl.length) resizeEl.remove();
        el.empty();
    }

    var chart = new ApexCharts(element, options);
    chart.render();

    let totalExclVatEl = $(document).find(".apexcharts-series[seriesName=netxsalesxexclxvat]");
    let totalVat = $(document).find(".apexcharts-series[seriesName=vat]");
    let totalSales = $(document).find(".apexcharts-series[seriesName=totalxsales]");
    if(totalExclVatEl.length) totalExclVatEl.first().hide();
    if(totalVat.length) totalVat.first().hide();
    if(totalSales.length) totalSales.first().hide();
}



if($(document).find(".plainDataTable").length) {
    if(typeof setDataTable == "function") {
        $(document).find(".plainDataTable").each(function () {
            let table = $(this), paginationLimit = table.data("pagination-limit"), sortingColumn = table.data("sorting-col"), sortingOrder = table.data("sorting-order");

            if(paginationLimit === undefined || empty(paginationLimit)) paginationLimit = 100;
            if(sortingColumn === undefined || empty(sortingColumn)) sortingColumn = 0;
            if(sortingOrder === undefined || empty(sortingOrder)) sortingOrder = "desc";

            setDataTable(table, [sortingColumn, sortingOrder], false,[], paginationLimit);
        });
    }
}








async function setDateRangePicker() {
    if($(document).find(".DP_RANGE").length) {
        $(document).find(".DP_RANGE").each(async function (){
            let el = $(this), startDate = 0, endDate = 0, ranges = {};
            if(el.data("no-pick") === true) return; //Used for displaying period-lists mainly


            let isTimerPicker = el.data("is-time-picker") === undefined || el.data("is-time-picker") !== false;
            let givenStart = el.attr("data-preset-start-time");


            if(givenStart === "today") {
                startDate = moment().startOf('day');
                endDate = moment().endOf('day');
            }
            else {
                let start = {count: 24, unit: 'hour'};
                if(givenStart !== undefined && !empty(givenStart)) {
                    let listStart = givenStart.split(",");
                    start = {count: parseInt(listStart[0]), unit: listStart[1]};
                }

                startDate = moment().startOf('minute').subtract(start.count, start.unit);
                endDate = moment().startOf('minute').add(1, 'hour')
            }


            if($(this).data("custom-picking") !== false) {
                ranges = {
                    'Today': [moment().startOf('day'), moment()],
                    'Yesterday': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                };
            }

            let params = {
                format_country: true,
                _token: document.getElementById("token_item").value,
                data_type: "table"
            };

            el.daterangepicker({
                opens: 'left',
                startDate: new Date(startDate),
                endDate: new Date(endDate),
                locale: {
                    format: isTimerPicker ? 'DD MMMM YYYY, HH:mm' : 'DD MMMM YYYY'
                },
                ranges
            }, function(start, end) {
                startTransactionsFiltering({
                    ...params,
                    ... {
                        start: Math.round((new Date(start)).valueOf() / 1000),
                        end: Math.round((new Date(end)).valueOf() / 1000),
                    }
                });
            });

            startTransactionsFiltering({
                ...params,
                ... {
                    start: Math.round((new Date(startDate)).valueOf() / 1000),
                    end: Math.round((new Date(endDate)).valueOf() / 1000),
                }
            });

        });
    }
}


if(typeof setDateRangePicker == "function") {
    setDateRangePicker();
}



