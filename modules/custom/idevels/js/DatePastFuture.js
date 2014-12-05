(function($){$(function() {

  // get curent date
  var d = new Date();

  function formatDate(date) {
    var day = date.getDate().toString();
    var month = date.getMonth().toString();
    var year = date.getFullYear().toString();
    day = day.length < 2 ? "0" + day : day;
    month = month.length < 2 ? "0" + month : month;
    return year + "-"+ month +"-"+ day
  }

  function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
  }

  function validateDays(month, year, $datediv, pastOrFuture) {
    $daySelect = $datediv.find("select[title=Day]");
    $daySelectOptions = $datediv.find("select[title=Day] option");
    $dayDefaultOption = $datediv.find("select[title=Day] option:first");
    // If future Day was selected - select first option
    if (month == d.getMonth()+1 && year == d.getFullYear()) {
      if ($daySelect.val() > d.getDate()) {
        $daySelect.val($dayDefaultOption.val());
      };
      $daySelectOptions.each(function(){
        if (parseInt($(this).val()) > d.getDate()) {
          $(this).hide();
        };
      });
    }
    // If user select not future Month - show all Days
    else {
      if ($daySelect.val() > daysInMonth(month, year)) {
        $daySelect.val($dayDefaultOption.val());
      };
      $daySelectOptions.each(function(){
        console.log(parseInt($(this).val()));
        if (parseInt($(this).val()) <= daysInMonth(month, year)) {
          $(this).show();
        }
        else {
          $(this).hide();
        }
      });
    };
  }

  function validateMonth(month, year, $datediv, pastOrFuture) {
    $monthSelect = $datediv.find("select[title=Month]");
    $monthSelectOptions = $datediv.find("select[title=Month] option");
    $monthDefaultOption = $datediv.find("select[title=Month] option:first");
    if (year == d.getFullYear()) {
      // If future Month was selected - select first option
      if ($monthSelect.val() > d.getMonth()+1) {
        $monthSelect.val(monthDefaultOption.val());
      };
      $monthSelectOptions.each(function(){
        if (parseInt($(this).val()) > d.getMonth()+1) {
          $(this).hide();
        }
        else {
          $(this).show();
        }
      });
    }
    else {
      $monthSelectOptions.each(function(){
        $(this).show();
      });
    }
    validateDays(month, year, $datediv, 'past')
  }

  // Validation for html5 input type=date
  $('.past-date input[type=date]').attr({ "max": formatDate(d), "min": "1900-01-01" });
  $('.future-date input[type=date]').attr({ "min": formatDate(d), "max": "2050-01-01" });

  // Remove futures years
  $(".past-date select[title=Year] option").each(function(i,el){
    if (parseInt($(this).text()) > d.getFullYear()) {
      $(this).remove();
    }
    else {
      $(this).parent().prepend(el);
    }
  });
  // Remove past years
  $(".future-date select[title=Year] option").each(function(i,el){
    if (parseInt($(this).text()) < d.getFullYear()) {
      $(this).remove();
    }
    else {
      $(this).parent().prepend(el);
    }
  });

  $(".past-date").each(function(){
    validateMonth(parseInt($(this).find("select[title=Month]").val()), parseInt($(this).find("select[title=Year]").val()), $(this), 'past');
  });

  // Validation for date selectbox on Month chenged
  $(".past-date").on('change', 'select[title=Month]', function(event) {  
    validateDays(parseInt($(this).val()), parseInt($(this).parent().parent().find("select[title=Year]").val()), $(this).parent().parent(), 'past');
  });

  // Validation for date and Month selectbox on Year chenged
  $(".past-date").on('change', 'select[title=Year]', function(event) {  
    validateMonth(parseInt($(this).parent().parent().find("select[title=Month]").val()), parseInt($(this).val()), $(this).parent().parent(), 'past');
  });

})})(jQuery);
