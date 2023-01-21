<html>
<head>
  <title>Checkout</title>
  <style>
    .vcenter {

      vertical-align: middle;

    }
  </style>
</head>

<body>
<br><br>
<form method="post" class="form-horizontal" action="custom/checkout.php">
  <input type="hidden" name="product" id="product" value="Rent">
  <input type="hidden" name="lease_payment_id" id="lease_payment_id" value="<?= $lease_payment_id ?>">
  <input type="hidden" name="price" id="price" value="<?= $outstanding ?>">
  <input type="hidden" name="gateway" id="gateway" value="">

  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12"><p>You are going to pay your Kijiji slots:</p><br><br></div>
    </div>
    <div class="row">
      <div class="col-sm-2 col-md-2 col-lg-2">User ID:</div>
      <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $owner_name ?></strong><br><br></div>
    </div>
    <div class="row">
      <div class="col-sm-2 col-md-2 col-lg-2">User Name:</div>
      <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $building_name ?></strong><br><?= $building_address ?>
        <br><br></div>
    </div>
    <div class="row">
      <div class="col-sm-2 col-md-2 col-lg-2">Slots Number:</div>
      <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $outstanding ?> $ CAD</strong><br><br></div>
    </div>
    <div class="row">
      <div class="col-sm-2 col-md-2 col-lg-2">Period:</div>
      <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $unit_number ?></strong><br><br></div>
    </div>

    <div class="row">
      <div class="col-sm-2 col-md-2 col-lg-2">Amount:</div>
      <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $outstanding ?> $ CAD</strong><br><br></div>
    </div>

    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-3 col-md-3 col-lg-3">Pay By</div>
      <div class="col-sm-2 col-md-2 col-lg-2">Convenience Fee</div>
      <div class="col-sm-2 col-md-2 col-lg-2">Total<br><br></div>
    </div>

    <div class="row">
      <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway" id="gateway1" value="p1"
                                                             required> Paypal Balance
      </div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter">0</div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b><?= $outstanding ?></b> $ CAD <input type="hidden"
                                                                                              name="outstanding1"
                                                                                              value="<?= $outstanding ?>">
      </div>
      <div class="col-sm-5 col-md-5 col-lg-5"><img src="../../phpimages/paypal64.png"></div>
    </div>
    <div class="row">
      <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway" id="gateway2" value="p2"
                                                             required> Paypal Credit Card
      </div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><?= $convenience_fee ?>%</div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter">
        <b><?= $outstanding + round($outstanding * $convenience_fee / 100) ?></b> $ CAD <input type="hidden" name="outstanding2" value="<?= $outstanding + round($outstanding * $convenience_fee / 100) ?>">
      </div>
      <div class="col-sm-5 col-md-5 col-lg-5"><img src="../../phpimages/paypal64.png"> <img src="../../phpimages/visa64.png"> <img src="../../phpimages/mastercard64.png"></div>
    </div>
    <div class="row">
      <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway" id="gateway3" value="m1" required> Moneris Credit Card
      </div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><?= $convenience_fee ?>%</div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter">
        <b><?= $outstanding + round($outstanding * $convenience_fee / 100) ?></b> $ CAD
      </div>
      <input type="hidden" name="outstanding3"
             value="<?= $outstanding + round($outstanding * $convenience_fee / 100) ?>">
      <div class="col-sm-5 col-md-5 col-lg-5"><img src="../../phpimages/moneris.png"> <img src="../../phpimages/visa64.png"> <img src="../../phpimages/mastercard64.png"></div>
    </div>
    <div class="row">
      <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway" id="gateway4" value="m2"
                                                             required> Moneris Intract
      </div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter">2$</div>
      <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b><?= $outstanding + 2 ?></b> $ CAD <input type="hidden" name="outstanding4" value="<?= $outstanding + 2 ?>">
      </div>
      <div class="col-sm-5 col-md-5 col-lg-5"><img src="../../phpimages/moneris.png"> <img src="../../phpimages/interac.png" height="64"></div>
    </div>


    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <hr>
      </div>
    </div>
    <div class="row">
      <button id="form-submit" class="btn btn-primary">Pay Now!</button>
      <button class="btn" aria-hidden="true" aria-label="Close" onclick="window.close()">Cancel</button>
    </div>
  </div>
</form>
</body>
</html>


