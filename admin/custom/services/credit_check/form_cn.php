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

        <legend><img src="logo.PNG" alt="logo" style="width:100px;height:80px;">信用调查<a class="language" href="form.php">English</a><a class="language" href="form_fr.php">Français|</a></legend>

        <div class="form-group">
          <label class="col-md-12 control-label-info">100-1650 boul Rene Levesque West, Montreal, Quebec (H3H 2S1)</label>
          <label class="col-md-12 control-label-info">Intercom: 8888 Tel: 514-937-3529 Fax: (855) 666-6364</label>
          <label class="col-md-12 control-label-info">info@spg-canada.com (Mon-Fri 09:00-13:00 and 15:00-18:00, Sat 11:00-13:00)</label>
          <label class="col-md-12 control-label-info">请填写所有带 * 的项目。</label>
        </div>

        <legend>住宅信息</legend>


        <div class="form-group">
          <label class="col-md-2 control-label">申请地址</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_address" class="form-control" type="text" autocomplete="off" >
            </div>
          </div>
          <label class="col-md-2 control-label">房间号</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_apt" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">城市</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">省份</label>
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
          <label class="col-md-2 control-label">邮编</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="dwelling_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <legend>租客信息</legend>


        <div class="form-group">
          <label class="col-md-2 control-label">*姓</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_surname" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">驾照ID</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_driverid" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-10 control-label" style="font-size: 12px;">状态</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="tenant_status" class="form-control" type="text" autocomplete="off">
                  <option value="Married">已婚</option>
                  <option value="Separated">分居</option>
                  <option value="Single">单身</option>
                  <option value="Divorced">离异</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">*名</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_firstname" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">护照ID</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_passportid" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">国籍</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_nationality" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">电话</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="tenant_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">性别</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="tenant_gender" class="form-control" type="text" autocomplete="off">
                  <option value="Female">女</option>
                  <option value="Male">男</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">SIN号码</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_sinno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">*生日</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group" data-provide="datepicker">
              <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
              <input name="tenant_dateofbirth" class="form-control" type="date" autocomplete="off" required>   
            </div>
          </div>
          <label class="col-md-4 control-label">*电子邮箱</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input name="tenant_email" class="form-control" type="email" autocomplete="off" data-error="The email address is invalid" required>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">*当前地址</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_address" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-2 control-label">房间号</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_apt" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-2 control-label">城市</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">省份</label>
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
          <label class="col-md-2 control-label">邮编</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">住了多久?</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_howlong" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">合约上的名字</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_onlease" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">房东姓名</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="tenant_landlord" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">*房东电话</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="tenant_tellandlord" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">*当前合约结束日期</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_endlease" class="form-control" type="date" autocomplete="off" required>
            </div>
          </div>
          <label class="col-md-4 control-label">*当前租金</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="tenant_rent" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>



        <legend>抚养人</legend>

        <div class="form-group">
          <label class="col-md-2 control-label">数量</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="dependent_number" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">性别</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="dependent_gender" class="form-control" type="text" autocomplete="off">
                  <option value="Female">女</option>
                  <option value="Male">男</option>
              </select>
            </div>
          </div>
          <label class="col-md-2 control-label">年龄</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="dependent_age" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <legend>工作信息</legend>

        <div class="form-group">
          <label class="col-md-2 control-label">雇主姓名</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_name" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">电话</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="employment_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">公司地址</label>
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_address" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">单元</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_unit" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">城市</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_city" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-2 control-label">省份</label>
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
          <label class="col-md-2 control-label">邮编</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input name="employment_postalcode" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">职位</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_occupation" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">每月工资</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_salary" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">工作多久?</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_howlong" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">其他收入</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="employment_other" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">          
          <label class="col-md-9 control-label">如果您有近期工资单，请发送到 <a href="mailto:info@spg-canada.com" target="_top">info@spg-canada.com</a>.</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="employment_paystub" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
        </div>

        <legend>银行信息</legend>


        <div class="form-group">
          <label class="col-md-11 control-label"><I class="span_banking">如果您通过电子邮件向我们提供您的空支票，您不需要填写以下的银行信息。</I></label>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">银行名称</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_name" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">分行地址</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_address" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">账户号</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_accountno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">交易代码</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_transit" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">银行代码</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
              <input name="banking_institution" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">电话</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input name="banking_tel" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-4 control-label">信用卡公司名称</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_company" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">*信用额度</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_limit" class="form-control" type="text" autocomplete="off" required>
            </div>
          </div>
        </div>

        <div class="form-group">          
          <label class="col-md-4 control-label">种类</label>
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
          <label class="col-md-4 control-label">保险种类</label>
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
          <label class="col-md-4 control-label">保险公司名称</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_insurance" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">保险单号</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_policyno" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
        </div>

        <legend>HydroQuebec</legend>

        <div class="form-group">
          <label class="col-md-11 control-label"><I class="span_banking">为了让您搬家更顺利，您可以向我们提供您的HydroQuebec账号。由我们联系HydroQuebec。</I></label>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label">HydroQuebec账号</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
              <input name="banking_hydroquebec" class="form-control" type="text" autocomplete="off">
            </div>
          </div>
          <label class="col-md-4 control-label">搬家日期</label>
          <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input name="banking_movingdate" class="form-control" type="date" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-7 control-label">账号对应您的当前地址</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="banking_active" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
        </div>


        <legend>其他问题</legend>


        <div class="form-group">
          <label class="col-md-6 control-label">您被判过罪吗？</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_crime" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
          <label class="col-md-6 control-label">您破产过吗？</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_bankruptcy" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
        </div>



        <div class="form-group">
          <label class="col-md-7 control-label">您是否在Regie du Logement of Quebec开过档案？</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_file" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="col-md-7 control-label">您是否被信用卡公司拒绝？</label>
          <div class="col-md-2 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <select name="question_declined" class="form-control" type="text" autocomplete="off">
                  <option value="Yes">是</option>
                  <option value="No">否</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-7 control-label">如果是，请提供信用卡公司名称。</label>
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
            <button type="submit" class="btn btn-warning btn-size" name="form_submit">提交<span class="glyphicon glyphicon-send"></span></button>
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