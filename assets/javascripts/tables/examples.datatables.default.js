

(function( $ ) {

	'use strict';

	var datatableInit = function() {

		$('#datatable-default').dataTable();
		$('#exp_prof_conf').dataTable();
		$('#exp_prof_unconf').dataTable();
		$('#datatable-expert-log').dataTable();
		$('#datatable-publication-log').dataTable();
		$('#datatable-school-log').dataTable();

	};

	$(function() {
		datatableInit();
	});

}).apply( this, [ jQuery ]);