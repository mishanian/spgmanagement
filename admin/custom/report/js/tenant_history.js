$(document).ready(function () {
  var table = $('#tenantBehaviourTable').DataTable({
    columnDefs: [{ orderable: false, targets: 0 }],
    columns: [{ orderable: false }, null, null, null],
  });

  var selectedChanged = false;
  var dateChanged = false;
  /*
   * Data table - tenant name select on change event listener
   */
  $('#tenantSelect').change(function () {
    selectedChanged = true;
    table.draw();
    selectedChanged = false;
  });

  /*
   * Date Picker for the tenant history date filter - and change event listener
   */
  $('#tenanthistoryDate')
    .datepicker({
      clearBtn: true,
      showButtonPanel: true,
      autoclose: true,
      numberOfMonths: 3,
      dateFormat: 'y-MM-dd',
    })
    .change(function (e) {
      dateChanged = true;
      table.draw();
      dateChanged = false;
    });

  $('#clearPaymentDateFilter').on('click', function () {
    $('#tenanthistoryDate').val('').datepicker('refresh');
    table.draw();
  });

  /*
   * Data table filter and search
   */
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    // If the tenant name filter is to be applied
    if (selectedChanged) {
      var _valueHTML = $.parseHTML(data[1]);
      var _text = _valueHTML[0].data; // Value from the Action column in the table
      var tenantId = _text.split('{').pop().split('}').shift();
      tenantId = parseInt(tenantId);

      var filterByTenantId = parseInt($('#tenantSelect').val());

      if (tenantId == filterByTenantId) {
        return true;
      }
      return false;
    }

    // If the date filter is to be applied
    if (dateChanged) {
      var _valueHTML = $.parseHTML(data[1]);
      // console.log(_valueHTML);
      var _dateAndTime = _valueHTML[0].data;
      var dateAndTimeArray = _dateAndTime.split(/(\s+)/);

      var dateArray = dateAndTimeArray[0].split('/');

      var filterSelectedDate = $('#tenanthistoryDate').val();
      //    console.log("fsd="+filterSelectedDate+"=="+dateAndTimeArray[0]);
      //    console.log("fsd="+typeof filterSelectedDate);
      //    console.log("tsd"+typeof $("#tenanthistoryDate").val());
      if (filterSelectedDate == dateAndTimeArray[0]) {
        // console.log("ّFound"+filterSelectedDate +"==="+ dateAndTimeArray[0]);
        return true;
      }
      return false;
    }

    return true;
  });
});
