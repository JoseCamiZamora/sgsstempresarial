var validacionCP= function(){ 

this.resultado=true;
this.errores=[];

}

validacionCP.prototype.load = function(form) {


let nombre="#"+form+" input, "+ "#"+form+" select";
var valresul=true;
var valerrors=[];

$(nombre).each(
    function(index){  
        
       
       
      if( $(this).hasClass('valCP_email') ){    
          
		 	let resul=validacionCP.prototype.formato_email( $(this).val() );  
		 	if(resul!=true ){  let msj={ iden:$(this).attr('id'), msj: 'tiene formato email no valido' };  valresul=false; valerrors.push(msj); } 
		 	console.log(valerrors); 
		  };

		 if( $(this).hasClass('valCP_obligatorio') ){     
		 	let resul=validacionCP.prototype.required( $(this).val() );  
		 	if(resul!=true ){  let msj={ iden:$(this).attr('id'), msj: 'es obligatorio' };  valresul=false;  valerrors.push(msj); } 
		 };


		 if( $(this).hasClass('valCP_numerico') ){     
		 	let resul=validacionCP.prototype.es_numerico( $(this).val() );  
		 	if(resul!=true ){  let msj={ iden:$(this).attr('id'), msj: 'debe ser un numero ' };  valresul=false;  valerrors.push(msj); } 
		 };



    }
);




return {resultado:valresul, errores:valerrors};
}


validacionCP.prototype.show_errors = function(errors) {
    

      var html='';

         $.each(errors, function(i, elem){
         	
          html+='<span style="color:#F27474;">El campo '+elem.iden+': '+elem.msj+'</span></br>';
          $('#'+elem.iden+'').addClass('form-control-warning'); 
          $('#'+elem.iden+'').closest('.form-group').addClass('has-warning'); 
       
         });

     if(html==''){ html='existen errores de formato en los campos ingresados';}
   

      swal({              html:true,
                          title: "Existen Errores!",
                          text: html,
                          type: "warning"
                        },function (isConfirm) { }); 
}



validacionCP.prototype.formato_email = function(dato) {
     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
     return emailReg.test( dato );
}

validacionCP.prototype.required = function(dato) {
	if ( typeof(dato) !== "undefined" && dato !== null && dato!=''   ) { return true; }else{ return false; }
    
}


validacionCP.prototype.es_numerico = function(dato) {
   
    isNumeric = /^[-+]?(\d+|\d+\.\d*|\d*\.\d+)$/;
     return isNumeric.test(dato);
}

