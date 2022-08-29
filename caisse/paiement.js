

$('#modal-espece').on('shown.bs.modal', function() {
    $('#inputMontantEspece').select();
})

$('#modal-cb').on('shown.bs.modal', function() {
    $('#inputMontantCB').select();
})

$('#modal-cheque').on('shown.bs.modal', function() {
    $('#inputMontantCheque').select();
})

$('#modal-divers').on('shown.bs.modal', function() {
    $('#inputPrixDivers').focus();
})

$('#modal-remise').on('shown.bs.modal', function() {
    $('#inputMontantRemisePanier').focus();
})

$('#modal-facture').on('shown.bs.modal', function() {
    $('#inputNumeroTicket').focus();
})



$('#modal-retour').on('shown.bs.modal', function() {
    $('#inputRetourPrixDivers').focus();
    $('#inputRetourArticleCatalogue').focus();
    
})


function getTotal() {
    var total = $('#total').text()
    total = total.replace(/\s/g, '');
    total = total.replace('€', '');
    return total;
}

function imprimeFacture(){
    var numero_ticket = $('#inputNumeroTicket').val()
    var client = $('#inputNomClient').val()
    if(numero_ticket == "" && client == ""){
        Toast.fire({
            icon: 'error',
            title: "Veuillez remplir les champs avant d'imprimer la facture"
        })

    }else{
     $.ajax({
        url: 'facture.php',
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
         numero_ticket: numero_ticket,
         client:client,

     }),
        success: function (data) {
        	console.log(data)
            var result = JSON.parse(data)
            if (result.response === 1) {
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,parseFloat(result.espece),parseFloat(result.cb),parseFloat(result.cheque),result.ticket_pied,false,true,result.header,client)
                    
                    $('#modal-facture').modal('hide')
                    $('#inputNumeroTicket').val('')
                    $('#inputNomClient').val('')
            }
        }

    })
 }

}


function totalCaisse(id_caisse){
    $.ajax({
        url: "../print_total_caisse.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            "id_caisse":id_caisse
        }),
        success: function (data) {
            var result = JSON.parse(data)
            if (result.response === 1) {
                Toast.fire({
                    icon: 'success',
                    title: "Ticket du total caisse imprimé !"
                })
                let nombreImpresora = "lp2";
                var impresora = new Impresora();
                impresora.setEmphasize(0)
                impresora.write(result.ticket)
                impresora.feed(1)
                impresora.cut()
                impresora.cash()
                impresora.imprimirEnImpresora(nombreImpresora)
                .then(valor => {
                    console.log("Resultat: " + valor);


                });

                // window.setTimeout(function () {
                //     window.location.reload();
                // }, 1000);
            }
        }
    })
}

function viderPanier(id_caisse){
    $.ajax({
        url: "../panier/videPanier.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
           "videTout":true,
           "id_caisse":id_caisse
       }),
        success: function (data) {
            var result = JSON.parse(data)
            if (result.response === 1) {
                Toast.fire({
                    icon: 'success',
                    title: "Caddie est en train de se vider... !"
                })
                window.setTimeout(function () {
                    window.location.reload();
                }, 1000);
            }
        }
    })
}


function paiementSuite(typeSuite, typePaiement, montantPaiement, resteAPayer) {

    if (typePaiement === 1) {
        var espece = typeSuite === 1 ? montantPaiement + resteAPayer : montantPaiement;
        var cb = typeSuite === 2 ? resteAPayer : 0;
        var cheque = typeSuite === 3 ? resteAPayer : 0;
    } else if (typePaiement === 2) {
        var espece = typeSuite === 1 ? resteAPayer : 0;
        var cb = typeSuite === 2 ? montantPaiement + resteAPayer : montantPaiement;
        var cheque = typeSuite === 3 ? resteAPayer : 0;
    } else if (typePaiement === 3) {
        var espece = typeSuite === 1 ? resteAPayer : 0;
        var cb = typeSuite === 2 ? resteAPayer : 0;
        var cheque = typeSuite === 3 ? montantPaiement + resteAPayer : montantPaiement;
    }
    var total = getTotal();
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
        }),
        success: function (data) {
            console.log(data)
            var result = JSON.parse(data)
            if (result.response === 1) {
                if(espece>0){

                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied,true)
                }else{
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied)
                }
                $('#modal-espece').modal('hide')
                $('#modal-cheque').modal('hide')
                $('#modal-cb').modal('hide')
                $("#searchArticle").load(location.href + " #searchArticle");
                $('#total').html('0.00 €')
                clearPanier(result.session, result.id_caisse)
            }
        }
    })


}

// AJOUT ARTICLE DEPUIS CAISSE
$("#formAjoutArticle").submit(function(e) {

    e.preventDefault();

    var form = $(this);
    var actionUrl = form.attr('action');

    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            console.log(data)
            var result = JSON.parse(data);
            if(result.response === 1){
                Toast.fire({
                    icon: 'success',
                    title: "Article ajouté avec succès !"
                })
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else{
                Toast.fire({
                    icon: 'error',
                    title: "Une erreur c'est produite...Veuillez recommencer."
                })
            }
        }
    });

});



// PAIEMENTS ESPECE
$('#paiementEspece').click(function () {
    var total = getTotal();
    $('#inputMontantEspece').select()
    $('#inputMontantEspece').val(total)
    $('#montantEspece').html(total)


})

$('#clotureCaisse').click(function(){
    window.location.href = "cloture-caisse.php";

})


function imprimeTicket(ticket,ticket_part2,arendre,footer,espece,cb,cheque,ticket_pied,cash=false,facture=false,ticketnum="",client=""){
    Impresora.getImpresoras()
    .then(listaDeImpresoras => {

        var impresora = new Impresora();
        let imprimante = "";
        for (let i=0; i<listaDeImpresoras.length; i++) {
            console.log(listaDeImpresoras[i])
            if(listaDeImpresoras[i].search('EPSON TM') >= 0 || listaDeImpresoras[i].search('lp') >= 0 ){
                imprimante = listaDeImpresoras[i]
            }
        }
        console.log(typeof(cb))
        arendre = parseFloat(arendre)
        impresora.setEmphasize(0);
        impresora.setAlign("center")
        if(facture){
            impresora.write('--------------------------------------')
            impresora.write('\n')
            impresora.write(ticketnum)
            impresora.write('\n')
            impresora.write('POUR '+client)
            impresora.write('\n')
            impresora.write('--------------------------------------')
            impresora.write('\n')
        }
        impresora.write(ticket)
        impresora.write("\n")
        impresora.setAlign('left')
        impresora.write(ticket_part2)
        impresora.setAlign("center")
        impresora.write('--------------------------------------')
        impresora.write("\n")
        
        impresora.feed(1)
        impresora.setAlign('left')
        impresora.write('  Details du paiement:')
        if(espece>0){
            impresora.write("\n")
            impresora.write("    > "+espece.toFixed(2)+" EUR EN ESPECES")
        }
        if(arendre>0){
            impresora.write('\n')
            impresora.write("    > MONNAIE RENDU "+arendre.toFixed(2)+" EUR")
        }
        if(cb>0){
            impresora.write('\n')
            impresora.write("    > "+cb.toFixed(2)+" EUR EN CB")
        }
        if(cheque>0){
            impresora.write('\n')
            impresora.write("    > "+cheque.toFixed(2)+" EUR EN CHEQUE")
        }

        impresora.write("\n")
        impresora.feed(1)
        impresora.setAlign("center")
        impresora.write(ticket_pied)
        impresora.write('\n')
        impresora.feed(1)
        impresora.write('======================================')
        impresora.write('\n')
        impresora.write(footer)
        impresora.feed(1)
    // imprimeTicket(ticket,num,ttc,total_ht,total_tva8,true)
    impresora.cut();
    // impresora.cutPartial();
    if(cash){
        impresora.cash()
    }
    impresora.imprimirEnImpresora(imprimante)
    .then(valor => {
        console.log("Resultat: " + valor);
        

    });
});
    
    

}


function paiementEspece(){
    var montantEspece = parseFloat($('#inputMontantEspece').val());
    var total = parseFloat(getTotal());
    console.log(montantEspece,total)
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
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response == 1) {
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied,true)
                    $('#modal-espece').modal('hide')
                    $('#total').html('0.00 €')
                    $("#searchArticle").load(location.href + " #searchArticle");
                    clearPanier(result.session, result.id_caisse)
                }
            }
        })
    } else if (montantEspece < total) {
        var resteAPayer = total - montantEspece;
        resteAPayer = resteAPayer.toFixed(2)
        $('#modal-espece > .modal-dialog > .modal-content > .modal-header > .modal-title').html("SUITE PAIEMENT");
        $('#modal-espece > .modal-dialog > .modal-content > .modal-body > .text-paiement').html("<p>Reste a payer:  <span class='resteAPayer'>" + resteAPayer + " €</span> </br>Choisir une méthode de paiement</p>");
        $('#modal-espece > .modal-dialog > .modal-content > .modal-body > .inputPaiement')
        .html("<button type='button' class='btn btn-success btnPaiement' onClick='paiementSuite(1,1," + montantEspece + "," + resteAPayer + ")' id='btnPaiementSuiteCB'>Espece</button><button type='button' class='btn btn-info btnPaiement' onClick='paiementSuite(2,1," + montantEspece + "," + resteAPayer + ")' id='btnPaiementSuiteEspece'>CB</button><button type='button' onClick='paiementSuite(3,1," + montantEspece + "," + resteAPayer + ")' class='btn btn-warning btnPaiement' id='btnPaiementSuiteCheques'>Cheque</button>");
        
    } else if (montantEspece > total) {
        var monnaieArendre = montantEspece - total;
        // $('#caddie').html("<h1 id='rendu'>RENDU MONNAIE: " + monnaieArendre.toFixed(2) + " €</h1>")
        $.ajax({
            url: "../tickets/ajoutTicket.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                "espece": montantEspece,
                "arendre":monnaieArendre,
                "rendu":"true",
                "cb": 0,
                "cheques": 0,
                "ticket_restaurant": 0,
                "total": total,
                
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response === 1) {
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied,true)
                    $('#caddie').html('<h1 class="text-center" id="rendu" style="margin-top:50px;font-weight: 600">RENDU MONNAIE: ' + monnaieArendre.toFixed(2) + ' €</h1>')
                    $('#total').text('0.00 €')
                    $('#modal-espece').modal('hide')
                    $("#searchArticle").load(location.href + " #searchArticle");
                    clearPanier(result.session, result.id_caisse, true)
                }
            }
        })
    }
    else if(total === 0){
        Toast.fire({
            icon: 'error',
            title: "Impossible d'encaisser un panier à 0 €"
        })
    }
}

$("#inputMontantEspece").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
       paiementEspece()
   }
});

// PAIEMENT CB 
$('#paiementCB').click(function () {
    var total = getTotal();
    $('#inputMontantCB').select()
    $('#inputMontantCB').val(total)
    $('#montantCB').html(total)

})



function paiementCB(){
    var montantCB = $('#inputMontantCB').val();
    var total = parseFloat(getTotal());

    console.log(montantCB, total)
    if (montantCB == total  && montantCB > 0 ) {
        $.ajax({
            url: "../tickets/ajoutTicket.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                "espece": 0,
                "cb": montantCB,
                "cheques": 0,
                "ticket_restaurant": 0,
                "total": total,
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response === 1) {
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied)
                    $('#modal-cb').modal('hide')
                    $('#total').html('0.00 €')
                    $("#searchArticle").load(location.href + " #searchArticle");
                    clearPanier(result.session, result.id_caisse)
                }
            }
        })
    } else if (montantCB < total) {
        var resteAPayer = total - montantCB;
        resteAPayer = resteAPayer.toFixed(2)
        $('#modal-cb > .modal-dialog > .modal-content > .modal-header > .modal-title').html("SUITE PAIEMENT");
        $('#modal-cb > .modal-dialog > .modal-content > .modal-body > .text-paiement').html("<p>Reste a payer:  <span class='resteAPayer'>" + resteAPayer + " €</span> </br>Choisir une méthode de paiement</p>");
        $('#modal-cb > .modal-dialog > .modal-content > .modal-body > .inputPaiement')
        .html("<button type='button' class='btn btn-success btnPaiement' onClick='paiementSuite(1,2," + montantCB + "," + resteAPayer + ")' id='btnPaiementSuiteCB'>Espece</button><button type='button' class='btn btn-info btnPaiement' onClick='paiementSuite(2,1," + montantCB + "," + resteAPayer + ")' id='btnPaiementSuiteCB'>CB</button><button type='button' onClick='paiementSuite(3,1," + montantCB + "," + resteAPayer + ")' class='btn btn-warning btnPaiement' id='btnPaiementSuiteCheques'>Cheque</button>");
    }
    else if(total === 0){
        Toast.fire({
            icon: 'error',
            title: "Impossible d'encaisser un panier à 0 €"
        })
    }
}

$("#inputMontantCB").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        paiementCB()
    }
});


// PAIEMENT CHEQUES
$('#paiementCheque').click(function () {
    var total = getTotal();
    $('#inputMontantCheque').select()
    $('#inputMontantCheque').val(total)
    $('#montantCheque').html(total)

})

function paiementCheque(){
    var montantCheque = $('#inputMontantCheque').val();
    var total = getTotal();

    if (montantCheque == total  && montantCheque > 0) {
        $.ajax({
            url: "../tickets/ajoutTicket.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                "espece": 0,
                "cb": 0,
                "cheques": montantCheque,
                "ticket_restaurant": 0,
                "total": total,
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response === 1) {
                    $('#modal-cheque').modal('hide')
                    $('#total').text('0.00 €')
                    $("#searchArticle").load(location.href + " #searchArticle");
                    imprimeTicket(result.ticket,result.ticket_part2,result.arendre,result.footer,result.espece,result.cb,result.cheque,result.ticket_pied)
                    clearPanier(result.session, result.id_caisse)
                }
            }
        })
    } else if (montantCheque < total) {
        var resteAPayer = total - montantCheque;
        resteAPayer = resteAPayer.toFixed(2)
        $('#modal-cheque > .modal-dialog > .modal-content > .modal-header > .modal-title').html("SUITE PAIEMENT");
        $('#modal-cheque > .modal-dialog > .modal-content > .modal-body > .text-paiement').html("<p>Reste a payer:  <span class='resteAPayer'>" + resteAPayer + " €</span> </br>Choisir une méthode de paiement</p>");
        $('#modal-cheque > .modal-dialog > .modal-content > .modal-body > .inputPaiement')
        .html("<button type='button' class='btn btn-success btnPaiement' onClick='paiementSuite(1,2," + montantCheque + "," + resteAPayer + ")' id='btnPaiementSuiteCheque'>Espece</button><button type='button' class='btn btn-info btnPaiement' onClick='paiementSuite(2,1," + montantCheque + "," + resteAPayer + ")' id='btnPaiementSuiteCheque'>CB</button><button type='button' onClick='paiementSuite(3,1," + montantCheque + "," + resteAPayer + ")' class='btn btn-warning btnPaiement' id='btnPaiementSuiteCheques'>Cheque</button>");
    }
}

$("#inputMontantCheque").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        paiementCheque()
    }
});


// PRODUIT DIVERS

function addProduitDivers(session, idcaisse) {
    var prixDivers = $('#inputPrixDivers').val()
    var tvaDivers = $('#inputTvaDivers').val()
    var qteDivers = $('#inputQTEDivers').val()

    $.ajax({
        url: '../panier.php',
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            articleDivers: prixDivers,
            tvaDivers: tvaDivers,
            qteDivers: qteDivers,
            session: session,
            idcaisse: idcaisse,
        }),
        success: function (data) {
            var result = JSON.parse(data)
            prixDivers = prixDivers.toString()
            console.log(prixDivers)
            if (result.response === 1) {
                var produit = result.data
                var total = result.total
                console.log(total)
                var montantDiver = parseFloat(prixDivers) * produit.qte
                $('#total').html(total.toFixed(2) + " €")
                $('#caddie').prepend(
                    '<div class="callout callout-info" style="margin:15px 30px 0 30px">\n' +
                    '<div class="row">' +
                    '<div class="col-md-4">' +
                    '<p style="font-size:20px;font-weight:600;">' +
                    produit.titre.toUpperCase() +
                    '<i class="fa fa-trash text-red" style="cursor:pointer;" onclick="deleteArticle(this.id,' + session + ',' + idcaisse + ')" id="deleteProduit-' + produit.ref + '"></i>' +
                    '</p>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<input type="text" onclick="this.select()" style="width: 50px;" name="quantiteProduit" id="quantiteProduit-' + produit.ref + '" value="' + produit.qte + '" />' +
                    '</div>' +
                    '<div class="col-md-1">' +
                    '<p style="font-size:22px;">' +
                    parseFloat(prixDivers) +
                    '€</p>' +
                    '</div>' +
                    '<div class="col-md-1">' +
                    '<p style="font-size:22px;">' +
                    montantDiver.toFixed(2) +
                    '€</p>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<input type="text" style="width: 50px;" onclick="this.select()" name="remiseProduit" id="remiseProduit-' + produit.ref + '" value="' + produit.remise + '" /><span>%</span>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<p>' +
                    '<input type="text" style="width: 50px;" onclick="this.select()" name="remiseEuro" id="remiseEuro-' + produit.ref + '-'+prixDivers+'" value="' + produit.remise_euro + '" /><span>€</span>' +
                    '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>')
                $('#inputPrixDivers').val('')
                $('#inputQTEDivers').val('1')
                $('#inputTvaDivers').val('8.5')
                $('#modal-divers').modal('hide')

            }
        }

    })
}

$("#inputPrixDivers").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        addProduitDivers($('#diversSession').val(), $('#diversIDCAISSE').val())
    }
});

$("#inputQTEDivers").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        addProduitDivers($('#diversSession').val(), $('#diversIDCAISSE').val())
    }
});

$("#inputTvaDivers").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        addProduitDivers($('#diversSession').val(), $('#diversIDCAISSE').val())
    }
});


// RETOUR ARTICLE
$('#showRetArticleCatalogue').click(function () {
    $('#retourArticleCatalogue').show()
    $('#inputRetourArticleCatalogue').focus();
    $('#retourArticleDivers').hide()
    $('#retourArticleChoix').hide();
    $('#btnRetCatalogue').show()
    $('#btnRetDivers').hide()

})


function retourArticleCatalogue(session, idcaisse, event) {
    console
    if (event.which == 13) {
        var ref = event.target.value;
        $.ajax({
            url: '../panier.php',
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                retourArticle: true,
                retourArticleCatalogue: ref,
                session: session,
                idcaisse: idcaisse,
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response === 1) {
                    window.location.reload()
                }
            }

        })
    }
}

$('#showRetArticleDivers').click(function () {
    $('#retourArticleDivers').show()
    $('#inputRetourPrixDivers').focus();
    $('#retourArticleCatalogueur').hide()
    $('#retourArticleChoix').hide()

})

function retourArticleDivers(session, idcaisse, event) {
    var retourDiversPrix = $('#inputRetourPrixDivers').val()
    var retourQteDivers = $('#inputRetourQTEDivers').val()
    var retourTvaDivers = $('#inputRetourTvaDivers').val()

    if (retourDiversPrix !== "") {
        $.ajax({
            url: '../panier.php',
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                retourArticle: true,
                retourArticleDivers: retourDiversPrix,
                retourQteDivers: retourQteDivers,
                retourTvaDivers: retourTvaDivers,
                session: session,
                idcaisse: idcaisse,
            }),
            success: function (data) {
                var result = JSON.parse(data)
                if (result.response === 1) {
                    window.location.reload()
                }
            }

        })

    }
    else{
        $('#erreurRetArticleDivers').text("Le champs prix ne peut pas être vide ! Veuillez entrez un prix.")
    }
}

