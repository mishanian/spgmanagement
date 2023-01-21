<?

namespace PHPMaker2023\spgmanagement; ?>loadjs.ready("head", function() { <?
                                                                            $GST = ExecuteScalar("select tax1 from provinces where id=1");
                                                                            $QST = ExecuteScalar("select tax2 from provinces where id=1");
                                                                            echo "var gst=$GST;\n";
                                                                            echo "var qst=$QST;\n";
                                                                            ?>



<? if (CurrentTable()->TableName == "invoice_infos" || CurrentTable()->TableName == "payment_infos" || CurrentTable()->TableName == "view_shared_payment_infos") { ?>
    //$("#r_material_amount, #r_material_gst_amount, #r_material_qst_amount, #r_material_tax_amount, #r_material_wtax_amount").css("background-color","skyblue");
    //$("#r_gst_amount, #r_qst_amount, #r_tax_amount, #r_amount_wo_tax, #r_amount").css("background-color","mediumseagreen");

    $("#x_labor_amount, #x_material_amount, #x_amount_wo_tax").change(function(){calcGrandTotal(0);});
    $("#x_amount").change(function(){calcGrandTotal(1);});
    $("input[type=checkbox][name='x_is_labor[]'], input[type=checkbox][name='x_is_tax[]'], input[type=radio][name='x_material_by_owner']").click(function(){calcGrandTotal(0);});
    <?
    if (CurrentUserLevel() != 14) { //vendor
    ?>
    <?
    } //if vendor
    ?>
    if ($("#x_amount").val()=="0"){calcGrandTotal(0);}else{calcGrandTotal(1);}

    function calcGrandTotal(startFrom){

    var laborObj = $("input[type=checkbox][name='x_is_labor[]']:checked");
    var taxObj = $("input[type=checkbox][name='x_is_tax[]']:checked");
    var materialOwnerObj = $("input[type=radio][name='x_material_by_owner']:checked");
    var is_labor = getValueUsingClass(laborObj);
    var is_tax = getValueUsingClass(taxObj);
    var material_by_owner = getValueUsingClass(materialOwnerObj);
    gst_yes=0;
    qst_yes=0;
    tax_gst_qst=0;
    if (startFrom==0) { // Start WO Tax
    amount=0;
    tax_amount=0;
    amount_wo_tax=0;
    labor_gst_amount=0;
    material_gst_amount=0;
    labor_qst_amount=0;
    material_qst_amount=0;
    labor_tax_amount=0;
    labor_wtax_amount=0;
    material_tax_amount=0;
    material_wtax_amount=0;
    gst_amount=0;
    qst_amount=0;


    if (is_labor=="1" ){labor_amount=parseFloat($("#x_labor_amount").val());}else{labor_amount=0;}
    if ( (is_labor=="2") ){material_amount=parseFloat($("#x_material_amount").val());}else{material_amount=0;} //&& material_by_owner==0

    //alert(is_labor+"- l="+labor_amount+" m="+material_amount);
    //GST

    if(is_tax=="1" || is_tax=="1,2"){labor_gst_amount=round(labor_amount*gst/100,2);}
    if(is_tax=="1" || is_tax=="1,2"){material_gst_amount=round(material_amount*gst/100,2);}
    $("#x_labor_gst_amount").val(labor_gst_amount);
    $("#x_material_gst_amount").val(material_gst_amount);

    //QST
    if(is_tax=="2" || is_tax=="1,2"){labor_qst_amount=round(labor_amount*qst/100,2);} // else{labor_wtax_amount=labor_amount;}
    if(is_tax=="2" || is_tax=="1,2"){material_qst_amount=round(material_amount*qst/100,2);} // else{labor_wtax_amount=labor_amount;}
    $("#x_labor_qst_amount").val(labor_qst_amount);
    $("#x_material_qst_amount").val(material_qst_amount);
    labor_tax_amount=round(labor_gst_amount+labor_qst_amount,2);
    material_tax_amount=round(material_gst_amount+material_qst_amount,2);
    $("#x_labor_tax_amount").val(labor_tax_amount);
    $("#x_material_tax_amount").val(material_tax_amount);
    labor_wtax_amount=round(labor_amount+labor_tax_amount,2);
    material_wtax_amount=round(material_amount+material_tax_amount,2);
    $("#x_labor_wtax_amount").val(labor_wtax_amount);
    $("#x_material_wtax_amount").val(material_wtax_amount);


    if (labor_amount != 0 || material_amount != 0) {
    // alert(parseInt($("#x_amount_wo_tax").val()));
    amount_wo_tax += round(material_amount + labor_amount, 2);
    } else {
    amount_wo_tax = parseFloat($("#x_amount_wo_tax").val(), 2);
    }


    if (is_tax == "1" || is_tax == "1,2") {
    gst_amount = round(amount_wo_tax * gst / 100, 2);
    }
    $("#x_gst_amount").val(gst_amount);
    if (is_tax == "2" || is_tax == "1,2") {
    qst_amount = round(amount_wo_tax * qst / 100, 2);
    }
    $("#x_qst_amount").val(qst_amount);
    tax_amount = round(gst_amount + qst_amount, 2);
    $("#x_tax_amount").val(tax_amount);

    amount += amount_wo_tax + tax_amount;
    amount = round(amount, 2);
    // alert("Is Tax=" + is_tax + "=>Gst=" + gst_amount + " - QST=" + qst_amount + " - Tax=" + tax_amount + " AWT=" + amount_wo_tax + " Total Amount=" + amount);
    $("#x_amount").val(amount);

    }else { // Start With Tax
    gst_amount=0;
    qst_amount=0;
    tax_amount=0;



    amount=parseFloat($("#x_amount").val().replace(",",""),2);
    if (is_tax == "1" || is_tax == "1,2") {
    gst_yes=1;
    tax_gst_qst +=gst;
    }


    if (is_tax == "2" || is_tax == "1,2") {
    qst_yes=1;
    tax_gst_qst +=qst;
    }


    // alert(amount / (gst+qst)*100);
    // tax_gst_qst=gst+qst;
    amount_wo_tax=round((100*amount)/(100+(tax_gst_qst)),2);
    $("#x_amount_wo_tax").val(amount_wo_tax);
    tax_amount=amount-amount_wo_tax;
    $("#x_tax_amount").val(tax_amount);
    gst_amount=round(amount_wo_tax*gst/100,2);
    qst_amount=round(amount_wo_tax*qst/100,2);
    $("#x_gst_amount").val(gst_amount);
    $("#x_qst_amount").val(qst_amount);

    }
    $(".ewFooterText").html("startFrom"+startFrom+" Is Labor="+is_labor+" Is Tax=" + is_tax + "=> tax_gst_qst="+tax_gst_qst+" gst_yes=" + gst_yes + " Gst="+gst+", qst_yes=" + qst_yes + " qst="+qst+" Gst amount=" + gst_amount + " - QST=" + qst_amount + " - Tax=" + tax_amount + " AWT=" + amount_wo_tax + " Total Amount=" + amount);





    //labor

    labor_fields = ["labor_amount", "labor_wtax_amount"];
    labor_gst_fields = ["labor_gst_amount", "labor_tax_amount"];
    labor_qst_fields = ["labor_qst_amount", "labor_tax_amount"];
    jQuery.each(labor_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "moccasin");
    if ( (is_labor=="1" )){$("#r_" + fld).show();}else{$("#r_" + fld).hide();$("#x_" + fld).val(0);}
    });
    jQuery.each(labor_gst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "moccasin");
    if ( (is_labor=="1" || is_labor=="1,2") && (is_tax=="1" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();;$("#x_" + fld).val(0);}
    });
    jQuery.each(labor_qst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "moccasin");
    if ( (is_labor=="1" || is_labor=="1,2") && (is_tax=="2" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();;$("#x_" + fld).val(0);}
    });

    //material
    material_fields = ["material_amount", "material_wtax_amount"];
    material_gst_fields = ["material_gst_amount", "material_tax_amount"];
    material_qst_fields = ["material_qst_amount", "material_tax_amount"];
    jQuery.each(material_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "skyblue");
    if ( (is_labor=="2" )){$("#r_" + fld).show();}else{$("#r_" + fld).hide();;$("#x_" + fld).val(0);}
    });
    jQuery.each(material_gst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "skyblue");
    if ( (is_labor=="2" || is_labor=="1,2") && (is_tax=="1" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();;$("#x_" + fld).val(0);}
    });
    jQuery.each(material_qst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "skyblue");
    if ( (is_labor=="2" || is_labor=="1,2") && (is_tax=="2" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();;$("#x_" + fld).val(0);}
    });


    //Labor+Material
    if ( is_labor=="1,2"){$('#r_amount_wo_tax').show();}else{$('#r_amount_wo_tax').hide();}



    //GST QST

    qst_fields = ["qst_amount", "tax_amount"];
    gst_fields = ["gst_amount", "tax_amount"];
    jQuery.each(gst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "mediumseagreen");
    if ( (is_tax=="1" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();}
    });
    jQuery.each(qst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "mediumseagreen");
    if ( (is_tax=="2" || is_tax=="1,2") ){ $("#r_" + fld).show();}else{$("#r_" + fld).hide();}
    });
    hideFields=["labor_tax_amount","material_tax_amount","labor_wtax_amount","tax_amount","labor_gst_amount",
    "labor_qst_amount", "labor_tax_amount","material_wtax_amount","material_gst_amount","material_qst_amount", "material_tax_amount"]; //"amount_wo_tax",
    jQuery.each(hideFields, function (i, fld) {
    $("#r_" + fld).hide();
    });
    $("#r_amount_wo_tax").css("background-color", "lightseagreen");
    $("#r_amount").css("background-color", "mediumseagreen");
    }
<? } //if Invoice or Payments
?>


<? if (CurrentTable()->TableName == "contract_infos" || CurrentTable()->TableName == "proposal_infos" || CurrentTable()->TableName == "book_infos") {

    if (CurrentTable()->TableName == "contract_infos" || CurrentTable()->TableName == "proposal_infos") {
        echo "var total_field='contract_price';\n";
    } else if (CurrentTable()->TableName == "book_infos") {
        echo "var total_field='amount';\n";
    }
?>
    $("#x_amount_wo_tax").change(function () {
    calcGrandTotal(0);
    });
    $("#x_"+total_field).change(function () {
    calcGrandTotal(1);
    });
    $("input[type=checkbox][name='x_is_tax[]']").click(function(){calcGrandTotal(0);});
    <? if (CurrentPageID() == "add" || CurrentPageID() == "edit") { ?>calcGrandTotal(0);<? } ?>




    function calcGrandTotal(startFrom) {
    var taxObj = $("input[type=checkbox][name='x_is_tax[]']:checked");
    var is_tax = getValueUsingClass(taxObj);
    gst_amount = 0;
    qst_amount = 0;
    amount = 0;
    tax_gst_qst=0;
    gst_yes=0;
    qst_yes=0;


    if (startFrom==0) { // Start WO Tax
    amount_wo_tax = parseFloat($("#x_amount_wo_tax").val().replace(",", ""), 2);

    if (is_tax == "1" || is_tax == "1,2") {
    gst_amount = round(amount_wo_tax * gst / 100, 2);
    }
    $("#x_gst_amount").val(gst_amount);
    if (is_tax == "2" || is_tax == "1,2") {
    qst_amount = round(amount_wo_tax * qst / 100, 2);
    }
    $("#x_qst_amount").val(qst_amount);
    tax_amount = round(gst_amount + qst_amount, 2);
    $("#x_tax_amount").val(tax_amount);

    amount += amount_wo_tax + tax_amount;
    amount = round(amount, 2);
    }else{ //if (startFrom==0) { // Start WO Tax

    gst_amount=0;
    qst_amount=0;
    tax_amount=0;



    amount=parseFloat($("#x_"+total_field).val().replace(",",""),2);
    if (is_tax == "1" || is_tax == "1,2") {
    gst_yes=1;
    tax_gst_qst +=gst;
    }


    if (is_tax == "2" || is_tax == "1,2") {
    qst_yes=1;
    tax_gst_qst +=qst;
    }


    // alert(amount / (gst+qst)*100);
    // tax_gst_qst=gst+qst;
    amount_wo_tax=round((100*amount)/(100+(tax_gst_qst)),2);
    $("#x_amount_wo_tax").val(amount_wo_tax);
    tax_amount=amount-amount_wo_tax;
    $("#x_tax_amount").val(tax_amount);
    gst_amount=round(amount_wo_tax*gst/100,2);
    qst_amount=round(amount_wo_tax*qst/100,2);
    $("#x_gst_amount").val(gst_amount);
    $("#x_qst_amount").val(qst_amount);

    }
    // alert("Is Tax=" + is_tax + "=>Gst=" + gst_amount + " - QST=" + qst_amount + " - Tax=" + tax_amount + " AWT=" + amount_wo_tax + " Total Amount=" + amount);
    $("#x_"+total_field).val(amount);
    $(".ewFooterText").html("startFrom"+startFrom+" Is Tax=" + is_tax + "=> tax_gst_qst="+tax_gst_qst+" gst_yes=" + gst_yes + " Gst="+gst+", qst_yes=" + qst_yes + " qst="+qst+" Gst amount=" + gst_amount + " - QST=" + qst_amount + " - Tax=" + tax_amount + " AWT=" + amount_wo_tax + " Total Amount=" + amount);



    //GST QST

    qst_fields = ["qst_amount", "tax_amount"];
    gst_fields = ["gst_amount", "tax_amount"];
    jQuery.each(gst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "mediumseagreen");
    if ((is_tax == "1" || is_tax == "1,2")) {
    $("#r_" + fld).show();
    } else {
    $("#r_" + fld).hide();
    }
    });
    jQuery.each(qst_fields, function (i, fld) {
    $("#r_" + fld).css("background-color", "mediumseagreen");
    if ((is_tax == "2" || is_tax == "1,2")) {
    $("#r_" + fld).show();
    } else {
    $("#r_" + fld).hide();
    }
    });
    hideFields = ["tax_amount"]; //"amount_wo_tax",
    jQuery.each(hideFields, function (i, fld) {
    $("#r_" + fld).hide();
    });
    $("#r_amount_wo_tax").css("background-color", "lightseagreen");

    $("#r_x_"+total_field).css("background-color", "mediumseagreen");
    }
<? } //contract
?>


function getValueUsingClass(laborObj){
/* declare an checkbox array */
var chkArray = [];

/* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
laborObj.each(function() {
chkArray.push($(this).val());
});

/* we join the array separated by the comma */
var selected;
selected = chkArray.join(',') ;

/* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
return selected;
}

function round(value, decimals) {
return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}
});