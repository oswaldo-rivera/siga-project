var passView = false;
var confirmView = false;
var conf = 0;

$("#f_usuario").submit(function () {
  dismissAlert();  // eliminar cualquier alerta anterior
  conf = 0;
  $("#us_pass").trigger('change');
  $('#us_confirm').trigger('change');
  let valid = /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/;
  
if(conf<4) {
    setAlerta('Corregí las deficiencias en la contraseña', 'danger');
    // alert(conf);
    return false;
  } else if ($('#us_pass').val() != $('#us_confirm').val()){
    setAlerta('Las contraseñas deben ser iguales', 'danger');
    return false;
  }

});

$("#us_pass").change(function() {
  let pass = $('#us_pass').val();
  conf = 0;
  let mesg = "";
  let color = "primary";


  // verificar el largo
  if (pass.length < 8) {
    $("#tp1").html('<span class="text-danger"><i class="bi-x-square"></i> +8 car.</span>');
  } else {
    $("#tp1").html('<span class="text-success"><i class="bi-check-square"></i> +8 car.</span>');
    conf += 1;
  }

  // Maysculas y minusculas
  if (pass.match(/[a-z]/) && pass.match(/[A-Z]/)) {
    $("#tp2").html('<span class="text-success"><i class="bi-check-square"></i> a-z/A-Z</span>');
    conf += 1;
  } else {
    $("#tp2").html('<span class="text-danger"><i class="bi-x-square"></i> a-z/A-Z</span>');
  }
  
  // numeros
  if (pass.match(/\d/)) {
    $("#tp3").html('<span class="text-success"><i class="bi-check-square"></i> núm.0-9</span>');
    conf += 1;
  } else {
    $("#tp3").html('<span class="text-danger"><i class="bi-x-square"></i> núm.0-9</span>');
  }

  // Check for special characters
  if (pass.match(/[^a-zA-Z\d]/)) {
    $("#tp4").html('<span class="text-success"><i class="bi-check-square"></i> @.*_#$</span>');
    conf += 1;
  } else {
    $("#tp4").html('<span class="text-danger"><i class="bi-x-square"></i> @.*_#$</span>');
  }

  // resultados
  if (conf < 2) {
    mesg = '<i class="bi-x-circle-fill"></i> Muy debil';
    color = "danger";
  } else if (conf === 2) {
    mesg = '<i class="bi-x-circle-fill"></i> Debil';
    color = "danger";
  } else if (conf === 3) {
    mesg = '<i class="bi-x-circle-fill"></i> Revisar';
    color = "danger";
  } else {
    mesg = '<i class="bi-check-circle-fill"></i>';
    color = "success";
  }

  $('#us_pass_alert').removeAttr("class");
  $('#us_pass_alert').addClass('text-' + color);
  $('#us_pass_alert').html(mesg);

});

$('#us_confirm').change(function () {
  var pass = $('#us_pass').val();
  var confirm = $('#us_confirm').val();
  if (confirm != '') {
    if (pass != confirm) {
      $('#us_confirm_alert').removeAttr("class");
      $('#us_confirm_alert').addClass('text-danger');
      $('#us_confirm_alert').html('<i class="bi-x-circle-fill"></i> Deben ser iguales');
    } else { 
      $('#us_confirm_alert').removeAttr("class");
      $('#us_confirm_alert').addClass('text-success');
      $('#us_confirm_alert').html('<i class="bi-check-circle-fill"></i>');
    }
  }
});

$("#view-pass").click( function() {
  if (passView == false) {
    $("#us_pass").prop("type", "text");
    $(this).addClass('text-primary');
  } else {
    $("#us_pass").prop("type", "password");
    $(this).removeClass('text-primary');
  }
  passView = !passView;
});

$("#view-confirm").click( function() {
  if (confirmView == false) {
    $("#us_confirm").prop("type", "text");
    $(this).addClass('text-primary');
  } else {
    $("#us_confirm").prop("type", "password");
    $(this).removeClass('text-primary');
  }
  confirmView = !confirmView;
});