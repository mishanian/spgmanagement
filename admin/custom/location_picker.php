<div id="location-dialog" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Google Map</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal" style="width: 550px">

                        <div id="modalMap" style="width: 100%; height: 400px;"></div>
                        <div class="clearfix">&nbsp;</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>



<script>
$('#modalMap').locationpicker({

location: {
latitude: 45.5016889,
longitude: -73.56725599999999
},

radius: 50,
inputBinding: {
latitudeInput: $('#x_latitude'),
longitudeInput: $('#x_longitude'),
radiusInput: $('#x-radius'),
locationNameInput: $('#x_address')
},
enableAutocomplete: true,
addressFormat: 'postal_code',
markerIcon: 'custom/images/map-marker-2-xl.png'
});
$('#location-dialog').on('shown.bs.modal', function () {
$('#modalMap').locationpicker('autosize');
});
</script>