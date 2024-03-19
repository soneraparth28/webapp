(function($) {
"use strict";

//Flat red color scheme for iCheck
$('input[type="radio"]').iCheck({
	radioClass: 'iradio_flat-red'
});

// Input checkbox iCheck
$('input[type="checkbox"]').iCheck({
	checkboxClass: 'icheckbox_square-red',
	radioClass: 'iradio_square-red',
	increaseArea: '20%'
});

//Colorpicker
$('.my-colorpicker2').colorpicker();

// Tags Input
$("#tagInput").tagsInput({
 'delimiter': [','],   // Or a string with a single delimiter. Ex: ';'
 'width':'auto',
 'height':'auto',
	 'removeWithBackspace' : true,
	 'minChars' : 2,
	 'maxChars' : 30,
	 'defaultText': add_tag
});

// Select2 Initiator
$('.select2 option[value="'+timezone+'"]').attr('selected', 'selected');
$('.select2').select2();

$('.select2Multiple').select2({
	tags: true, // Delete this line for force Defaults Programs
	tokenSeparators: [',', ' '],
});

// Add subcategories
$("#addSub").on('click', function(e) {

	e.preventDefault();

	var saveHtml    = $('#addSub').html();
	$('#addSub').attr({'disabled' : 'true'}).html('<i class="fa fa-cog fa-spin fa-1x fa-fw fa-loader"></i>');

    $.ajax({
        url: URL_BASE + "/panel/admin/subcategories/add",
        type:"POST",
        data: $('#addSubForm').serialize(),
        success:function(data){
            window.location.reload();
        },error:function(data){
           var errors =data.responseJSON;

		     var errorsHtml = '';

		    $.each(errors['errors'], function(index, value) {
		        errorsHtml += '<li><i class="glyphicon glyphicon-remove myicon-right"></i> ' + value + '</li>';
		        });

					$('#errorsAlert').html(errorsHtml);
					$('#boxErrors').fadeIn();
					$('#addSub').removeAttr('disabled').html(saveHtml);
		        }
    }); //end of ajax
});// End Add subcategories

// Delete Post, Categories, Members, Languages, etc...
$(".actionDelete").on('click', function(e) {
   	e.preventDefault();

   	var element = $(this);
    var form    = $(element).parents('form');

	element.blur();

	Swal.fire({
		title: delete_confirm,
		text: "You won't be able to revert this!",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: yes_confirm
	  }).then((result) => {
		if (result.isConfirmed) {
			form.submit();
		}
	  });

	/*new swal(
		{   title: delete_confirm,
		  type: "warning",
		  showLoaderOnConfirm: true,
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		   confirmButtonText: yes_confirm,
		   cancelButtonText: cancel_confirm,
		    closeOnConfirm: false,
		    },
		    function(isConfirm){
				alert(isConfirm);
		    	 if (isConfirm) {
		    	 	form.submit();
		    	 	}
          });*/
});

		 // Delete Blog
		 $(".actionDeleteBlog").on('click', function(e) {
		    	e.preventDefault();

		    var element = $(this);
				var form    = $(element).parents('form');

		 	element.blur();

		 	new swal(
		 		{   title: delete_confirm,
		 		  type: "warning",
		 		  showLoaderOnConfirm: true,
		 		  showCancelButton: true,
		 		  confirmButtonColor: "#DD6B55",
		 		   confirmButtonText: yes_confirm,
		 		   cancelButtonText: cancel_confirm,
		 		    closeOnConfirm: false,
		 		    },
		 		    function(isConfirm){
		 		    	 if (isConfirm) {
		 		    	 	form.submit();
		 		    	 	}
		 		    });
		 		 });

	 // Email Driver
	 $('#emailDriver').on('change', function() {

	     if($(this).val() == 'mailgun') {
	 			$('#mailgun').slideDown();
	 		} else {
	 				$('#mailgun').slideUp();
	 		}

	 		if($(this).val() == 'postmark') {
	 			$('#postmark').slideDown();
	 		} else {
	 			$('#postmark').slideUp();
	 		}

	     if($(this).val() == 'ses') {
	 			$('#ses').slideDown();
	 		} else {
	 			$('#ses').slideUp();
	 		}
	 });

	 // Accept Only Numbers
	 $(document).ready(function() {

	     $(".onlyNumber").on('keydown', function (e) {
	         // Allow: backspace, delete, tab, escape, enter and .
	         if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
	              // Allow: Ctrl+A, Command+A
	             (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
	              // Allow: home, end, left, right, down, up
	             (e.keyCode >= 35 && e.keyCode <= 40)) {
	                  // let it happen, don't do anything
	                  return;
	         }
	         // Ensure that it is a number and stop the keypress
	         if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	             e.preventDefault();
	         }
	     });
	 });

	 $('.isNumber').keypress(function (event) {
         return isNumber(event, this)
 });

 function isNumber(evt, element) {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (
         (charCode != 46 || $(element).val().indexOf('.') != -1) &&
         (charCode < 48 || charCode > 57))
         return false;
         return true;
 }

	 // Delete Image in Theme section
	 $('.delete-image').on('click', function() {
         var element = $(this);

         element.parents('.box-body').find('.fileContainer').addClass('display-none');
         element.parents('.box-body').find('.file-name-file').html('');
         element.parents('.box-body').find('.filePhoto').val('');
   });

	 // Upload Image in Theme section
	 $(".filePhoto").on('change', function(){

     var element = $(this);

     element.parents('.box-file').find('.text-file').html(choose_image);

   	var loaded = false;
   	if(window.File && window.FileReader && window.FileList && window.Blob){
       // Check empty input filed
   		if($(this).val()) {

   			var oFReader = new FileReader(), rFilter = /^(?:image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/png|image)$/i;
   			if($(this)[0].files.length === 0){return}

   			var oFile = $(this)[0].files[0];
         var fsize = $(this)[0].files[0].size; //get file size
   			var ftype = $(this)[0].files[0].type; // get file type

         // Validate formats
         if(!rFilter.test(oFile.type)) {
   				element.val('');
   				alert(formats_available);
   				return false;
   			}

         // Validate Size
         if(!rFilter.test(oFile.type)) {
   				element.val('');
   				alert(formats_available);
   				return false;
   			}

   			oFReader.onload = function (e) {

   				var image = new Image();
           image.src = oFReader.result;

   				image.onload = function() {

             element.parents('.box-body').find('.fileContainer').removeClass('display-none');
             element.parents('.box-body').find('.file-name-file').html(oFile.name);
           };// <<--- image.onload
         }
           oFReader.readAsDataURL($(this)[0].files[0]);
   		}// Check empty input filed
   	}// window File
   });
   // END Upload Image in Theme section

	 // Cancel Payment
	 $(".cancel_payment").on('click', function(e) {
	    	e.preventDefault();

	     var element = $(this);
	     var form    = $(element).parents('form');

	 	element.blur();

	 	swal(
	 		{   title: delete_confirm,
	 		  type: "warning",
	       text: cancel_payment,
	 		  showLoaderOnConfirm: true,
	 		  showCancelButton: true,
	 		  confirmButtonColor: "#DD6B55",
	 		   confirmButtonText: yes_cancel_payment,
	 		   cancelButtonText: cancel_confirm,
	 		    closeOnConfirm: false,
	 		    },
	 		    function(isConfirm){
	 		    	 if (isConfirm) {
	 		    	 	form.submit();
	 		    	 	}
	 		    	 });
	 		 });

			 // Approve verification request
			 $(".actionApprove").on('click', function(e) {
			    	e.preventDefault();

			   var element = $(this);
				 var url     = element.attr('href');
				 var form    = $(element).parents('form');

			 	element.blur();

			 	swal(
			 		{   title: delete_confirm,
			 		  type: "warning",
			       text: approve_confirm_verification,
			 		  showLoaderOnConfirm: true,
			 		  showCancelButton: true,
			 		  confirmButtonColor: "#52bb03",
			 		   confirmButtonText: yes_confirm_approve_verification,
			 		   cancelButtonText: cancel_confirm,
			 		    closeOnConfirm: false,
			 		    },
			 		    function(isConfirm){
			 		    	 if (isConfirm) {
			 		    	 	form.submit();
			 		    	 	}
			 		    	});
			 		 });

					 // Delete verification request
					 $(".actionDeleteVerification").on('click', function(e) {
					    	e.preventDefault();

					  var element = $(this);
					 	var url     = element.attr('href');
					 	var form    = $(element).parents('form');

					 	element.blur();

					 	swal(
					 		{   title: delete_confirm,
					 		  type: "warning",
					       text: delete_confirm_verification,
					 		  showLoaderOnConfirm: true,
					 		  showCancelButton: true,
					 		  confirmButtonColor: "#DD6B55",
					 		   confirmButtonText: yes_confirm_verification,
					 		   cancelButtonText: cancel_confirm,
					 		    closeOnConfirm: false,
					 		    },
					 		    function(isConfirm){
					 		    	 if (isConfirm) {
					 		    	 	form.submit();
					 		    	 	}
					 		    });
					 		});

		// login as User...
		$(".loginAsUser").on('click', function(e) {
		   	e.preventDefault();

		   	var element = $(this);
		    var form    = $(element).parents('form');

			element.blur();

            Swal.fire(
                {
                    title: delete_confirm,
                    text: login_as_user_warning,
                    type: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    confirmButtonColor: "#52bb03",
                    confirmButtonText: yes,
                    cancelButtonText: cancel_confirm,
                    closeOnConfirm: false,
                })
                .then( result => {
                    if (result.isConfirmed) form.submit();
                });

        });

				 // Approve post
				 $(".actionApprovePost").on('click', function(e) {
				    	e.preventDefault();

				   var element = $(this);
					 var url     = element.attr('href');
					 var form    = $(element).parents('form');

				 	element.blur();

				 	swal(
				 		{   title: delete_confirm,
				 		  type: "warning",
				       text: approve_confirm_post,
				 		  showLoaderOnConfirm: true,
				 		  showCancelButton: true,
				 		  confirmButtonColor: "#52bb03",
				 		   confirmButtonText: yes_confirm_approve_post,
				 		   cancelButtonText: cancel_confirm,
				 		    closeOnConfirm: false,
				 		    },
				 		    function(isConfirm){
				 		    	 if (isConfirm) {
				 		    	 	form.submit();
				 		    	 	}
				 		    	});
				 		 });

						 // Delete post
						 $(".actionDeletePost").on('click', function(e) {
						    	e.preventDefault();

						  var element = $(this);
						 	var form    = $(element).parents('form');

						 	element.blur();

						 	swal(
						 		{   title: delete_confirm,
						 		  type: "warning",
						      text: delete_confirm_post,
						 		  showLoaderOnConfirm: true,
						 		  showCancelButton: true,
						 		  confirmButtonColor: "#DD6B55",
						 		   confirmButtonText: yes_confirm_reject_post,
						 		   cancelButtonText: cancel_confirm,
						 		    closeOnConfirm: false,
						 		    },
						 		    function(isConfirm){
						 		    	 if (isConfirm) {
						 		    	 	form.submit();
						 		    	 	}
						 		    });
						 		});

								// Refund
	 						 $(".actionRefund").on('click', function(e) {
	 						    	e.preventDefault();

	 						  var element = $(this);
	 						 	var form    = $(element).parents('form');

	 						 	element.blur();

	 						 	swal(
	 						 		{   title: delete_confirm,
	 						 		  type: "error",
	 						 		  showLoaderOnConfirm: true,
	 						 		  showCancelButton: true,
	 						 		  confirmButtonColor: "#DD6B55",
	 						 		   confirmButtonText: yes_confirm_refund,
	 						 		   cancelButtonText: cancel_confirm,
	 						 		    closeOnConfirm: false,
	 						 		    },
	 						 		    function(isConfirm){
	 						 		    	 if (isConfirm) {
	 						 		    	 	form.submit();
	 						 		    	 	}
	 						 		    });
	 						 		});

})(jQuery);


function startTransactionsFiltering(params) {
    $.ajax({
        method: "post",
        url: "/panel/admin/ajax/transactions",
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


function transactionFilterTable(response) {
    let dataTable = $(document).find("table#transaction_table").first();
    let htmlObj = $("<tbody></tbody>");

    console.log(response);
    let dateStart = response.start, dateEnd = response.end, countryData = response.data;

    for(let countryName in countryData) {
        let data = countryData[countryName];
        let invoiceString = `https://app.mator.io/panel/admin/invoices/${dateStart}/${dateEnd}/` + (countryName === "total" ? "total" : `${data.country_code}`);

        htmlObj.append(
            "<tr>" +
            "<td>" + countryName + "</td>" +
            "<td>" + numberFormatting(data.net_sales, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
            "<td>" + numberFormatting(data.net_sales_excl_vat, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
            "<td>" + data.vat_percentage + "%</td>" +
            "<td>" + numberFormatting(data.vat, {style: "decimal", maximumFractionDigits: 2, minimumFractionDigits: 2}) + "</td>" +
            "<td>" + shortNumbByT(data.total_sales) + "</td>" +
            "<td>" +
             "<a href='" + invoiceString + "' target='_blank'>Open invoice</a>" +
            "</td>" +
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
        let totalAmountInclVat = 0, totalAmountExclVat = 0, TotalVatAmount = 0;
        if(Object.keys(items).length)
            // totalAmountInclVat = parseFloat((Object.keys(items).reduce(function (total, i) {
            //     return total + items[i].amount;
            // }, totalAmountInclVat).toFixed(2)))

            if(Object.keys(items).length) {
                for(let i in items) {
                    let item = items[i];
                    let itemAmountExclVat = parseFloat(item.amount);
                    let itemAmountInclVat = itemAmountExclVat;
                    let itemAmountVat = 0;

                    if(item.tax_details !== null) {
                        let taxPercentage = parseFloat(item.tax_details.percentage);
                        itemAmountInclVat = parseFloat((itemAmountExclVat * ((1 + (taxPercentage / 100)))).toFixed(2))
                        itemAmountVat = itemAmountInclVat - itemAmountExclVat;
                    }

                    TotalVatAmount += itemAmountVat;
                    totalAmountInclVat += itemAmountInclVat;
                    totalAmountExclVat += itemAmountExclVat;
                }
            }

        let graphDateTime = new Date(entryDate).getTime();
        chartData.total_sales.data.push(
            {
                x: graphDateTime,
                y: Object.keys(items).length
            }
        );
        chartData.net_sales.data.push(
            {
                x: graphDateTime,
                y: totalAmountInclVat
            }
        );
        chartData.net_sales_excl_vat.data.push(
            {
                x: graphDateTime,
                y: totalAmountExclVat
            }
        );
        chartData.vat.data.push(
            {
                x: graphDateTime,
                y: TotalVatAmount
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


if(typeof setDateRangePicker == "function") {
    setDateRangePicker();
}
