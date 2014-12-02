(function($){$(function() {

  // get curent date
  var d = new Date();

  // Validation for html5 input type=date
  $('.past-date input[type=date]').attr({ "max": d.getFullYear().toString() + "-"+ d.getMonth().toString() +"-"+ d.getDate().toString()});


  // Validation for date selectbox

  // Remove futures years
  $(".past-date select[title=Year] option").each(function(){
    if (parseInt($(this).text()) > d.getFullYear()) {
      $(this).remove();
    };
  });

  // If user select curent Year - hide all futures Months
  $(".past-date select[title=Year]").change(function(event) {
    if (parseInt($(this).val()) == d.getFullYear()) {
      $(this).parent().parent().find("select[title=Month] option").each(function(){
        if (parseInt($(this).val()) > d.getMonth()) {
          $(this).hide();
        };
      });
      // If future Month was selected - select first option
      if ($(this).parent().parent().find("select[title=Month]").val() > d.getMonth()) {
        $(this).parent().parent().find("select[title=Month]").val($(this).parent().parent().find("select[title=Month] option:first").val());
      };
      // If user select curent Month - hide all futures Days
      $(this).parent().parent().find("select[title=Month]").change(function(event) {
        if (parseInt($(this).val()) == d.getMonth()) {
          $(this).parent().parent().find("select[title=Day] option").each(function(){
            if (parseInt($(this).val()) > d.getDate()) {
              $(this).hide();
            };
          });
          // If future Day was selected - select first option
          if ($(this).parent().parent().find("select[title=Day]").val() > d.getDate()) {
            $(this).parent().parent().find("select[title=Day]").val($(this).parent().parent().find("select[title=Day] option:first").val());
          };
        }
        // If user select not curent Month - show all Days
        else {
          $(this).parent().parent().find("select[title=Day] option").each(function(){
            $(this).show();
          });
        };
      });
    }
    // If user select not curent Year - show all Months
    else {
      $(this).parent().parent().find("select[title=Month] option").each(function(){
        $(this).show();
      });
    };
  });

})})(jQuery);
