

async function ajaxRequest(endpoint, params, method = "post") {
    return await $.ajax({
        method,
        url: endpoint,
        data: params,
        // success: (res) => {
        //     console.log(res);
        // },
        // error: (res) => {
        //     console.log("result error: " + res.responseText);
        // }
    })
}


function registerEvent(params) {
    $.ajax({
        method: "post",
        url: "/register-user-events",
        data: params,
        success: (res) => {
            console.log(res);
        },
        error: (res) => {
            console.log("result error: " + res.responseText);
        }
    })
}

if(!("getCsrfToken" in window)) {
    const getCsrfToken = () => {
        return document.querySelector("meta[name=csrf-token]").getAttribute("content");
    }
}


function invoiceLookup() {
    let invoiceField = $(document).find("#invoice-lookup").first();
    if(!invoiceField.length) return false;
    let id = invoiceField.val();
    let url = window.location.href + "/" + id;

    window.open(url, "_blank")
}
$(document).on("click", "button#invoiceLookupBtn", function () {invoiceLookup()});







$(document).ready(function () {

    let params = {
        event_type: "page_view",
        event_value: window.location.pathname,
        _token: document.getElementById("token_item").value
    };
    registerEvent(params);





})

function validURL(str) {
    let pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}

function getObjLastElement(obj) {
    if(!Object.keys(obj).length) return null;
    return obj[(Object.keys(obj).length - 1)];
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



function sortByKey(arr,key = "id", ascending = false, key2 = "") {
    arr.sort(function (a, b) {
        if(!(key in a && key in b)) return 0;

        let val1, val2;
        val1 = a[key];
        val2 = b[key];

        if(!empty(key2)) {
            val1 = val1[key2];
            val2 = val2[key2];
        }

        val1 = typeof val1 !== "number" ? parseFloat(val1) : val1;
        val2 = typeof val2 !== "number" ? parseFloat(val2) : val2;
        if (val1 === val2) return 0;
        return (val1 > val2) ? (ascending ? 1 : -1) : (ascending ? -1 : 1) ;
    });

    return arr;
}


function drawChart(chartData, metaData) {
    let title = metaData.title;
    let rangeType = metaData.range_type;

    var options = {
        chart: {
            type: 'area',
            height: "400px",
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


if($(document).find("#search-creators-form").length) {
    let form = $(document).find("#search-creators-form").first();
    form.on("submit", function (e) {
        e.preventDefault();
        let el = $(this);
        if(el.hasClass("form-active")) el.submit();
    })

    let selector = "#search-creators-form";
    $(document).on("click", function (){
        if(form.hasClass("form-active")) form.removeClass("form-active");
    });

    $(document).on("click", selector, function (e) {
        e.stopPropagation();
        let el = $(this);
        console.log([el.attr("id"), el.parents(selector).length, selector, el]);
        if("#" + el.attr("id") === selector || el.parents(selector).length) {
            if(!form.hasClass("form-active")) form.addClass("form-active");
        }
        else if(form.hasClass("form-active")) form.removeClass("form-active");

    });
}


function textMimic(textField) {
    let mimicSearchId = textField.attr("data-mimic-search");
    if(mimicSearchId === undefined) return;

    console.log([mimicSearchId]);

    let mimicTargetField = $(document).find("[data-mimic-id=" + mimicSearchId + "]");
    if(!mimicTargetField.length) return;
    mimicTargetField = mimicTargetField.first();

    mimicTargetField.text(textField.val());
}

if($(document).find(".mimic-text-input").length) {
    $(document).on("change", ".mimic-text-input", function () {
        textMimic($(this));
    })
}



function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)

    );
}

function videoIsPlaying(videoPlayer) {
    return !!(videoPlayer.currentTime > 0 && !videoPlayer.paused && !videoPlayer.ended && videoPlayer.readyState > 2);
}

function videoAddAutoPlayOnView() {

    if($(document).find(".plyr__video-wrapper").length) {
        $(document).find(".plyr__video-wrapper").each(function () {
            if($(this).attr("style") !== "undefined" && $(this).attr("style") !== undefined) $(this).removeAttr("style");
        })
    }
    let videoPlayers = $(document).find("#updatesPaginator video.js-player");

    document.addEventListener('scroll', function () {
        if(videoPlayers.length) {
            videoPlayers.each(function () {
                let videoPlayer = $(this).get(0);

                if(isInViewport(videoPlayer) && !videoIsPlaying(videoPlayer)) {
                    if(videoPlayer.readyState < 3) videoPlayer.load();
                    videoPlayer.play();
                }
                else if(!isInViewport(videoPlayer) && videoIsPlaying(videoPlayer)) videoPlayer.pause();
            })
        }
    }, {
        passive: true
    });

}
videoAddAutoPlayOnView();






$(document).on("click", "[data-href]",function () { window.location = $(this).attr("data-href"); });
















