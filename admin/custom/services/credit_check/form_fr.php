<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
  <title>SPG Canada</title>
  <link rel="icon" type="image/PNG" href="logo.PNG" />
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body>
  <div class="container">

    <form class="well form-horizontal" action="action.php" method="post"  id="investigation_form">
      <fieldset>

        <!-- Form Name -->

        <legend><img src="logo.PNG" alt="logo" style="width:100px;height:80px;">Credit Conseil<a class="language" href="form.php">English</a><a class="language" href="form_cn.php">中文|</a></legend>

        <div class="form-group">
          <label class="col-md-12 control-label-info">100-1650 boul Rene Levesque West, Montreal, Quebec (H3H 2S1)</label>
          <label class="col-md-12 control-label-info">Intercom: 8888 Tel: 514-937-3529 Fax: (855) 666-6364</label>
          <label class="col-md-12 control-label-info">info@spg-canada.com (Mon-Fri 09:00-13:00 and 15:00-18:00, Sat 11:00-13:00)</label>
          <label class="col-md-12 control-label-info">S.V.P. Remplir tous les * champs ou demande vous sera retournée sans avoir été traitée.</label>
        </div>

        <legend>Dwelling ID</legend>


        <div class="form-group">
          <label class="col-md-2 control-label">Adresse Demandé</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_address" class="form-control" type="text" autocomplete="off" >
            </div>
          </div>
          <label class="col-md-2 control-label">Apt</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_apt" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">Ville</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Province</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <select name="dwelling_province" class="form-control" type="text" autocomplete="off">
                  <option value="AB">AB</option>
                  <option value="BC">BC</option>
                  <option value="MB">MB</option>
                  <option value="NB">NB</option>
                  <option value="NL">NL</option>
                  <option value="NS">NS</option>
                  <option value="ON">ON</option>
                  <option value="PE">PE</option>
                  <option value="QC">QC</option>
                  <option value="SK">SK</option>
                  <option value="NT">NT</option>
                  <option value="NU">NU</option>
                  <option value="YT">YT</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">Code Postal</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <legend>Prospective Tenant</legend>


        <div class="form-group">
          <label class="col-md-2 control-label">*Nom de Famille</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_surname" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">Permis de Conduire</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_driverid" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-10 control-label">État</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="tenant_status" class="form-control" type="text" autocomplete="off">
                  <option value="Married">Marié</option>
                  <option value="Separated">Séparé</option>
                  <option value="Single">Célibataire</option>
                  <option value="Divorced">Divorcé</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">*Prénom</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_firstname" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">Passport</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_passportid" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Nationality</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_nationality" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">Tél</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="tenant_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Sexe</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="tenant_gender" class="form-control" type="text" autocomplete="off">
                  <option value="Female">Femme</option>
                  <option value="Male">Homme</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">No. de NAS</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_sinno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">*Date de Naissance</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group" data-provide="datepicker">
              <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
              <input name="tenant_dateofbirth" class="form-control" type="date" autocomplete="off" required>   
            </div>
          </div>
          <label class="col-md-4 control-label">*Curriel Électronique</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input name="tenant_email" class="form-control" type="email" autocomplete="off" data-error="The email address is invalid" required>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">*Adresse Actuelle</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_address" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">Apt</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_apt" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">Ville</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Province</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <select name="tenant_province" class="form-control" type="text" autocomplete="off">
                  <option value="AB">AB</option>
                  <option value="BC">BC</option>
                  <option value="MB">MB</option>
                  <option value="NB">NB</option>
                  <option value="NL">NL</option>
                  <option value="NS">NS</option>
                  <option value="ON">ON</option>
                  <option value="PE">PE</option>
                  <option value="QC">QC</option>
                  <option value="SK">SK</option>
                  <option value="NT">NT</option>
                  <option value="NU">NU</option>
                  <option value="YT">YT</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">Code Postal</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Depuis combien de temps y résider</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_howlong" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Votre nom sur le bail</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_onlease" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Nom du Propriétaire</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_landlord" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">*Tél du Propriétaire</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="tenant_tellandlord" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">*Date de Fin du Bail Actuel</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_endlease" class="form-control" type="date" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-4 control-label">*Loyer Actuel</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_rent" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>



        <legend>Les Personnes à Charge</legend>

        <div class="form-group">
          <label class="col-md-2 control-label">Nombre</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="dependent_number" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Sexe</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="dependent_gender" class="form-control" type="text" autocomplete="off">
                  <option value="Female">Femme</option>
                  <option value="Male">Homme</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">Âge</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="dependent_age" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <legend>Employment</legend>

        <div class="form-group">
          <label class="col-md-2 control-label">Nom de l'Employeur</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_name" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Tél</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="employment_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Adresse</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_address" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Unite</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_unit" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Ville</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">Province</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <select name="employment_province" class="form-control" type="text" autocomplete="off">
                  <option value="AB">AB</option>
                  <option value="BC">BC</option>
                  <option value="MB">MB</option>
                  <option value="NB">NB</option>
                  <option value="NL">NL</option>
                  <option value="NS">NS</option>
                  <option value="ON">ON</option>
                  <option value="PE">PE</option>
                  <option value="QC">QC</option>
                  <option value="SK">SK</option>
                  <option value="NT">NT</option>
                  <option value="NU">NU</option>
                  <option value="YT">YT</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">Code Postal</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Titre de la Profession</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_occupation" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Salaire Mensuel</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_salary" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Combien de temps y travaillé</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_howlong" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Autre ressource de revenu</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_other" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">          
          <label class="col-md-9 control-label">Avez-vous le plus récenttalon de paie? Si oui, svp envoyer à <a href="mailto:info@spg-canada.com" target="_top">info@spg-canada.com</a>.</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="employment_paystub" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
              </select>
            </div>
          </div>
        </div>

        <legend>Banking</legend>


        <div class="form-group">
          <label class="col-md-11 control-label"><I class="span_banking">Vous pouvez aussi nous fournir un spécimen de chèque à votre nom par courrier électronique, vous n'avez pas besoin de remplir les informations bancaires ci-dessous.</I></label>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Nom de la Banque</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_name" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Adresse de la Succursale de Banque</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_address" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Numéro de Compte</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_accountno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Numéro de Transit</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_transit" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Numéro de l'Institution</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_institution" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Tél</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="banking_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Compagnie de Carte de Crédit</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_company" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">*Limite de Crédit</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_limit" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>

        <div class="form-group">          
          <label class="col-md-4 control-label">Type</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="banking_payment" class="form-control" type="text" autocomplete="off">
                  <option value="VISA">VISA</option>
                  <option value="Master Card">Master Card</option>
                  <option value="American Express">American Express</option>
                  <option value="VISA-Debit">VISA-Debit</option>
              </select>
            </div>
          </div>
          <label class="col-md-4 control-label">Type d'Assurance</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="banking_insurancetype" class="form-control" type="text" autocomplete="off">
                  <option value="Auto">Auto</option>
                  <option value="Home">Home</option>
                  <option value="Life">Life</option>
              </select>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">Nom de la Compagnie d'Assurance</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_insurance" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Numéro de Police</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_policyno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <legend>HydroQuebec</legend>

        <div class="form-group">
          <label class="col-md-11 control-label"><I class="span_banking">Pour rendre votre déménagement plus lisse, nous fournir votre numéro de compte HydroQuebec pour nous contacter HydroQuebec.</I></label>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label">No. de Compte Hydro</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_hydroquebec" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">Date d'Occupation</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="banking_movingdate" class="form-control" type="date" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-7 control-label">Compte est actif à l'adresse mentionnée ci-dessus en cours.</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="banking_active" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Oui</option>
                  <option value="No">Non</option>
              </select>
            </div>
          </div>
        </div>


        <legend>Questions</legend>


        <div class="form-group">
          <label class="col-md-6 control-label">Avez-vous déjà été reconnu coupable d'un crime?</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_crime" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Oui</option>
                  <option value="No">Non</option>
              </select>
            </div>
          </div>
          <label class="col-md-6 control-label">Avez-vous déjà déclaré faillite?</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_bankruptcy" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Oui</option>
                  <option value="No">Non</option>
              </select>
            </div>
          </div>
        </div>



        <div class="form-group">
          <label class="col-md-7 control-label">Avez-vous déjà été l'ouverture d'un fichier dans la Régie du logement du Québec?</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_file" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Oui</option>
                  <option value="No">Non</option>
              </select>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-7 control-label">Avez-vous déjà été refusé par une société de carte de crédit, si oui, lequel crédit card.</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_declined" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">Oui</option>
                  <option value="No">Non</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-7 control-label">Si oui, nom de la Société de Carte de Crédit.</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="question_creditcompany" class="form-control" type="text">
            </div>
          </div>
        </div>


        <!-- Success message -->
        <div class="alert alert-success" role="alert" id="success_message">Success <i class="glyphicon glyphicon-thumbs-up"></i> Thanks for contacting us, we will send you a document to your e-mail address for you to sign.</div>

        <!-- Button -->
        <div class="form-group">
          <label class="col-md-5 control-label"></label>
          <div class="col-md-7">
            <button type="submit" class="btn btn-warning btn-size" name="form_submit">Soumettre<span class="glyphicon glyphicon-send"></span></button>
          </div>
        </div>

      </fieldset>
    </form>
  </div>
</div><!-- /.container -->

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<!--
<script src="js/validation.js"></script>
-->
</body>
</html>