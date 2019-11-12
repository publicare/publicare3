<script>
<!--
	function isEmailAddr(email){
  var result = false;
  var theStr = new String(email);
  var index = theStr.indexOf("@");
  if (index > 0){
    var pindex = theStr.indexOf(".",index);
    if ((pindex > index+1) && (theStr.length > pindex+1))
	result = true;
  }
  return result;
}

function validRequired(formField,fieldLabel){
	var result = true;
	if (formField.value == ""){
		alert('O campo "' + fieldLabel +'" deve ser preenchido.');
		formField.focus();
		MostraBotaoGravar();
		result = false;
	}
	
	return result;
}

function allDigits(str){
   return inValidCharSet(str,"0123456789");
}

function inValidCharSet(str,charset){
	var result = true;
	for (var i=0;i<str.length;i++)
		if (charset.indexOf(str.substr(i,1))<0) {
			result = false;
			break;
		}
	
	return result;
}

function validEmail(formField,fieldLabel,required){
	var result = true;
	
	if (required && !validRequired(formField,fieldLabel))
		result = false;

	if (result && ((formField.value.length < 3) || !isEmailAddr(formField.value)) )	{
		alert("O email foi digitado de forma incorreta");
		formField.focus();
		result = false;
	}
   
  return result;

}

function validNum(formField,fieldLabel,required){
	var result = true;
	if (required && !validRequired(formField,fieldLabel)) result = false;
 	if (result) {
 		if (!allDigits(formField.value)){
 			alert('Por favor, preencha o campo "' + fieldLabel +'" com um número válido.');
 			MostraBotaoGravar()
			formField.focus();		
			result = false;
		}
	} 
	return result;
}


function validInt(formField,fieldLabel,required){
	var result = true;
	if (required && !validRequired(formField,fieldLabel)) result = false;
 	if (result){
 		var num = parseInt(formField.value);
 		if (isNaN(num)){
 			alert('Por favor, preencha o campo "' + fieldLabel +'" com um número válido.');
			formField.focus();		
			result = false;
		}
	} 
	return result;
}

function validDate(DateField,fieldLabel,required,valor)
{
	var checkstr = "0123456789";
	var Datevalue = "";
	var DateTemp = "";
	var seperator = "/";
	var day;
	var month;
	var year;
	var leap = 0;
	var err = 0;
	var i;
	   err = 0;
	 
	
	   DateValue = DateField.value;
	   /* Delete all chars except 0..9 */
	   for (i = 0; i < DateValue.length; i++) {
		  if (checkstr.indexOf(DateValue.substr(i,1)) >= 0) {
		     DateTemp = DateTemp + DateValue.substr(i,1);
		  }
	   }
	   DateValue = DateTemp;
	   /* Always change date to 8 digits - string*/
	   /* if year is entered as 2-digit / always assume 20xx */
	   if (DateValue.length == 6) {
	      DateValue = DateValue.substr(0,4) + '20' + DateValue.substr(4,2); }
	   if (DateValue.length != 8) {
	      err = 19;}
	   /* year is wrong if year = 0000 */
	   year = DateValue.substr(4,4);
	   if (year == 0) {
	      err = 20;
	   }
	   /* Validation of month*/
	   month = DateValue.substr(2,2);
	   if ((month < 1) || (month > 12)) {
	      err = 21;
	   }
	   /* Validation of day*/
	   day = DateValue.substr(0,2);
	   if (day < 1) {
	     err = 22;
	   }
	   /* Validation leap-year / february / day */
	   if ((year % 4 == 0) || (year % 100 == 0) || (year % 400 == 0)) {
	      leap = 1;
	   }
	   if ((month == 2) && (leap == 1) && (day > 29)) {
	      err = 23;
	   }
	   if ((month == 2) && (leap != 1) && (day > 28)) {
	      err = 24;
	   }
	   /* Validation of other months */
	   if ((day > 31) && ((month == "01") || (month == "03") || (month == "05") || (month == "07") || (month == "08") || (month == "10") || (month == "12"))) {
	      err = 25;
	   }
	   if ((day > 30) && ((month == "04") || (month == "06") || (month == "09") || (month == "11"))) {
	      err = 26;
	   }
	   /* if 00 ist entered, no error, deleting the entry */
	   if ((day == 0) && (month == 0) && (year == 00)) {
	      err = 0; day = ""; month = ""; year = ""; seperator = "";
	   }
	   /* if no error, write the completed date to Input-Field (e.g. 13.12.2001) */
	   if (err == 0) {
	      DateField.value = day + seperator + month + seperator + year;
	      return true;
	   }
	   /* Error-message if err != 0 */
	   else {
	      alert("Por favor preencha o campo "+fieldLabel+" com uma data no formato DD/MM/YYYY");
	      //DateField.select();
		 // DateField.focus();
		  return false;
	   }
}

function validTime (formField,fieldLabel,required,valor){
	var result = true;
	if (required && !validRequired(formField,fieldLabel)) result = false;
 	if (result){
 		if (valor)
			var elems=valor.split(":");
		else
			var elems = formField.value.split(":");
		result = ((elems.length == 3) || (elems.length == 2)); // should be three or two components
 		if (result)
		{
			if (elems.length==2)
				segundo=0;
			else
				segundo=elems[2];
			teste = new Date(2002,01,01,elems[0],elems[1],segundo);
			if ((teste.getHours()==elems[0]) && (teste.getMinutes()==elems[1]) && (teste.getSeconds()==elems[2]))
				result = true;
			else
				result = false;
		}
  		if (!result){
 			alert('Entre uma hora válida no formato HH:MM:SS para o campo "' + fieldLabel +'".');
			formField.focus();		
		}
	} 
	
	return result;
}

function validDateTime(formField,fieldLabel,required,sodata) {
	var elems = formField.value.split(" ");
	if ((elems.length!=2) && (sodata==false))
	{
		alert('Entre uma data/hora válida no formato MM/DD/YYYY HH:MM:SS para o campo "' + fieldLabel +'".');
		return false;
		formfield.focus();
		MostraBotaoGravar();
	}
	result =  validDate(formField,fieldLabel,required,elems[0]);
	if (elems.length==2)
	 result = result && validTime(formField,fieldLabel,required,elems[1]);
	return result;
}

	function MostraBotaoGravar(){
	trGravarTop2.style.display='none';
	trGravarTop1.style.display='';
	}

	function JSVerificaCampoObrigatorio(ObjVer,ObjNome){
	 switch(ObjVer.type){
		case "text":
			if (ObjVer.value == ""){
				alert('Preencha o campo "'+ObjNome+'"');
				MostraBotaoGravar();
				return false;}
			else
				return true;
			break;
		case "radio":
			if (document.forms['formedit'][ObjVer.id].value == 'undefined'){
				alert('Preencha o campo de nome "'+ObjNome+'"');
				MostraBotaoGravar();
				return false;}
			else
				return true;
			break;
		case "checkbox":
			if (!ObjVer.checked){
				alert('Preencha o campo "'+ObjNome+'"');
				MostraBotaoGravar();
				return false;}
			else
				return true;
			break;
		case "textarea":
			//if (ObjVer.value ==""){
			//	alert("Existe um campo texto-avançado que deve ser preenchido obrigatóriamente: "+ObjNome);
			//MostraBotaoGravar();
			//	return false;}
			//else
				return true;
			break;
		case "select-one":
			if (ObjVer.value == 0){
				alert('Preencha o campo "'+ObjNome+'"');
				MostraBotaoGravar(); 
				return false;}
			else
				return true;
			break;
		case "file":
			if (ObjVer.value == 0){
				alert('Preencha o campo "'+ObjNome+'"');
				MostraBotaoGravar();
				return false;}
			else
				return true;
			break;
		default:
			//if (ObjVer.value ==""){
			//	alert('Preencha o campo "'+ObjNome+'"');
			//MostraBotaoGravar()
			//	return false;}
			//else
				return true;
			break;
	 alert('corrija aqui o problema de nao aparecer o botao!');
	 }
	}

//-->
</script>