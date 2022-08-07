function getTotal(){
	var total = $('#total').text()
	total = total.replace(/\s/g, '');
	total = total.replace('€', '');
	return total;
}

function paiementSuite(typeSuite,typePaiement,montantPaiement,resteAPayer){
	
	if(typePaiement === 1){
		var espece = typeSuite === 1 ? montantPaiement + resteAPayer : montantPaiement;
		var cb = typeSuite === 2 ? resteAPayer : 0 ;
		var cheque = typeSuite === 3 ? resteAPayer : 0;
	}
	else if(typePaiement === 2){
		var espece = typeSuite === 1 ? resteAPayer : 0;
		var cb = typeSuite === 2 ? montantPaiement + resteAPayer : montantPaiement ;
		var cheque = typeSuite === 3 ? resteAPayer : 0;
	}
	else if(typePaiement === 3){
		var espece = typeSuite === 1 ? resteAPayer : 0;
		var cb = typeSuite === 2 ? resteAPayer : 0 ;
		var cheque = typeSuite === 3 ? montantPaiement + resteAPayer : montantPaiement;
	}
	console.log(espece,cb,cheque)
	var total = getTotal();
	var id_caisse = 1;
	var session = 1;
	$.ajax({
		url: "../tickets/ajoutTicket.php",
		type: "POST",
		contentType: "application/json",
		data: JSON.stringify({
			"espece": espece,
			"cb": cb,
			"cheques": cheque,
			"ticket_restaurant": 0,
			"total": total,
			"id_caisse":id_caisse,
			"session":session,
		}),
		success: function (data) {
			var result =  JSON.parse(data)
			if (result.response === 1) {
				clearPanier(session,id_caisse)
			}
		}
	})


}

// PAIEMENTS ESPECE
$('#paiementEspece').click(function () {
	var total = getTotal();
	$('#inputMontantEspece').select()
	$('#inputMontantEspece').val(total)
	$('#montantEspece').html(total)

})

$('#btnPaiementEspece').click(function () {
	var montantEspece = parseFloat($('#inputMontantEspece').val());
	var total = parseFloat(getTotal());
	var id_caisse = 1;
	var session = 1;
	console.log(montantEspece<total,montantEspece == total)
	if (montantEspece == total) {
		$.ajax({
			url: "../tickets/ajoutTicket.php",
			type: "POST",
			contentType: "application/json",
			data: JSON.stringify({
				"espece": montantEspece,
				"cb": 0,
				"cheques": 0,
				"ticket_restaurant": 0,
				"total": total,
				"id_caisse":id_caisse,
				"session":session,
			}),
			success: function (data) {
				var result =  JSON.parse(data)
				if (result.response === 1) {
					clearPanier(session,id_caisse)
				}
			}
		})
	}
	else if(montantEspece < total){
		var resteAPayer = total - montantEspece;
		resteAPayer = resteAPayer.toFixed(2)
		$('#modal-espece > .modal-dialog > .modal-content > .modal-header > .modal-title').html("SUITE PAIEMENT");
		$('#modal-espece > .modal-dialog > .modal-content > .modal-body > .text-paiement').html("<p>Reste a payer:  <span class='resteAPayer'>"+resteAPayer+" €</span> </br>Choisir une méthode de paiement</p>");
		$('#modal-espece > .modal-dialog > .modal-content > .modal-body > .inputPaiement')
		.html("<button type='button' class='btn btn-success btnPaiement' onClick='paiementSuite(1,1,"+montantEspece+","+resteAPayer+")' id='btnPaiementSuiteCB'>Espece</button><button type='button' class='btn btn-info btnPaiement' onClick='paiementSuite(2,1,"+montantEspece+","+resteAPayer+")' id='btnPaiementSuiteEspece'>CB</button><button type='button' onClick='paiementSuite(3,1,"+montantEspece+","+resteAPayer+")' class='btn btn-warning btnPaiement' id='btnPaiementSuiteCheques'>Cheque</button>");
	}
	else if(montantEspece > total){
		var monnaieArendre = montantEspece - total;
		$('#caddie').html("<h1>RENDU MONNAIE: "+ monnaieArendre.toFixed(2) +" €</h1>")
		$.ajax({
			url: "../tickets/ajoutTicket.php",
			type: "POST",
			contentType: "application/json",
			data: JSON.stringify({
				"espece": monnaieArendre,
				"cb": 0,
				"cheques": 0,
				"ticket_restaurant": 0,
				"total": total,
				"id_caisse":id_caisse,
				"session":session,
			}),
			success: function (data) {
				var result =  JSON.parse(data)
				if (result.response === 1) {
					clearPanier(session,id_caisse,true)
				}
			}
		})
	}
})


// PAIEMENT CB 
$('#paiementCB').click(function () {
	var total = getTotal();
	$('#inputMontantCB').select()
	$('#inputMontantCB').val(total)
	$('#montantCB').html(total)

})

$('#btnPaiementCB').click(function () {
	var montantCB = $('#inputMontantCB').val();
	var total = getTotal();
	var id_caisse = 1;
	var session = 1;

	if (montantCB == total) {
		$.ajax({
			url: "../tickets/ajoutTicket.php",
			type: "POST",
			contentType: "application/json",
			data: JSON.stringify({
				"espece": 0 ,
				"cb": montantCB,
				"cheques": 0,
				"ticket_restaurant": 0,
				"total": total,
				"id_caisse":id_caisse,
				"session":session,
			}),
			success: function (data) {
				var result =  JSON.parse(data)
				if (result.response === 1) {
					clearPanier(session,id_caisse)
				}
			}
		})
	}
})


 // PAIEMENT CHEQUES
 $('#paiementCB').click(function () {
 	var total = getTotal();
 	$('#inputMontantCheque').select()
 	$('#inputMontantCheque').val(total)
 	$('#montantCheque').html(total)

 })

 $('#btnPaiementCheque').click(function () {
 	var montantCheque = $('#inputMontantCheque').val();
 	var total = getTotal();
 	var id_caisse = 1;
 	var session = 1;

 	if (montantCheque == total) {
 		$.ajax({
 			url: "../tickets/ajoutTicket.php",
 			type: "POST",
 			contentType: "application/json",
 			data: JSON.stringify({
 				"espece":0,
 				"cb": 0,
 				"cheques": montantCheque,
 				"ticket_restaurant": 0,
 				"total": total,
 				"id_caisse":id_caisse,
 				"session":session,
 			}),
 			success: function (data) {
 				var result =  JSON.parse(data)
 				if (result.response === 1) {
 					clearPanier(session,id_caisse)
 				}
 			}
 		})
 	}
 })