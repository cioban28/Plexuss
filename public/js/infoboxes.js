Plex.searchRequest = null;
function getAvgDoughnutBox(bgcolor, headPercent, headCourse, content, boxfor, divID, minScore, maxScore) {
    $.ajax({
        type: "POST",
        url: '/infoavgboxdoughnut',
        data: ({
            bgcolor: bgcolor,
            headPercent: headPercent,
            headCourse: headCourse,
            content: content,
            boxfor: boxfor,
            minScore: minScore,
            maxScore: maxScore
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getRankingBox(topcolcor, toptitle, subheadcolor, midcolor, boxfor, divID, expandID,rankData) {
	var rankid = JSON.parse(rankData);
	rankid = $(rankid).get(0);
	
	$.ajax({
		type: "POST",
		url: '/infobox',
		async: true,
		data: ({
			topcolcor: topcolcor,
			toptitle: toptitle,
			subheadcolor: subheadcolor,
			midcolor: midcolor,
			boxfor: boxfor,
			expandID: expandID,
			rankData: rankData,
		}),
		cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {
			$('#' + divID).html(data);
			setResizeBox();
			getRankingBoxData(rankid.id,'1');
		}
	});}
	
function getRankingBoxData(rankid,expandID) {
	
	$.ajax({
		type: "POST",
		url: '/infoboxrankdata',
		async: true,
		data: ({
			rankid: rankid,expandID:expandID
		}),
		cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {
			 
			$("#rankingBoxTab").html(data);
			 setResizeBox();
		}
	});
}	

function getRankingBox1(topcolcor, toptitle, subheadcolor, midcolor, boxfor, divID, expandID,rankData) {
	var rankid = JSON.parse(rankData);
	rankid = $(rankid).get(0);
	
	$.ajax({
		type: "POST",
		url: '/infobox',
		async: true,
		data: ({
			topcolcor: topcolcor,
			toptitle: toptitle,
			subheadcolor: subheadcolor,
			midcolor: midcolor,
			boxfor: boxfor,
			expandID: expandID,
			rankData: rankData,
		}),
		cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {
			$('#' + divID).html(data);
			setResizeBox();
			getRankingBoxData1(rankid.id,'2');			
		}
	});}
	
function getRankingBoxData1(rankid,expandID) {
	
	$.ajax({
		type: "POST",
		url: '/infoboxrankdata',
		async: true,
		data: ({
			rankid: rankid,expandID:expandID
		}),
		cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {
			 
			$("#conferenceboxBoxTab").html(data);
			 setResizeBox();
		}
	});
}	

function expandDiv(expandID) {
    $('#expanddiv' + expandID).slideToggle(500, function() {
        $('#expand-toggle' + expandID).toggleClass("run");
        setResizeBox();
    });
}


function expandDivContent(expandID,expandDiv) {	

    $('#'+expandDiv).slideToggle(500, function() {
        $('#'+expandID).toggleClass("run");       
    });
}


/*masonry function*/
function setResizeBox() {
    //console.log(" setResizeBox in infoboxs called");
    imagesLoaded( '#container-box', function() {
        $('#container-box').masonry({
            itemSelector: '.box-div'
        });
    });
};




function filterCollege(name) {

	if(Plex.searchRequest != null) {
		console.log('CRASH');
        Plex.searchRequest.abort();
        Plex.searchRequest = null;
    }

    $('#ajaxloader-div').show();
    Plex.searchRequest = $.ajax({
        type: "POST",
        url: '/letterfilter',
        async: true,
        data: ({
            name: name
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
        	$('#ajaxloader-div').hide();
        	Plex.searchRequest = null;
        	if(data == ''){
        		$('#letterFilterd').html('<li class="notFound">No schools with that name found.</li>');
        	} else {
            	$('#letterFilterd').html(data);
        	}
        }
    });
}



/* Dom Filter Rows */
function filterRows(filterString) {


    // For each row, that is, div in #rows of class .row
    $.each($('#letterFilterd .li-filterdata'), function(i, row) {
        $('#ajaxloader-div').show();
        var $row = $(row); // Get the row div element
        var rowData = $row.data(); // Get the data associated with the row div element
        var id = rowData.id; // Get the row id
        var type = rowData.type;
        if (filterString.length == 0) {
            $row.show();
        } else {
            if (type.toLowerCase().search("rowheader") != -1) {
                $row.show();
            } else {

                if (

                    filterByType(filterString, rowData.type)
                ) {
                    $row.show();
                } else {
                    $row.hide();
                }
            }

        }

    });
}

/* Filter Rows by Type */
function filterByType(filterString, type) {
    $('#ajaxloader-div').hide();
    var p1 = RegExp('^' + filterString);
    if (p1.test(type)) {
        return true;
    } else {
        return false;
    }
}


function getGraphScoresBox(bgcolor, shareBtn, pecrcentage, scoreType, satpercent, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxgraphscores',
        data: ({
            bgcolor: bgcolor,
            shareBtn: shareBtn,
            pecrcentage: pecrcentage,
            scoreType: scoreType,
            satpercent: satpercent,
            boxfor: boxfor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getDirectoryBox(topcolcor, midcoclor, bottomcolor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxdirectory',
        data: ({
            topcolcor: topcolcor,
            midcoclor: midcoclor,
            bottomcolor: bottomcolor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);

            $('.bxslider').bxSlider({
                minSlides: 1,
                maxSlides: 15,
                slideWidth: 18,
                slideMargin: 5,
                pager: false,
                moveSlides: 1,

            });
        }
    });
}

function getTotalRankingBoxes(headbgcolor, headbgtext, ranknumber, collegelogo, collegename, collegelink, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxtotalranking',
        data: ({
            headbgcolor: headbgcolor,
            headbgtext: headbgtext,
            ranknumber: ranknumber,
            collegelogo: collegelogo,
            collegename: collegename,
            collegelink: collegelink,
            boxfor: boxfor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).append(data);
            setResizeBox();
        }
    });
}

function getTotalValueBoxes(headbgcolor, headbgtext, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxtotalvalue',
        data: ({
            headbgcolor: headbgcolor,
            headbgtext: headbgtext,
            boxfor: boxfor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).append(data);
            setResizeBox();
        }
    });
}

function getGraduationRateBoxes(headbgtext, footPercent, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxgradrate',
        data: ({
            headbgtext: headbgtext,
            footPercent: footPercent,
            boxfor: boxfor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).append(data);
            setResizeBox();
        }
    });
}

function getGenderComparisonBox(headingSmall, headingBig, menContent, womenContent, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxcomparison',
        data: ({
            headingSmall: headingSmall,
            headingBig: headingBig,
            menContent: menContent,
            womenContent: womenContent,
            boxfor: boxfor,
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).append(data);
            setResizeBox();
        }
    });
}

function getLearningSkillsBoxes(header, title, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxlearnskills',
        data: ({
            header: header,
            title: title,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getPopularSalaryBoxes(headImage, headerbg, title, contentbgColor, evenbgColor, sat_read_25, sat_read_75, sat_math_25, sat_math_75, sat_write_25, sat_write_75, act_composite_25, act_composite_75, act_english_25, act_english_75, act_math_25, act_math_75, act_write_25, act_write_75, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxsalarybox',
        data: ({
            headImage: headImage,
            headerbg: headerbg,
            title: title,
            contentbgColor: contentbgColor,
            evenbgColor: evenbgColor,
            sat_read_25: sat_read_25,
            sat_read_75: sat_read_75,
            sat_math_25: sat_math_25,
            sat_math_75: sat_math_75,
            sat_write_25: sat_write_25,
            sat_write_75: sat_write_75,
            act_composite_25: act_composite_25,
            act_composite_75: act_composite_75,
            act_english_25: act_english_25,
            act_english_75: act_english_75,
            act_math_25: act_math_25,
            act_math_75: act_math_75,
            act_write_25: act_write_25,
            act_write_75: act_write_75,
            boxfor: boxfor,
            divID: divID
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getConsideredAdmissionBoxes(headImage, headerbg, title, contentbgColor, evenbgColor, content, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxconsideradmissionbox',
        data: ({
            headImage: headImage,
            headerbg: headerbg,
            title: title,
            contentbgColor: contentbgColor,
            evenbgColor: evenbgColor,
            content: content,
            boxfor: boxfor,
            divID: divID
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getAppInfoBox(bgColor, title, firstAns, secondAns, thirdAns, webLink, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoappinfobox',
        data: ({
            bgColor: bgColor,
            title: title,
            firstAns: firstAns,
            secondAns: secondAns,
            thirdAns: thirdAns,
            webLink: webLink,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}

function getNotablesBox(headImg, title, name, dob, classYear, speciality, majors, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infonotablesbox',
        data: ({
            headImg: headImg,
            title: title,
            name: name,
            dob: dob,
            classYear: classYear,
            speciality: speciality,
            majors: majors,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getTuitionBox(headImg, title, icon, InStateTitleColor, OutStateTitleColor, InTuitionValue, InBooksValue, InRoomValue, InOtherValue, OutTuitionValue, OutBooksValue, OutRoomValue, OutOtherValue, totalInExpenseColor, inExpenseValue, totalOutExpenseColor, outExpenseValue, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infotuitionbox',
        data: ({
            headImg: headImg,
            title: title,
            icon: icon,
            InStateTitleColor: InStateTitleColor,
            OutStateTitleColor: OutStateTitleColor,
            InTuitionValue: InTuitionValue,
            InBooksValue: InBooksValue,
            InRoomValue: InRoomValue,
            InOtherValue: InOtherValue,
            OutTuitionValue: OutTuitionValue,
            OutBooksValue: OutBooksValue,
            OutRoomValue: OutRoomValue,
            OutOtherValue: OutOtherValue,
            totalInExpenseColor: totalInExpenseColor,
            inExpenseValue: inExpenseValue,
            totalOutExpenseColor: totalOutExpenseColor,
            outExpenseValue: outExpenseValue,
            boxfor: boxfor,
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getCalculatorBox(bgColor, headTitle, headImage, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infocalculatorbox',
        data: ({
            bgColor: bgColor,
            headTitle: headTitle,
            headImage: headImage,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}


function getLoanRateBox(bgColor, headBgColor, headTitle1, headTitle2, contentCrossColor, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoloanratebox',
        data: ({
            bgColor: bgColor,
            headBgColor: headBgColor,
            headTitle1: headTitle1,
            headTitle2: headTitle2,
            contentCrossColor: contentCrossColor,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getCampusDineBox(bgImage, headTitle, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infocampusdinebox',
        data: ({
            bgImage: bgImage,
            headTitle: headTitle,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getBigListBox(bgImage, headTitle, headTitle2, ListContent, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infobiglistbox',
        data: ({
            bgImage: bgImage,
            headTitle: headTitle,
            headTitle2: headTitle2,
            ListContent: ListContent,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getWeatherBox(bgColor, currPlace, currTemp, WeatherImage, WeatherType, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoweatherbox',
        data: ({
            bgColor: bgColor,
            currPlace: currPlace,
            currTemp: currTemp,
            WeatherImage: WeatherImage,
            WeatherType: WeatherType,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getCollegeSportsBox(topImage, topTitleSub, topTitleMain, basemen, basewomen, basketmen, basketwomen, all_track_men, all_track_women, cross_men, cross_women, football_men, football_women, golf_men, golf_women, all_track_men, all_track_women, gymnastics_men, gymnastics_women, rowing_men, rowing_women, soccer_men, soccer_women, softball_men, softball_women, swimming_men, swimming_women, tennis_men, tennis_women, volleyball_men, volleyball_women, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infocollegesportsbox',
        data: ({
            topImage: topImage,
            topTitleSub: topTitleSub,
            topTitleMain: topTitleMain,

            basemen: basemen,
            basewomen: basewomen,
            basketmen: basketmen,
            basketwomen: basketwomen,
            all_track_men: all_track_men,
            all_track_women: all_track_women,
            cross_men: cross_men,
            cross_women: cross_women,
            football_men: football_men,
            football_women: football_women,
            golf_men: golf_men,
            golf_women: golf_women,
            all_track_men: all_track_men,
            all_track_women: all_track_women,
            gymnastics_men: gymnastics_men,
            gymnastics_women: gymnastics_women,
            rowing_men: rowing_men,
            rowing_women: rowing_women,
            soccer_men: soccer_men,
            soccer_women: soccer_women,
            softball_men: softball_men,
            softball_women: softball_women,
            swimming_men: swimming_men,
            swimming_women: swimming_women,
            tennis_men: tennis_men,
            tennis_women: tennis_women,
            volleyball_men: volleyball_men,
            volleyball_women: volleyball_women,


            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}


function getCollegeExpensesBox(bgColor, title, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infocollegeexpensebox',
        data: ({
            bgColor: bgColor,
            title: title,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getUgEthnicBox(headImg, subTitle, headTitle, aianpercent, asiapercent, bkaapercent, hisppercent, nhpipercent, whitpercent, twomorepercent, unknpercent, nralpercent, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infocollegeugethnicbox',
        data: ({
            headImg: headImg,
            subTitle: subTitle,
            headTitle: headTitle,
            aianpercent: aianpercent,
            asiapercent: asiapercent,
            bkaapercent: bkaapercent,
            hisppercent: hisppercent,
            nhpipercent: nhpipercent,
            whitpercent: whitpercent,
            twomorepercent: twomorepercent,
            unknpercent: unknpercent,
            nralpercent: nralpercent,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}


function getEnrollNutBox(headBg, smallHeadTitle, bigHeadTitle, headText, graphImage, footText, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoenrollnutbox',
        data: ({
            headBg: headBg,
            smallHeadTitle: smallHeadTitle,
            bigHeadTitle: bigHeadTitle,
            headText: headText,
            graphImage: graphImage,
            footText: footText,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getThreeWayNutBox(sideImage, midGraph, rightContentOne, rightContentTwo, rightContentThree, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infothreewaynutbox',
        data: ({
            sideImage: sideImage,
            midGraph: midGraph,
            rightContentOne: rightContentOne,
            rightContentTwo: rightContentTwo,
            rightContentThree: rightContentThree,
            boxfor: boxfor
        }),

        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    })
}

function getMajorProgramBox(bgColor, title, subTitle, progContent, progLink, progIcon, boxfor, divID) {
    $.ajax({
        type: "POST",
        url: '/infoboxmajorprograms',
        data: ({
            bgColor: bgColor,
            title: title,
            subTitle: subTitle,
            progContent: progContent,
            progLink: progLink,
            progIcon: progIcon,
            boxfor: boxfor
        }),
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            $('#' + divID).html(data);
            setResizeBox();
        }
    });
}