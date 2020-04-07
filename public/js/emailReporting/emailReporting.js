
	$('#fetchToday').on('click', function(event) {
	    event.preventDefault();
	    document.getElementById("fetchToday").style.border = "2px solid #4caf50";
	    document.getElementById("fetchMonth").style.border = "2px solid grey";
	    document.getElementById("fetchYesterday").style.border = "2px solid grey";
	    var today = new Date();
	    var dd = today.getDate();
	    var mm = today.getMonth() + 1; //January is 0!
	    var yyyy = today.getFullYear();
	    if (dd < 10) {
	        dd = '0' + dd;
	    }
	    if (mm < 10) {
	        mm = '0' + mm;
	    }
	    start = yyyy + '-' + mm + '-' + dd;
	    end = start;
	    fetch(start, end);
	});

	$('#fetchYesterday').on('click', function(event) {
	    event.preventDefault();
	    document.getElementById("fetchYesterday").style.border = "2px solid #4caf50";
	    document.getElementById("fetchMonth").style.border = "2px solid grey";
	    document.getElementById("fetchToday").style.border = "2px solid grey";
	    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
	    var dd = yesterday.getDate();
	    var mm = yesterday.getMonth() + 1; //January is 0!
	    var yyyy = yesterday.getFullYear();
	    if (dd < 10) {
	        dd = '0' + dd;
	    }
	    if (mm < 10) {
	        mm = '0' + mm;
	    }
	    start = yyyy + '-' + mm + '-' + dd;
	    end = start;
	    fetch(start, end);
	});

	$('#fetchMonth').on('click', function(event) {
	    event.preventDefault();
	    document.getElementById("fetchYesterday").style.border = "2px solid grey";
	    document.getElementById("fetchMonth").style.border = "2px solid #4caf50";
	    document.getElementById("fetchToday").style.border = "2px solid grey";
	    end = '0';
	    var today = new Date();
	    var mm = today.getMonth() + 1; //January is 0!
	    var yyyy = today.getFullYear();
	    if (mm < 10) {
	        mm = '0' + mm;
	    }
	    start = yyyy + '-' + mm + '-' + '01';
	    fetch(start, end);
	});

	$('#email_template_fetch').on('click', function(event) {
	    event.preventDefault();
	    document.getElementById("fetchYesterday").style.border = "2px solid grey";
	    document.getElementById("fetchMonth").style.border = "2px solid grey";
	    document.getElementById("fetchToday").style.border = "2px solid grey";
	    // console.log(event.target.id);
	    var start = $('#start_date').val();
	    var end = $('#end_date').val();
	    if (start == "") {
	        start = '0';
	    }
	    if (end == "") {
	        end = '0';
	    }
	    fetch(start, end);
	});

	function fetch(start, end) {
	    $("#email_templates").html("<tr><td colspan='13' style='text-align:center;'> Loading... </td></tr>");
	    $("#email_templates_foot").html("<tr><td>Template Name</td><td>Category</td><td>Provider</td><td>Sent</td><td>Open</td><td>Clicks</td><td>OR%</td><td>CR%</td><td>Complete</td><td>1</td><td>5</td><td>P</td><td>$</td></tr>");
	    
	    $.ajax({
	        url: "emailTemplate/" + start + "/" + end,
	        dataType: "json",
	        success: function(result) {
	            if (result == "From Date must be less than End Date") {
	                $("#email_templates").html("<tr><td colspan='13' style='text-align:center;'> From Date must be less than End Date </td></tr>");
	            } else {
	                $("#email_templates").empty();
	                var tot = 0;
	                var op = 0;
	                var clk = 0;
	                var conv = 0;
	                var total_open_rate = 0;
	                var total_click_rate = 0;
	                var sumComplete = 0;
	                var sumSelected_1_4 = 0;
	                var sumSelected_5_more = 0;
	                var sumPremium = 0;
	                var sumConversion = 0;

	                $.each(result, function(index, value) {
	                  var row = $("<tr><td style='text-align:left'><a id='" + value.template + "' onclick='tempHTML(this.id);'>" + value.template + "</a></td><td>"+selectOption(value.category, value.template, value.cat)+"</td><td>" + value.provider + "</td><td>" + (value.total).toLocaleString() + "</td><td>" + (value.open).toLocaleString() + "</td><td>" + (value.click).toLocaleString() + "</td><td>" + (value.open_rate).toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + (value.click_rate).toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + (value.complete).toLocaleString() + "</td><td>" + (value.selected_1_4).toLocaleString() + "</td><td>" + (value.selected_5_more).toLocaleString() + "</td><td>" + (value.premium).toLocaleString() + "</td><td>"+ (value.conversion).toLocaleString(undefined, { maximumFractionDigits: 2 }) +"</td></tr>");  
	                    $("#email_templates").append(row);
	                    tot = tot + value.total;
	                    op = op + value.open;
	                    clk = clk + value.click;
	                    conv = conv + parseInt(value.conversion);
	                    sumComplete = sumComplete + parseInt(value.complete);
	                    sumSelected_1_4 = sumSelected_1_4 + parseInt(value.selected_1_4);
		                sumSelected_5_more = sumSelected_5_more + parseInt(value.selected_5_more);
		                sumPremium = sumPremium + parseInt(value.premium);
		                sumConversion = sumConversion + parseInt(value.conversion);
	                });
	                total_open_rate = (op / tot) * 100;
	                total_click_rate = (clk / op) * 100;
	                $("#email_templates_foot").html("<tr><td>Template Name</td><td>Category</td><td>Provider</td><td>" + tot.toLocaleString() + "</td><td>" + op.toLocaleString() + "</td><td>" + clk.toLocaleString() + "</td><td>" + total_open_rate.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + total_click_rate.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + sumComplete.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + sumSelected_1_4.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + sumSelected_5_more.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + sumPremium.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td><td>" + sumConversion.toLocaleString(undefined, { maximumFractionDigits: 2 }) + "</td></tr>");
	                $('.js-select').select2();
	            }
	        },
	        error: function(result) {
	            
	            $("#email_templates").html("<tr><td colspan='13' style='text-align:center;'> Check the from and to column. From must be before than To or input error </td></tr>");
	        }
	    });
	}

	function selectOption(category, template, cat){
		var selectElement = '';
		selectElement += '<select class="js-select" name="category" onchange="category_modification(this.value,\''+template+'\');">';
		$.each(cat, function(index, value) {
			selectElement += '<option value="'+ value.id +'" '+selectCondition(category, value.category)+' >'+value.category+'</option>'; 
		});
		selectElement += '</select>';
		return selectElement;
	}

	function selectCondition(category, compareWith){
		if(category == compareWith)
		{
			return 'selected';
		}
		return '';
	}

	function category_modification(category_id, template){
		$.ajax({
				url: "category_modify",
				type:"POST",
				data: {category_id : category_id,template: template},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(result) {
            console.log(result);
        },
        error: function(result) {
          	console.log(result);
        }
		});
	}

	$(document).ready(function() {
			$('.js-select').select2();

	    function exportTableToCSV($table, filename) {
	        var $headers = $table.find('tr:has(th)'),
	            $rows = $table.find('tr:has(td)')
	            // Temporary delimiter characters unlikely to be typed by keyboard
	            // This is to avoid accidentally splitting the actual contents
	            ,
	            tmpColDelim = String.fromCharCode(11) // vertical tab character
	            ,
	            tmpRowDelim = String.fromCharCode(0) // null character
	            // actual delimiter characters for CSV format
	            ,
	            colDelim = '","',
	            rowDelim = '"\r\n"';
	        // Grab text from table into CSV formatted string
	        var csv = '"';
	        csv += formatRows($headers.map(grabRow));
	        csv += rowDelim;
	        csv += formatRows($rows.map(grabRow)) + '"';
	        // Data URI
	        var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
	        $(this)
	            .attr({
	                'download': filename,
	                'href': csvData
	                //,'target' : '_blank' //if you want it to open in a new window
	            });
	        //------------------------------------------------------------
	        // Helper Functions 
	        //------------------------------------------------------------
	        // Format the output so it has the appropriate delimiters
	        function formatRows(rows) {
	            return rows.get().join(tmpRowDelim)
	                .split(tmpRowDelim).join(rowDelim)
	                .split(tmpColDelim).join(colDelim);
	        }
	        // Grab and format a row from the table
	        function grabRow(i, row) {
	            var $row = $(row);
	            //for some reason $cols = $row.find('td') || $row.find('th') won't work...
	            var $cols = $row.find('td');
	            if (!$cols.length) $cols = $row.find('th');
	            return $cols.map(grabCol).get().join(tmpColDelim);
	        }
	        // Grab and format a column from the table 
	        function grabCol(j, col) {
	            var $col = $(col),
	                $text = $col.text();
	            return $text.replace('"', '""'); // escape double quotes
	        }
	    }

	    $("#export").click(function(event) {
	        // var outputFile = 'export'
	        // var outputFile = window.prompt("What do you want to name your output file (Note: This won't have any effect on Safari)") || 'export';
	        var outputFile = 'EmailReporting';
	        outputFile = outputFile.replace('.csv', '') + '.csv'

	        // CSV
	        exportTableToCSV.apply(this, [$('#tableDiv>table'), outputFile]);

	        // IF CSV, don't do event.preventDefault() or return false
	        // We actually need this to be a typical hyperlink
	    });
	});

	// Get the modal
	var modal = document.getElementById('myModal');
		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];
		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
		    modal.style.display = "none";
		    document.getElementById("modal-content").innerHTML = "";
		    document.getElementById("title").innerHTML = "";
		    document.getElementById("from_email").innerHTML = "";
		    document.getElementById("email_div").style.display = "none";
		}
		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
		    if (event.target == modal) {
		        modal.style.display = "none";
		    }
	}
	var template;
	function tempHTML(id) {
	    //console.log(id);
	    template=id;
	    var modal = document.getElementById("myModal");
	    modal.style.display = "block";
	    document.getElementById("title").innerHTML = id;
	    document.getElementById("modal-content").innerHTML = "<p>Template HTML is loading...</p>";
	    if (id.length == 0) {
	        document.getElementById("modal-content").innerHTML = "No template HTML found";
	        return;
	    } else {
	        $.ajax({
	            url: "templateHtml/" + id,
	            success: function(result) {
	            		document.getElementById("email_div").style.display="block";
	                document.getElementById("title").innerHTML = result.subject;
	                document.getElementById("from_email").innerHTML = "<br><small>From:" + result.from.email + "</small>";
	                document.getElementById("modal-content").innerHTML = result.html;
	            },
	            error: function(result) {
	                document.getElementById("modal-content").innerHTML = "Resource Not Found";
	            }
	        });
	    }
	}

	//For Fixed Header And Keeping Width As It Is
	var sticky = $("#myHeader").offset().top;
	var header = document.getElementById('myHeader');
	// console.log($("table#example thead").attr("id"));

	var width = [];
	$("table#example thead tr").each(function () {
		var j=0;
		$(this).children('th').each(function() {
		  $(this).addClass("tdfix"+j);
		  j=j+1;
		}); 
	})

	$(window).on('scroll', function() {
	    $("table#example thead tr").each(function() {
	        var j = 0;
	        $(this).children('th').each(function() {
	            width[j] = $(".tdfix" + j).outerWidth();
	            j = j + 1;
	        });
	    })
	    if (window.pageYOffset > sticky) {
	        header.classList.add("sticky");

	        $("table#example tbody tr").each(function() {
	            var j = 0;
	            $(this).children('td').each(function() {
	                $(this).addClass("td" + j);
	                j = j + 1;
	            });
	        })

	        for (var i = 0; i < width.length; i++) {
	            $(".tdfix" + i).css('width', width[i]);
	            $(".td" + i).css('width', width[i]);
	        }
	    } else {
	        header.classList.remove("sticky");
	    }
	});

	// var $rows = $('#email_templates tr');
	$('#searchTemplate').keyup(function() {
	    var $rows = $('#email_templates tr');
	    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

	    $rows.show().filter(function() {
	        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
	        return !~text.indexOf(val);
	    }).hide();
	});

	function category_filter(category){
		// console.log(category);
		if(category != ''){
			// var $rows = $('#email_templates tr');
	  //   var val = $.trim(category);//.replace(/ +/g, ' ').toLowerCase();
	  //   console.log('Val = '+ val);
	  //   $rows.show().filter(function() {
	  //       var text = $('.js-select option:selected').text().replace(/\s+/g, ' ').toLowerCase();
	  //       console.log(text);
	  //       console.log(text.indexOf(val));
	  //       return !~text.indexOf(val);
	  //   }).hide();

	    // $("#gdRows tr td").each(function() {
     //    	var cellText = $.trim($(this).text());
	    //     if (cellText.length == 0) {
	    //         $(this).parent().hide();
	    //     }
	    // });

				var ajax = true;
		    $.ajax({
		        url: "category_modify/" + category + "/" + ajax,
		        dataType: "json",
		        success: function(result) {
		        	console.log(result);
		        // 		$("#email_templates tr td:nth-child(1)").each(function() {
					     //    	var cellText = $.trim($(this).text());
					     //    	console.log(cellText);
						    //     if (result.indexOf(cellText) == (-1)) {
						    //         $(this).parent().hide();
						    //     }
						    //     else{
						    //     	$(this).hide();
						    //     	console.log('In Array');
						    //     }
						    // });
		        },
		        error: function(result) {
		            // $("#email_templates").html("<tr><td colspan='13' style='text-align:center;'> Error in Fetching Data </td></tr>");
		        }
		    });
			}
			else{
				var $rows = $('#email_templates tr');
		    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

		    $rows.show().filter(function() {
		        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
		        return !~text.indexOf(val);
		    }).hide();
			}
	}

	$('#send_test').click(function(event){
		event.preventDefault();
		var emailList = $('#test_email').val();
		if(emailList == ''){
			$("#test_email").css({"box-shadow": "0px 1px 6px #971919","border":"1px solid #00C273"});
			$("#test_email").attr("placeholder","Please enter emails");
	    $("#test_email").focus();
		}
		else{		
			emailList = emailList.replace(/(^,)|(,$)/g, "");
			if(validateEmailList(emailList)){
				$('#error_msg').html('<p id="error_msg" style="text-align: left"></p>');
				$("#test_email").css({"box-shadow": "0px 1px 6px #00C273","border":"1px solid #d7d8d8"});
				var list = emailList.split(",").map(function(item) {
				  return item.trim();
				});
				$('#email_input').css('display','none'); 
	       document.getElementById("email_message").innerHTML = "Sending Email...";
				$.ajax({
	        url: "sendMail/"+template+"/"+list,
	        success: function(result) {	
	        	document.getElementById("email_message").innerHTML = "Sent successfully to:";
	        	$('#error_msg').css({'display':'block','color':'#0e0e0e'});
	        	document.getElementById("error_msg").innerHTML = list;  
	        	   		  
	         	setTimeout(function() {
					     $("#email_message").html('<p id="email_message" style="font-size: 14px;color: #00C273;margin: 0px;padding-bottom: 4px; text-align: -webkit-left;">Send a test to:</p>');
					     $('#error_msg').fadeOut('slowfast');		
					     $('#email_input').css('display','block');
					  	 $('#test_email').val('');	
					  	 $('#test_email').css({"box-shadow": "0 0 2px #d7d8d8","border":"1px solid #d7d8d8"});				  	 	    
					  }, 5000);	
					  
	        },
	        error: function(result) {
	        	$('#error_msg').css({'display':'block','color':'#0e0e0e'});
	        	document.getElementById("error_msg").innerHTML = "Some Error Occured";  
	        	$('#email_input').css('display','none');    		  
	         	setTimeout(function() {
					     $("#email_message").html('<p id="email_message" style="font-size: 14px;color: #00C273;margin: 0px;padding-bottom: 4px; text-align: -webkit-left;">Send a test to:</p>');
					     $('#error_msg').fadeOut('fast');		
					     $('#email_input').css('display','block');
					  	 $('#test_email').val('');
					  	 $("#test_email").css({"box-shadow": "0px 1px 6px #971919","border":"1px solid #00C273"});	
					  	 $('#test_email').css({"box-shadow": "0 0 2px #d7d8d8","border":"1px solid #d7d8d8"});
					  }, 5000);	
	        }
	    });
			}
			else{
				$('#error_msg').css('display','block');
				$('#error_msg').html('Enter correct emails');
				$("#test_email").css({"box-shadow": "0px 1px 6px #971919","border":"1px solid #00C273"});
		    $("#test_email").focus();
				$('#error_msg').css('color','red');
			}
			
		}
	});

	function validateEmailList(raw){
    var emails = raw.split(',');
    var valid = true;
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    for (var i = 0; i < emails.length; i++) {
        if( emails[i] === "" || !regex.test(emails[i].replace(/\s/g, ""))){
            valid = false;
            break;
        }
    }
    return valid;
	}

	$('#test_email').keypress(function (e) {
	  if (e.which == 13) {
	    $('#send_test').click();
	    return false;  
	  }
	});
