<script>
				loadjs.ready("head", function() {
					$("#psearch").on("keyup", function() {
						var value = $(this).val().toLowerCase();
						$("table ew-table").filter(function() {
							$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
					});	});	}); 
			</script>';