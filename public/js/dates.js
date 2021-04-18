var today = new Date();
var day = today.getDate();
var month = today.getMonth() + 1;
var year = today.getFullYear();

if(month < 10)
		monthToDate = '0' + month;
	else
		monthToDate = month;
if(day < 10)
		dayToDate = '0' + day;
	else
		dayToDate = day;
	
var date = year + '-' + monthToDate + '-' + dayToDate;

$('.resetDate').click(setActualDate('expenseDate'));

function setActualDate(divId){
	$('#'+divId).val(date);
}

function howManyDaysInMonth(monthToCheck){
	if(monthToCheck == 2){
		if(year % 4 == 0 &  year % 100 != 0 || year % 400 == 0)
			return 29;
		else
			return 28;
	}
	else if (monthToCheck == 4 || monthToCheck == 6 || monthToCheck == 9 || monthToCheck == 11)
		return 30;
	else
		return 31;
}

function createPeriodDates(){
	
	if( $( '#period option:selected' ).text() == "Bieżący miesiąc"){
		$('#startDate').val(year + '-' + monthToDate + '-01' );
		$('#endDate').val(year + '-' + monthToDate + '-' + howManyDaysInMonth(month));
		$('#startDate').prop('readonly', true);
		$('#endDate').prop('readonly', true);
		$('.inputIcon').html('<i class="icon-lock"></i>')
	}
	else if( $( '#period option:selected' ).text() == "Bieżący rok"){
		$('#startDate').val(year + '-01-01' );
		$('#endDate').val(year + '-12-31');
		$('#startDate').prop('readonly', true);
		$('#endDate').prop('readonly', true);
		$('.inputIcon').html('<i class="icon-lock"></i>')
	}
	else if( $( '#period option:selected' ).text() == "Poprzedni miesiąc"){
		var monthToCheck = month - 1;
		var yearToDate = year;
		if(monthToCheck == 0){
			monthToCheck = 12;
			yearToDate--;
		}
		var checkedMonthDays = howManyDaysInMonth(monthToCheck);
		if(monthToCheck < 10)
			monthToCheck = '0' + monthToCheck;
		$('#startDate').val(yearToDate + '-' + monthToCheck + '-01' );
		$('#endDate').val(yearToDate + '-' + monthToCheck + '-' + checkedMonthDays);
		$('#startDate').prop('readonly', true);
		$('#endDate').prop('readonly', true);
		$('.inputIcon').html('<i class="icon-lock"></i>')
	}
	else{
		$('#startDate').val('');
		$('#endDate').val('');
		$('#startDate').prop('readonly', false);
		$('#endDate').prop('readonly', false);
		$('.inputIcon').html('<i class="icon-lock-open"></i>')
	}
		
}

