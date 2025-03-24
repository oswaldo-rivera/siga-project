
$("#user_code").keyup(function () {
  $(this).val($(this).val().toUpperCase());
  if ($(this).val().length == 6) {
    $("#btn-confirmar").removeClass("disabled");
    // setAlerta('El código está incompleto', 'danger');
  } else {
    $("#btn-confirmar").addClass('disabled');
  }
});

$("#user_code").blur(function () {
  if ( $(this).val().length == 6 ) {
    $("#btn-confirmar").removeClass("disabled");
  } else {
    $(this).focus();
    $("#btn-confirmar").addClass('disabled');
    return false;
  }
});