/**
 * The js script is to make some radios in the lease_fill.html to be uncheckable, just like checkbox button.
 */

var radio=document.getElementById('service_heating_1');
uncheck(radio);
var radio=document.getElementById('service_heating_2');
uncheck(radio);

var radio=document.getElementById('water_consumption_1');
uncheck(radio);
var radio=document.getElementById('water_consumption_2');
uncheck(radio);

var radio=document.getElementById('gas_1');
uncheck(radio);
var radio=document.getElementById('gas_2');
uncheck(radio);

var radio=document.getElementById('electricity_1');
uncheck(radio);
var radio=document.getElementById('electricity_2');
uncheck(radio);

var radio=document.getElementById('hot_water_heater_1');
uncheck(radio);
var radio=document.getElementById('hot_water_heater_2');
uncheck(radio);

var radio=document.getElementById('hot_water_1');
uncheck(radio);
var radio=document.getElementById('hot_water_2');
uncheck(radio);

var radio=document.getElementById('sonwrm_parking_1');
uncheck(radio);
var radio=document.getElementById('sonwrm_parking_2');
uncheck(radio);

var radio=document.getElementById('snowrm_balcony_1');
uncheck(radio);
var radio=document.getElementById('snowrm_balcony_2');
uncheck(radio);

var radio=document.getElementById('snowrm_entrance_1');
uncheck(radio);
var radio=document.getElementById('snowrm_entrance_2');
uncheck(radio);

var radio=document.getElementById('snowrm_stars_1');
uncheck(radio);
var radio=document.getElementById('snowrm_stars_2');
uncheck(radio);

var radio=document.getElementById('restriction_immovable_1');
uncheck(radio);
var radio=document.getElementById('restriction_immovable_2');
uncheck(radio);



//the method to deal with unchecking
function uncheck(radio) {
    //method1:double click
   radio.ondblclick=function () {
       if(radio.checked)
           radio.checked=false;
   };
   //method2:press alt with clicking
   radio.onclick=function (e) {
       if(e.altKey){
           if(radio.checked)
               radio.checked=false;
       }
   };
}

