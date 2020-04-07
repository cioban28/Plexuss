$(document).ready(function() {
	Plex.agencyReporting = {
	};

	Plex.agencyReporting.getLastTwelveMonthsReporting = function() {
		$('.export-report-btn').removeClass('active');
		$.ajax({
			url: '/agency/ajax/getDashboardReportingTwo/12',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

		}).done(function(data) {
			var report_data = JSON.parse(data);
			// Year not required from call.
			delete report_data.year;

			for (var month in report_data) {
				if (month.includes('pacing')) continue;

				Plex.agencyReporting.appendToReportingTable(month, report_data);
			}

			Plex.agencyReporting.attachGoalValues(report_data);
			
			$('.export-report-btn').addClass('active');
			$('.loader-spinner').fadeOut(200);
		})
	}

	Plex.agencyReporting.attachGoalValues = function(data) {
		$('.monthly-goals .goal-value[data-type="applications"]').hide().html(data.application_pacing).fadeIn(200);
		// $('.monthly-goals .goal-value[data-type="accepted"]').hide().html(data.opportunity_pacing).fadeIn(200);
		$('.monthly-goals .goal-value[data-type="enrolled"]').hide().html(data.enrolled_pacing).fadeIn(200);
	}

	Plex.agencyReporting.appendToReportingTable = function(month, data) {
		var table = $('.reporting-wrapper table'),
			applications = Plex.agencyReporting.determineEntryColor(data[month].applications, data.application_pacing),
			opportunities = Plex.agencyReporting.determineEntryColor(data[month].opportunities, data.opportunity_pacing),
			enrolled = Plex.agencyReporting.determineEntryColor(data[month].enrolled, data.enrolled_pacing);

		// Order matters here. Month | Completed Apps | Accepted | Enrolled | Removed
		var tableRow = 
					'<tr>' +
						'<td>' + month + '</td>' +
						'<td>' + applications + '</td>' +
						'<td>' + opportunities + '</td>' +
						'<td>' + enrolled + '</td>' +
						'<td class="removed">' + data[month].removed + '</td>' +
						'<td></td>' + // Leave empty
					'</tr>';

		table.append(tableRow);

	}

	// If value is less than goal, we colorcode it red. Otherwise
	Plex.agencyReporting.determineEntryColor = function(value, goal) {
		var color = value < goal ? 'red' : 'green';
		return '<span class="' + color + '">' + value + '</span>';
	}

	Plex.agencyReporting.exportTableToCSV = function(filename) {
	    var csv = [],
	    	today = new Date(),
	    	date = (today.getMonth() + 1) + '/' + today.getDate() + '/' + today.getFullYear(),
	    	rows = document.querySelectorAll('.reporting-wrapper table tr');
	    
	    for (var i = 0; i < rows.length; i++) {
	        var row = [], cols = rows[i].querySelectorAll("td, th");
	        for (var j = 0; j < cols.length - 1; j++) 
	            row.push(cols[j].innerText);

	        csv.push(row.join(","));     
	    }

	    csv.push(null, date);

	    Plex.agencyReporting.downloadCSV(csv.join("\n"), filename);
	}

	Plex.agencyReporting.downloadCSV = function(csv, filename) {
	    var csvFile;
	    var downloadLink;

	    csvFile = new Blob([csv], {type: "text/csv"});
	    downloadLink = document.createElement("a");
	    downloadLink.download = filename;
	    downloadLink.href = window.URL.createObjectURL(csvFile);
	    downloadLink.style.display = "none";
	    document.body.appendChild(downloadLink);

	    downloadLink.click();
	}

	$(document).on('click', '.export-report-btn.active', function() {
		Plex.agencyReporting.exportTableToCSV('plexuss_report_last_12_months.csv');
	});

	Plex.agencyReporting.getLastTwelveMonthsReporting();
});