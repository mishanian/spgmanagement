<?php

include_once ("pdo/dbconfig.php");
echo
<<<HTML
<!--start advanced search section-->
<div class="advanced-search advance-search-header">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <form method="post" action="map-listing-apts.php?search=2">
          <div class="form-group search-long">
            <div class="search">
              <div class="input-search input-icon">
                <input class="form-control" type="text" placeholder="Search for a place to stay?">
              </div>
              <select name="province" title="{$DB_snapshot->echot('All Provinces')}" class="selectpicker bs-select-hidden" data-live-search="false">
HTML;

$province_rows = $DB_province->getAllProvinces();
foreach ($province_rows as $province_row){
    echo "<option value=\"".$province_row['id']."\">". $DB_snapshot->echot($province_row['name']) ."</option>";
}

echo
<<<HTML
              </select>
              <div class="advance-btn-holder">
                <button class="advance-btn btn" type="button"><i class="fa fa-gear"></i> Advanced</button>
              </div>
            </div>
            <div class="search-btn">
              <button class="btn btn-secondary">Go</button>
            </div>
          </div>
          <div class="advance-fields">
            <div class="row">
              <div class="col-sm-6 col-xs-6">
                <div class="form-group">
                  <select class="selectpicker" data-live-search="true" title="{$DB_snapshot->echot('Any Price')}" name="price">
                      <option value="500">{$DB_snapshot->echot('$500 or less')}</option>
                      <option value="1000">{$DB_snapshot->echot('$501 to $1000')}</option>
                      <option value="1500">{$DB_snapshot->echot('$1001 to $1500')}</option>
                      <option value="2000">{$DB_snapshot->echot('$1501 to $2000')}</option>
                      <option value="2500">{$DB_snapshot->echot('$2001 or above')}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6 col-xs-6">
                <div class="form-group">
                  <select class="selectpicker" data-live-search="true" title="{$DB_snapshot->echot('Any Size')}" name="size">
HTML;

$size_type_rows = $DB_size->getAllSizeType();
foreach ($size_type_rows as $size_type_row){

    echo "<option value=\"".$size_type_row['id']."\">". $DB_snapshot->echot($size_type_row['name']) ."</option>";

}
echo <<<HTML
                  </select>
                </div>
              </div>
              <div class="col-sm-6 col-xs-6">
                <div class="form-group">
                  <select class="selectpicker" data-live-search="true" title="{$DB_snapshot->echot('Bedrooms')}" name="bedroom">
                    <option value="1">{$DB_snapshot->echot('1 bedroom')}</option>
                    <option value="2">{$DB_snapshot->echot('2 bedrooms')}</option>
                    <option value="3">{$DB_snapshot->echot('3 bedrooms')}</option>
                    <option value="4">{$DB_snapshot->echot('4 bedrooms')}</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-6 col-xs-6">
                <div class="form-group">
                  <select class="selectpicker" data-live-search="true" title="{$DB_snapshot->echot('Areas (Sqft)')}" name="area">
                    <option value="500">{$DB_snapshot->echot('500 or less')}</option>
                    <option value="1000">{$DB_snapshot->echot('501 to 1000')}</option>
                    <option value="1500">{$DB_snapshot->echot('1000 to 1500')}</option>
                  </select>
                </div>
              </div>
       
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end advanced search section-->


HTML;
