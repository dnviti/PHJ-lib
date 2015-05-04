//incremento quantit� ricambi
function incrementa(target){
	//var target=$(this).attr("target");
	var quantita=$("#"+target).val();
	quantita++;
	$("#"+target).val(quantita);
}
function decrementa(target){
	//var target=$(this).attr("target");
	var quantita=$("#"+target).val();
	if(quantita>0){
		quantita--;
		$("#"+target).val(quantita);
	}
}
//AGGIUNGI RIGA RICAMBIO
var contaRicambio=4;
var rigaRicambio=4;
function aggiungiRicambio(){
		
		var table = document.getElementById("ricambi");
		
		var row = table.insertRow(rigaRicambio);

		
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);

		cell1.innerHTML = "<input rintracciaRiga="+rigaRicambio+" onclick=\"eliminaRigaRicambio(this)\" style=\"width:100%;\" type=\"button\" value=\"Elimina\" />";
		cell2.innerHTML = "<input type=\"text\" class=\"codice_ricambio\" placeholder=\"Codice...\" tabindex=\"33\"/>";
		cell3.innerHTML = "<input type=\"text\" class=\"nome_articolo\" placeholder=\"Aritcolo...\" tabindex=\"33\"/></td>";
		cell4.innerHTML = "<input style=\"width: 55%;\" type=\"number\" id=\"quantita_"+contaRicambio+"\" class=\"quantita\" placeholder=\"Quantità...\" value=0  tabindex=\"33\"/>\n" +
				"<input onclick=\"decrementa($(this).attr('target'))\" target=\"quantita_"+contaRicambio+"\" class=\"decrementa\" style=\"width: 20%;\" type=\"button\" value=\" - \" />\n" +
				"<input onclick=\"incrementa($(this).attr('target'))\" target=\"quantita_"+contaRicambio+"\" class=\"incrementa\" style=\"width: 20%;\" type=\"button\" value=\" + \" />";
		contaRicambio++;
		rigaRicambio++;
}function eliminaRigaRicambio(t) //ELIMINA RIGA RICAMBIO
{
    var row = t.parentNode.parentNode;
    document.getElementById("ricambi").deleteRow(row.rowIndex);
    console.log(row);
    rigaRicambio--;
}

//AGGIUNGI RICA TACNICO
var contaTecnico=2;
var rigaTecnico=2;
function aggiungiTecnico(){
		
		var table = document.getElementById("tecnici");
		
		var row = table.insertRow(rigaTecnico);

		
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		var cell6 = row.insertCell(5);
		var cell7 = row.insertCell(6);

		cell1.innerHTML = "<input rintracciaRiga="+rigaTecnico+" onclick=\"eliminaRigaTecnico(this)\" style=\"width:100%;\" type=\"button\" value=\"Elimina\" />";
		cell2.innerHTML = "<input type=\"text\" id=\"codice_"+contaTecnico+"\" placeholder=\"Codice...\" tabindex=\"39\"/>";
		cell3.innerHTML = "<input type=\"text\" id=\"tecnico_"+contaTecnico+"\" placeholder=\"Tecnico...\" tabindex=\"39\" />";
		cell4.innerHTML = "<input style=\"max-width:100%;\" type=\"date\" id=\"data_"+contaTecnico+"\" placeholder=\"Data...\" tabindex=\"39\" />";
		cell5.innerHTML = "<input type=\"time\" id=\"dalle_"+contaTecnico+"\" placeholder=\"Ora inizio...\" tabindex=\"39\" />";
		cell6.innerHTML = "<input type=\"time\" id=\"alle_"+contaTecnico+"\" placeholder=\"Ora termine...\" tabindex=\"39\" />";
		cell7.innerHTML = "<input type=\"number\" id=\"totale_ore_"+contaTecnico+"\" placeholder=\"Totale ore...\" tabindex=\"39\" />";
		contaTecnico++;
		rigaTecnico++;
}function eliminaRigaTecnico(t) //ELIMINA RIGA TECNICO
{
    var row = t.parentNode.parentNode;
    document.getElementById("tecnici").deleteRow(row.rowIndex);
    console.log(row);
    rigaTecnico--;
}



