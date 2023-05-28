(function($) {
    "use strict";

	function init() {
		$('#sidebar-menu li a').each(function (i,e) {
			if (location.pathname === $(e).attr('href')) {
				$(e).addClass('active');
				if ($(e).parent('li').parent('ul').prev('a').length > 0) {
					$(e).parent('li').parent('ul').prev('a').addClass('active');
					$(e).parent('li').parent('ul').parent('li').addClass('menu-open');
				}
			}
		});
	}
	
	// Sidebar Initiate
	init();

	//DataTable
	if ($('#data-table').length) {
		$('#data-table').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": false,
			"autoWidth": false,
			"responsive": true,
		});
	}
	
	// Select 2
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }

	// Tooltip
	if($('[data-toggle="tooltip"]').length > 0 ){
		$('[data-toggle="tooltip"]').tooltip();
	}

	
})(jQuery);