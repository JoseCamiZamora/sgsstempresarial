function SU_form_crear_usuario(){
    $('#modal_usuarios').modal();
    $('.preloader').fadeIn();
    var urlraiz=$("#url_raiz_proyecto").val();
    var miurl='';
    miurl=urlraiz+"/form_nuevo_usuario";

    $.ajax({
    url: miurl
    }).done( function(resul) 
    {
      $('.preloader').fadeOut();
      $("#contenido_modal_usuarios").html(resul);
   
    }).fail( function() 
   {
    $('.preloader').fadeOut();
     SU_revise_conexion();
   }) ;


}

function SU_form_crear_usuario_pr(){
    $('#modal_usuarios').modal();
    $('.preloader').fadeIn();
    var urlraiz=$("#url_raiz_proyecto").val();
    var miurl='';
    miurl=urlraiz+"/form_nuevo_usuario_pr";

    $.ajax({
    url: miurl
    }).done( function(resul) 
    {
      $('.preloader').fadeOut();
      $("#contenido_modal_usuarios").html(resul);
   
    }).fail( function() 
   {
    $('.preloader').fadeOut();
     SU_revise_conexion();
   }) ;


}

$(document).on("submit","#f_crear_usuario",function(e){
   //funcion para crear un nuevo usuario
  e.preventDefault();
  $('.preloader').fadeIn();

  var formu=$(this);
  var urlraiz=$("#url_raiz_proyecto").val();
  var varurl=urlraiz+"/crear_usuario_th";

  
  $.ajax({
    // la URL para la petición
    url : varurl,
    data : formu.serialize(),
    method: 'POST',
    dataType : 'html'
  })
  .done(function(resul) {
      $('.preloader').fadeOut();
      $("#contenido_modal_usuarios").html(resul);

   })
  .fail(function(err){
      $('.preloader').fadeOut();
      SU_revise_conexion();
         
  });


});

$(document).on("submit","#f_crear_usuario_pr",function(e){
   //funcion para crear un nuevo usuario
  e.preventDefault();
  $('.preloader').fadeIn();

  var formu=$(this);
  var urlraiz=$("#url_raiz_proyecto").val();
  var varurl=urlraiz+"/crear_usuario_pr";

  
  $.ajax({
    // la URL para la petición
    url : varurl,
    data : formu.serialize(),
    method: 'POST',
    dataType : 'html'
  })
  .done(function(resul) {
      $('.preloader').fadeOut();
      $("#contenido_modal_usuarios").html(resul);

   })
  .fail(function(err){
      $('.preloader').fadeOut();
      SU_revise_conexion();
         
  });


});

$(document).on("submit","#f_editar_usuario",function(e){
  //funcion para actualizar los datos del usuario
  e.preventDefault();
  $('.preloader').fadeIn();

  var formu=$(this);
  var urlraiz=$("#url_raiz_proyecto").val();
  var varurl=urlraiz+"/editar_usuario";

  
  $.ajax({
    url : varurl,
    data : formu.serialize(),
    method: 'POST',
    dataType : 'json',
    })
    .done(function(resul) {
      $('.preloader').fadeOut();
      if(resul.estado=="actualizado"){
        $.notify("Datos actualizados ", "success"); 

      }else{
        $.notify("error al guardar, Revise su Conexión ", "error"); 
      }

    })
    .fail(function(err){
        SU_revise_conexion();
        $(".preloader").hide();
         
    });
  

});

$(document).on("submit","#f_editar_acceso",function(e){
  //funcion para actualizar los datos del usuario
  e.preventDefault();
  $('.preloader').fadeIn();

  var formu=$(this);
  var urlraiz=$("#url_raiz_proyecto").val();
  var varurl=urlraiz+"/editar_acceso";

  
  $.ajax({
    url : varurl,
    data : formu.serialize(),
    method: 'POST',
    dataType : 'json',
    })
    .done(function(resul) {
      $('.preloader').fadeOut();
      if(resul.estado=="actualizado"){
        $.notify("Datos actualizados ", "success"); 

      }else{
        $.notify("error al guardar, Revise su Conexión ", "error"); 
      }

    })
    .fail(function(err){
        SU_revise_conexion();
        $(".preloader").hide();
         
    });
  

});







$(document).on("submit","#f_editar_imagen",function(e){
  //para subir una imagen de usuario al sistema
  e.preventDefault();
  $('.preloader').fadeIn();
  var quien=$(this).attr("id");
  var formu=$(this);
  var varurl="";
   var urlraiz=$("#url_raiz_proyecto").val();
  var varurl=urlraiz+"/editar_imagen";

  var file = $("#file_avatar")[0].files[0];
  var fileName = file.name;
  var fileSize = file.size;

  var formData = new FormData();
  
  formData.append("photo", file );
  formData.append("id_usuario", $("#id_usuario_avatar").val() );
  
  $(".preloader").fadeIn();

  
  $.ajax({
    url : varurl,
    method : 'POST',
    cache: false,
    processData: false,
    contentType : false,
    data: formData,
    headers: {
      'X-CSRF-Token': $('input[id="_token_avatar"]').val()
   }
  
  }).done(function(resul){
    
    $('.preloader').fadeOut();
    
    if(resul.estado=="actualizado"){
        $.notify("Datos actualizados ", "success"); 
      
    }

  })
  .fail( function (jqXHR, status, error) { 
      SU_revise_conexion();
      $(".preloader").hide();
   });


});

function SU_cambiar_imagen(idusuario){

    $('#modal_usuarios').modal();
    $('.preloader').fadeIn();
    var urlraiz=$("#url_raiz_proyecto").val();
    var miurl='';
    miurl=urlraiz+"/form_editar_imagen/"+idusuario+"";

    $.ajax({
    url: miurl
    }).done( function(resul) 
    {
      $('.preloader').fadeOut();
      $("#contenido_modal_usuarios").html(resul);
   
    }).fail( function() 
   {
     $('.preloader').fadeOut();
     SU_revise_conexion();
   }) ;


}


function SU_revise_conexion(){
  swal({
    title: "Error",
    text: "Revise su Conexion y vuelva a intentarlo",
    type: "warning"
  },function (isConfirm) {  $(".preloader").hide();  });
}

function cambiar_tipo(id_tipo){
  
  if(id_tipo == 1){
   $('#PQRS_id_opcion').prop("disabled",true);;
  }else{
     $('#PQRS_id_opcion').prop("disabled",false);
  }
    
  
}