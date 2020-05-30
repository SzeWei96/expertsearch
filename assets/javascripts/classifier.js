$(document).ready(function () {
	'use strict';

	var files, file, extension;
	var folder = new Array();
	var train_data_inputs_folder;
	var $article_check_ids = [];

	var EditableTable_classifier_list = {

		options: {
			//addButton: '#addToTable',
			table: '#datatable-editable-classifier-list',
			dialog: {
				wrapper: '#dialog-classifier-list',
				cancelButton: '#dialogCancel-classifier-list',
				confirmButton: '#dialogConfirm-classifier-list',
			}
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					{ "bSortable": false }
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();

					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})
				/*
				.on('click', 'a.edit-row', function( e ) {
					e.preventDefault();

					_self.rowEdit( $(this).closest( 'tr' ) );
				})
				*/
				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-classifier-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									var $base_url = $row.find('a.remove-row').attr('data-base-url');
									var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
									var $data_id = $row.find('a.remove-row').attr('data-id');
									var $data_table = $row.find('a.remove-row').attr('data-table');

									$.ajax({
										type: "post",
										url: $base_url + '/classifier/delete_all',
										data: {
											data_id_name: $data_id_name,
											data_id: $data_id,
											data_table: $data_table
										},
										success: function (response) {
											$("#ajax-remove-success-alert").toggle();
										},
										error: function (response) {
											alert("Invalid!");
											console.log(response);
										}
									});

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				});

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({ 'disabled': 'disabled' });

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}

	};

	var editabletable_classifier_management_test_article_list = function () {
		var $table = $('#datatable-editable-classifier-management-test-article-list');
		var $cancelButton = $('#dialogCancel-classifier-management-test-article-list');
		var $confirmButton = $('#dialogConfirm-classifier-management-test-article-list');

		// format function for row details
		var fnFormatDetails = function (categories) {
			var $categories = categories.innerHTML.split("<br>");
			$categories = $categories.slice(0, 3);
			$categories = $categories.join('<br>');

			return [
				'<table class="table mb-none">',
				'<tr class="b-top-none">',
				'<td><h4 class="mb-none">Categories (highest 3)</h4></td>',
				'</tr>',
				'<tr>',
				'<td>',
				$categories,
				'<br>......',
				'</td>',
				'</tr>',
				'</div>'
			].join('');
		};

		// insert the expand/collapse column
		var th = document.createElement('th');
		var td = document.createElement('td');
		td.innerHTML = '<i data-toggle class="fa fa-plus-square-o text-primary h5 m-none" style="cursor: pointer;"></i>';
		td.className = "text-center";
		td.style = "width: 1px;";

		$table
			.find('thead tr').each(function () {
				this.insertBefore(th, this.childNodes[0]);
			});

		$table
			.find('tbody tr').each(function () {
				this.insertBefore(td.cloneNode(true), this.childNodes[0]);
			});

		// initialize
		var datatable = $table.dataTable({
			aoColumnDefs: [{
				bSortable: false,
				aTargets: [0]
			}],
			aaSorting: [
				[1, 'asc']
			]
		});

		var rowRemove = function ($row) {
			//datatable.row($row.get(0)).remove().draw();
			datatable.fnDeleteRow($row.get(0));
		};

		// add a listener
		$table.on('click', 'i[data-toggle]', function () {
			var $this = $(this),
				tr = $(this).closest('tr').get(0),
				categories = $(this).closest('tr').find('#categories').get(0);

			if (datatable.fnIsOpen(tr)) {
				$this.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
				datatable.fnClose(tr);
			} else {
				$this.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
				datatable.fnOpen(tr, fnFormatDetails(categories), 'details');
			}
		});

		$table.on('click', 'a.remove-row', function (e) {
			e.preventDefault();

			var $row = $(this).closest('tr');

			$.magnificPopup.open({
				items: {
					src: '#dialog-classifier-management-test-article-list',
					type: 'inline'
				},
				preloader: false,
				modal: true,
				callbacks: {
					change: function () {
						$confirmButton.on('click', function (e) {
							e.preventDefault();

							var $base_url = $row.find('a.remove-row').attr('data-base-url');
							var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
							var $data_id = $row.find('a.remove-row').attr('data-id');
							var $data_table = $row.find('a.remove-row').attr('data-table');
							var $data_class_id = $row.find('a.remove-row').attr('data-class-id');
							var $data_publication_id = $row.find('a.remove-row').attr('data-publication-id');

							$.ajax({
								type: "post",
								url: $base_url + '/classifier/delete',
								data: {
									data_id_name: $data_id_name,
									data_id: $data_id,
									data_table: $data_table
								},
								async: false
							}).done(
								$.ajax({
									type: "post",
									url: $base_url + '/classifier/reset_expert',
									data: {
										data_id: $data_id,
										data_class_id: $data_class_id,
										data_publication_id: $data_publication_id
									},
									success: function (response) {
										console.log(response);
										location.reload();
									},
									error: function (response) {
										alert("Invalid!");
										console.log(response);
									}

								})
							);

							rowRemove($row);
							$.magnificPopup.close();
						});
					},
					close: function () {
						$confirmButton.off('click');
					}
				}
			});
		})

		$cancelButton.on('click', function (e) {
			e.preventDefault();

			$.magnificPopup.close();
		});
	};

	var EditableTable_classifier_management_test_article_list = {

		options: {
			//addButton: '#addToTable',
			table: '#DataTable-editable-classifier-management-test-article-list', 
			dialog: {
				wrapper: '#dialog-classifier-management-test-article-list',
				cancelButton: '#dialogCancel-classifier-management-test-article-list',
				confirmButton: '#dialogConfirm-classifier-management-test-article-list',
			},
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					null,
					null,
					{
						"bSortable": false
					}
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();

					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})
				// .on('click', 'a.edit-row', function (e) {
				// 	e.preventDefault();

				// 	_self.rowEdit($(this).closest('tr'));
				// })
				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-classifier-management-test-article-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									var $base_url = $row.find('a.remove-row').attr('data-base-url');
									var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
									var $data_id = $row.find('a.remove-row').attr('data-id');
									var $data_table = $row.find('a.remove-row').attr('data-table');
									var $data_class_id = $row.find('a.remove-row').attr('data-class-id');
									var $data_publication_id = $row.find('a.remove-row').attr('data-publication-id');

									$.ajax({
										type: "post",
										url: $base_url + '/classifier/delete',
										data: {
											data_id_name: $data_id_name,
											data_id: $data_id,
											data_table: $data_table
										},
										async: false
									}).done(
										$.ajax({
											type: "post",
											url: $base_url + '/classifier/reset_expert',
											data: {
												data_id: $data_id,
												data_class_id: $data_class_id,
												data_publication_id: $data_publication_id
											},
											success: function (response) {
												console.log(response);
												location.reload();
											},
											error: function (response) {
												alert("Invalid!");
												console.log(response);
											}

										})
									);

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				})

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({
				'disabled': 'disabled'
			});

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}
	};

	var editabletable_classifier_management_test_expert_list = function () {
		var $table = $('#datatable-editable-classifier-management-test-expert-list');
		var $cancelButton = $('#dialogCancel-classifier-management-test-expert-list');
		var $confirmButton = $('#dialogConfirm-classifier-management-test-expert-list');

		// format function for row details
		var fnFormatDetails = function (categories) {
			var $categories = categories.innerHTML.split("<br>");
			$categories = $categories.slice(0, 3);
			$categories = $categories.join('<br>');

			return [
				'<table class="table mb-none">',
				'<tr class="b-top-none">',
				'<td><h4 class="mb-none">Categories (highest 3)</h4></td>',
				'</tr>',
				'<tr>',
				'<td>',
				$categories,
				'<br>......',
				'</td>',
				'</tr>',
				'</div>'
			].join('');
		};

		// insert the expand/collapse column
		var th = document.createElement('th');
		var td = document.createElement('td');
		td.innerHTML = '<i data-toggle class="fa fa-plus-square-o text-primary h5 m-none" style="cursor: pointer;"></i>';
		td.className = "text-center";
		td.style = "width: 1px;";

		$table
			.find('thead tr').each(function () {
				this.insertBefore(th, this.childNodes[0]);
			});

		$table
			.find('tbody tr').each(function () {
				this.insertBefore(td.cloneNode(true), this.childNodes[0]);
			});

		// initialize
		var datatable = $table.dataTable({
			aoColumnDefs: [{
				bSortable: false,
				aTargets: [0]
			}],
			aaSorting: [
				[1, 'asc']
			]
		});

		var rowRemove = function ($row) {
			//datatable.row($row.get(0)).remove().draw();
			datatable.fnDeleteRow($row.get(0));
		};

		// add a listener
		$table.on('click', 'i[data-toggle]', function () {
			var $this = $(this),
				tr = $(this).closest('tr').get(0),
				categories = $(this).closest('tr').find('#categories').get(0);

			if (datatable.fnIsOpen(tr)) {
				$this.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
				datatable.fnClose(tr);
			} else {
				$this.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
				datatable.fnOpen(tr, fnFormatDetails(categories), 'details');
			}
		});

		$table.on('click', 'a.remove-row', function (e) {
			e.preventDefault();

			var $row = $(this).closest('tr');

			$.magnificPopup.open({
				items: {
					src: '#dialog-classifier-management-test-expert-list',
					type: 'inline'
				},
				preloader: false,
				modal: true,
				callbacks: {
					change: function () {
						$confirmButton.on('click', function (e) {
							e.preventDefault();

							var $base_url = $row.find('a.remove-row').attr('data-base-url');
							var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
							var $data_id = $row.find('a.remove-row').attr('data-id');
							var $data_table = $row.find('a.remove-row').attr('data-table');

							$.ajax({
								type: "post",
								url: $base_url + '/classifier/delete',
								data: {
									data_id_name: $data_id_name,
									data_id: $data_id,
									data_table: $data_table
								},
								success: function (response) {
									$("#ajax-remove-success-alert").toggle();
									console.log(response);
								},
								error: function (response) {
									alert("Invalid!");
									console.log(response);
								}
							});

							rowRemove($row);
							$.magnificPopup.close();
						});
					},
					close: function () {
						$confirmButton.off('click');
					}
				}
			});
		})

		$cancelButton.on('click', function (e) {
			e.preventDefault();

			$.magnificPopup.close();
		});
	};

	// var EditableTable_classifier_management_test_expert_list = {

	// 	options: {
	// 		//addButton: '#addToTable',
	// 		table: '#DataTable-editable-classifier-management-test-expert-list',
	// 		dialog: {
	// 			wrapper: '#dialog-classifier-management-test-expert-list',
	// 			cancelButton: '#dialogCancel-classifier-management-test-expert-list',
	// 			confirmButton: '#dialogConfirm-classifier-management-test-expert-list',
	// 		},
	// 	},

	// 	initialize: function () {
	// 		this
	// 			.setVars()
	// 			.build()
	// 			.events();
	// 	},

	// 	setVars: function () {
	// 		this.$table = $(this.options.table);
	// 		this.$addButton = $(this.options.addButton);

	// 		// dialog
	// 		this.dialog = {};
	// 		this.dialog.$wrapper = $(this.options.dialog.wrapper);
	// 		this.dialog.$cancel = $(this.options.dialog.cancelButton);
	// 		this.dialog.$confirm = $(this.options.dialog.confirmButton);

	// 		return this;
	// 	},

	// 	build: function () {
	// 		this.datatable = this.$table.DataTable({
	// 			aoColumns: [
	// 				null,
	// 				null,
	// 				null,
	// 				{
	// 					"bSortable": false
	// 				}
	// 			]
	// 		});

	// 		window.dt = this.datatable;

	// 		return this;
	// 	},

	// 	events: function () {
	// 		var _self = this;

	// 		this.$table
	// 			.on('click', 'a.save-row', function (e) {
	// 				e.preventDefault();

	// 				_self.rowSave($(this).closest('tr'));
	// 			})
	// 			.on('click', 'a.cancel-row', function (e) {
	// 				e.preventDefault();

	// 				_self.rowCancel($(this).closest('tr'));
	// 			})
	// 			// .on('click', 'a.edit-row', function (e) {
	// 			// 	e.preventDefault();

	// 			// 	_self.rowEdit($(this).closest('tr'));
	// 			// })
	// 			.on('click', 'a.remove-row', function (e) {
	// 				e.preventDefault();

	// 				var $row = $(this).closest('tr');

	// 				$.magnificPopup.open({
	// 					items: {
	// 						src: '#dialog-classifier-management-test-expert-list',
	// 						type: 'inline'
	// 					},
	// 					preloader: false,
	// 					modal: true,
	// 					callbacks: {
	// 						change: function () {
	// 							_self.dialog.$confirm.on('click', function (e) {
	// 								e.preventDefault();

	// 								_self.rowRemove($row);
	// 								$.magnificPopup.close();
	// 							});
	// 						},
	// 						close: function () {
	// 							_self.dialog.$confirm.off('click');
	// 						}
	// 					}
	// 				});
	// 			})

	// 		this.$addButton.on('click', function (e) {
	// 			e.preventDefault();

	// 			_self.rowAdd();
	// 		});

	// 		this.dialog.$cancel.on('click', function (e) {
	// 			e.preventDefault();
	// 			$.magnificPopup.close();
	// 		});

	// 		return this;
	// 	},

	// 	// ==========================================================================================
	// 	// ROW FUNCTIONS
	// 	// ==========================================================================================
	// 	rowAdd: function () {
	// 		this.$addButton.attr({
	// 			'disabled': 'disabled'
	// 		});

	// 		var actions,
	// 			data,
	// 			$row;

	// 		actions = [
	// 			'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
	// 			'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
	// 			'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
	// 			'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
	// 		].join(' ');

	// 		data = this.datatable.row.add(['', '', '', actions]);
	// 		$row = this.datatable.row(data[0]).nodes().to$();

	// 		$row
	// 			.addClass('adding')
	// 			.find('td:last')
	// 			.addClass('actions');

	// 		this.rowEdit($row);

	// 		this.datatable.order([0, 'asc']).draw(); // always show fields
	// 	},

	// 	rowCancel: function ($row) {
	// 		var _self = this,
	// 			$actions,
	// 			i,
	// 			data;

	// 		if ($row.hasClass('adding')) {
	// 			this.rowRemove($row);
	// 		} else {

	// 			data = this.datatable.row($row.get(0)).data();
	// 			this.datatable.row($row.get(0)).data(data);

	// 			$actions = $row.find('td.actions');
	// 			if ($actions.get(0)) {
	// 				this.rowSetActionsDefault($row);
	// 			}

	// 			this.datatable.draw();
	// 		}
	// 	},

	// 	rowEdit: function ($row) {
	// 		var _self = this,
	// 			data;

	// 		data = this.datatable.row($row.get(0)).data();

	// 		$row.children('td').each(function (i) {
	// 			var $this = $(this);

	// 			if ($this.hasClass('actions')) {
	// 				_self.rowSetActionsEditing($row);
	// 			} else {
	// 				$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
	// 			}
	// 		});
	// 	},

	// 	rowSave: function ($row) {
	// 		var _self = this,
	// 			$actions,
	// 			values = [];

	// 		if ($row.hasClass('adding')) {
	// 			this.$addButton.removeAttr('disabled');
	// 			$row.removeClass('adding');
	// 		}

	// 		values = $row.find('td').map(function () {
	// 			var $this = $(this);

	// 			if ($this.hasClass('actions')) {
	// 				_self.rowSetActionsDefault($row);
	// 				return _self.datatable.cell(this).data();
	// 			} else {
	// 				return $.trim($this.find('input').val());
	// 			}
	// 		});

	// 		this.datatable.row($row.get(0)).data(values);

	// 		$actions = $row.find('td.actions');
	// 		if ($actions.get(0)) {
	// 			this.rowSetActionsDefault($row);
	// 		}

	// 		this.datatable.draw();
	// 	},

	// 	rowRemove: function ($row) {
	// 		if ($row.hasClass('adding')) {
	// 			this.$addButton.removeAttr('disabled');
	// 		}

	// 		this.datatable.row($row.get(0)).remove().draw();
	// 	},

	// 	rowSetActionsEditing: function ($row) {
	// 		$row.find('.on-editing').removeClass('hidden');
	// 		$row.find('.on-default').addClass('hidden');
	// 	},

	// 	rowSetActionsDefault: function ($row) {
	// 		$row.find('.on-editing').addClass('hidden');
	// 		$row.find('.on-default').removeClass('hidden');
	// 	}
	// };

	var EditableTable_category_keyword_list = {

		options: {
			//addButton: '#addToTable',
			table: '#datatable-editable-category-keyword-list',
			dialog: {
				wrapper: '#dialog-category-keyword-list',
				cancelButton: '#dialogCancel-category-keyword-list',
				confirmButton: '#dialogConfirm-category-keyword-list',
			}
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					null,
					{ "bSortable": false }
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();
					
					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})

				.on('click', 'a.edit-row', function (e) {
					e.preventDefault();

					_self.rowEdit($(this).closest('tr'));
				})

				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-category-keyword-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									var $base_url = $row.find('a.remove-row').attr('data-base-url');
									var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
									var $data_id = $row.find('a.remove-row').attr('data-id');
									var $data_table = 'keyword_' + $row.find('a.remove-row').attr('data-table');
									
									$.ajax({
										type: "post",
										url: $base_url + '/classifier/delete',
										data: {
											data_id_name: $data_id_name,
											data_id: $data_id,
											data_table: $data_table
										},
										success: function (response) {
											$("#ajax-remove-success-alert").toggle();
											console.log(response);
										},
										error: function (response) {
											alert("Invalid!");
											console.log(response);
										}
									});

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				});

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({ 'disabled': 'disabled' });

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();

			var $base_url = values['context']['dataset']['baseUrl'];
			var $data_table = values['context']['dataset']['table'];

			var $data_id_name_category = values['prevObject']['0']['dataset']['idName'];
			var $data_id_category = values['prevObject']['0']['dataset']['id'];
			var $data_id_name_keyword = values['prevObject']['1']['dataset']['idName'];
			var $data_id_keyword = values['prevObject']['1']['dataset']['id'];

			var $category = values['0'];
			var $keyword = values['1'];

			var ajax_keyword = $.ajax({
				type: "post",
				url: $base_url + '/classifier/update_keyword',
				data: {
					data_id_name: $data_id_name_keyword,
					data_id: $data_id_keyword,
					data_table: $data_table,
					keyword: $keyword
				}
			});

			var ajax_category = $.ajax({
				type: "post",
				url: $base_url + '/classifier/update_category',
				data: {
					data_id_name: $data_id_name_category,
					data_id: $data_id_category,
					data_table: $data_table,
					category: $category
				}
			});

			$.when(ajax_keyword, ajax_category).done(function () {
				$("#ajax-edit-success-alert").toggle();
			});

			$.when(ajax_keyword, ajax_category).fail(function () {
				alert("Invalid!");
				console.log(response);
			});
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}

	};

	var EditableTable_train_stopword_list = {

		options: {
			//addButton: '#addToTable',
			table: '#datatable-editable-edit-stopword-list',
			dialog: {
				wrapper: '#dialog-edit-stopword-list',
				cancelButton: '#dialogCancel-edit-stopword-list',
				confirmButton: '#dialogConfirm-edit-stopword-list',
			}
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					{
						"bSortable": false
					}
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();

					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})

				.on('click', 'a.edit-row', function (e) {
					e.preventDefault();
					
					_self.rowEdit($(this).closest('tr'));
				})

				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-edit-stopword-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				});

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({
				'disabled': 'disabled'
			});

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			var $base_url = values['context']['dataset']['baseUrl'];
			var $data_table = values['context']['dataset']['table'];

			var $data_id_name_stopword = values['prevObject']['0']['dataset']['idName'];
			var $data_id_stopword = values['prevObject']['0']['dataset']['id'];

			var $stopword = values['0'];

			var ajax_stopword = $.ajax({
				type: "post",
				url: $base_url + '/classifier/update_stopword',
				data: {
					data_id_name: $data_id_name_stopword,
					data_id: $data_id_stopword,
					data_table: $data_table,
					stopword: $stopword
				}
			});

			$.when(ajax_stopword).done(function () {
				$("#ajax-edit-success-alert").toggle();
			});

			$.when(ajax_stopword).fail(function () {
				alert("Invalid!");
				console.log(response);
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}

	};

	var EditableTable_training_token_list = {

		options: {
			//addButton: '#addToTable',
			table: '#datatable-editable-training-token-list',
			dialog: {
				wrapper: '#dialog-training-token-list',
				cancelButton: '#dialogCancel-training-token-list',
				confirmButton: '#dialogConfirm-training-token-list',
			}
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					null,
					{
						"bSortable": false
					}
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();

					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})

				.on('click', 'a.edit-row', function (e) {
					e.preventDefault();

					_self.rowEdit($(this).closest('tr'));
				})

				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-training-token-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				});

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({
				'disabled': 'disabled'
			});

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').not(':nth-child(2)').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			var $base_url = values['context']['dataset']['baseUrl'];
			var $data_table = values['context']['dataset']['table'];
			var $class_id = values['context']['dataset']['classId'];
			
			var $data_id_name_token = values['prevObject']['0']['dataset']['idName'];
			var $data_id_token = values['prevObject']['0']['dataset']['id'];

			var $token = values['0'];
			var $ori_token = values['prevObject']['0']['dataset']['oriToken'];

			var ajax_token = $.ajax({
				type: "post",
				url: $base_url + '/classifier/update_token',
				data: {
					data_id_name: $data_id_name_token,
					data_id: $data_id_token,
					data_table: $data_table,
					token: $token,
					class_id: $class_id,
					ori_token: $ori_token,
				}
			});

			$.when(ajax_token).done(function () {
				$("#ajax-edit-success-alert").toggle();
			});

			$.when(ajax_token).fail(function () {
				alert("Invalid!");
				console.log(response);
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}

	};

	var article_list_table = $("#article-list-table").DataTable({
		aoColumns: [{
				"bSortable": false
			},
			null,
			null,
			null,
			null,
			null,
			null
		],
		order: [
			['1', "asc"]
		],
		stateSave: true
	});

	var editabletable_test_article_list = function () {
		var $table = $('#datatable-editable-test-article-list');
		var $cancelButton = $('#dialogCancel-test-article-list');
		var $confirmButton = $('#dialogConfirm-test-article-list');
		
		// format function for row details
		var fnFormatDetails = function (categories) {
			var $categories = categories.innerHTML.split("<br>");
			$categories = $categories.slice(0, 3);
			$categories = $categories.join('<br>');

			return [
				'<table class="table mb-none">',
					'<tr class="b-top-none">',
						'<td><h4 class="mb-none">Categories (highest 3)</h4></td>',
					'</tr>',
					'<tr>',
						'<td>',
						$categories,
						'<br>......',
						'</td>',
					'</tr>',
				'</div>'
			].join('');
		};

		// insert the expand/collapse column
		var th = document.createElement('th');
		var td = document.createElement('td');
		td.innerHTML = '<i data-toggle class="fa fa-plus-square-o text-primary h5 m-none" style="cursor: pointer;"></i>';
		td.className = "text-center";
		td.style = "width: 1px;";

		$table
			.find('thead tr').each(function () {
				this.insertBefore(th, this.childNodes[0]);
			});

		$table
			.find('tbody tr').each(function () {
				this.insertBefore(td.cloneNode(true), this.childNodes[0]);
			});

		// initialize
		var datatable = $table.dataTable({
			aoColumnDefs: [{
				bSortable: false,
				aTargets: [0]
			}],
			aaSorting: [
				[1, 'asc']
			]
		});

		var rowRemove = function ($row) {
			//datatable.row($row.get(0)).remove().draw();
			datatable.fnDeleteRow($row.get(0));
		};

		// add a listener
		$table.on('click', 'i[data-toggle]', function () {
			var $this = $(this),
				tr = $(this).closest('tr').get(0),
				categories = $(this).closest('tr').find('#categories').get(0);

			if (datatable.fnIsOpen(tr)) {
				$this.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
				datatable.fnClose(tr);
			} else {
				$this.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
				datatable.fnOpen(tr, fnFormatDetails(categories), 'details');
			}
		});

		$table.on('click', 'a.remove-row', function (e) {
			e.preventDefault();

			var $row = $(this).closest('tr');

			$.magnificPopup.open({
				items: {
					src: '#dialog-test-article-list',
					type: 'inline'
				},
				preloader: false,
				modal: true,
				callbacks: {
					change: function () {
						$confirmButton.on('click', function (e) {
							e.preventDefault();

							var $base_url = $row.find('a.remove-row').attr('data-base-url');
							var $data_id_name = $row.find('a.remove-row').attr('data-id-name');
							var $data_id = $row.find('a.remove-row').attr('data-id');
							var $data_table = $row.find('a.remove-row').attr('data-table');
							var $data_class_id = $row.find('a.remove-row').attr('data-class-id');
							var $data_publication_id = $row.find('a.remove-row').attr('data-publication-id');

							$.ajax({
								type: "post",
								url: $base_url + '/classifier/delete',
								data: {
									data_id_name: $data_id_name,
									data_id: $data_id,
									data_table: $data_table
								},
								async: false
							}).done(
								$.ajax({
									type: "post",
									url: $base_url + '/classifier/reset_expert',
									data: {
										data_id: $data_id,
										data_class_id: $data_class_id,
										data_publication_id: $data_publication_id
									},
									success: function (response) {
										console.log(response);
										location.reload();
									},
									error: function (response) {
										alert("Invalid!");
										console.log(response);
									}

								})
							);

							rowRemove($row);
							$.magnificPopup.close();
						});
					},
					close: function () {
						$confirmButton.off('click');
					}
				}
			});
		})

		$cancelButton.on('click', function (e) {
			e.preventDefault();
			
			$.magnificPopup.close();
		});
	};

	var EditableTable_test_article_list = {

		options: {
			//addButton: '#addToTable',
			table: '#DataTable-editable-test-article-list',
			dialog: {
				wrapper: '#dialog-test-article-list',
				cancelButton: '#dialogCancel-test-article-list',
				confirmButton: '#dialogConfirm-test-article-list',
			},
		},

		initialize: function () {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function () {
			this.$table = $(this.options.table);
			this.$addButton = $(this.options.addButton);

			// dialog
			this.dialog = {};
			this.dialog.$wrapper = $(this.options.dialog.wrapper);
			this.dialog.$cancel = $(this.options.dialog.cancelButton);
			this.dialog.$confirm = $(this.options.dialog.confirmButton);

			return this;
		},

		build: function () {
			this.datatable = this.$table.DataTable({
				aoColumns: [
					null,
					null,
					{
						"bSortable": false
					}
				]
			});

			window.dt = this.datatable;

			return this;
		},

		events: function () {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function (e) {
					e.preventDefault();

					_self.rowSave($(this).closest('tr'));
				})
				.on('click', 'a.cancel-row', function (e) {
					e.preventDefault();

					_self.rowCancel($(this).closest('tr'));
				})
				// .on('click', 'a.edit-row', function (e) {
				// 	e.preventDefault();

				// 	_self.rowEdit($(this).closest('tr'));
				// })
				.on('click', 'a.remove-row', function (e) {
					e.preventDefault();

					var $row = $(this).closest('tr');

					$.magnificPopup.open({
						items: {
							src: '#dialog-test-article-list',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function () {
								_self.dialog.$confirm.on('click', function (e) {
									e.preventDefault();

									_self.rowRemove($row);
									$.magnificPopup.close();
								});
							},
							close: function () {
								_self.dialog.$confirm.off('click');
							}
						}
					});
				})

			this.$addButton.on('click', function (e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on('click', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function () {
			this.$addButton.attr({
				'disabled': 'disabled'
			});

			var actions,
				data,
				$row;

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

			data = this.datatable.row.add(['', '', '', actions]);
			$row = this.datatable.row(data[0]).nodes().to$();

			$row
				.addClass('adding')
				.find('td:last')
				.addClass('actions');

			this.rowEdit($row);

			this.datatable.order([0, 'asc']).draw(); // always show fields
		},

		rowCancel: function ($row) {
			var _self = this,
				$actions,
				i,
				data;

			if ($row.hasClass('adding')) {
				this.rowRemove($row);
			} else {

				data = this.datatable.row($row.get(0)).data();
				this.datatable.row($row.get(0)).data(data);

				$actions = $row.find('td.actions');
				if ($actions.get(0)) {
					this.rowSetActionsDefault($row);
				}

				this.datatable.draw();
			}
		},

		rowEdit: function ($row) {
			var _self = this,
				data;

			data = this.datatable.row($row.get(0)).data();

			$row.children('td').each(function (i) {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsEditing($row);
				} else {
					$this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
				}
			});
		},

		rowSave: function ($row) {
			var _self = this,
				$actions,
				values = [];

			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
				$row.removeClass('adding');
			}

			values = $row.find('td').map(function () {
				var $this = $(this);

				if ($this.hasClass('actions')) {
					_self.rowSetActionsDefault($row);
					return _self.datatable.cell(this).data();
				} else {
					return $.trim($this.find('input').val());
				}
			});

			this.datatable.row($row.get(0)).data(values);

			$actions = $row.find('td.actions');
			if ($actions.get(0)) {
				this.rowSetActionsDefault($row);
			}

			this.datatable.draw();
		},

		rowRemove: function ($row) {
			if ($row.hasClass('adding')) {
				this.$addButton.removeAttr('disabled');
			}

			this.datatable.row($row.get(0)).remove().draw();
		},

		rowSetActionsEditing: function ($row) {
			$row.find('.on-editing').removeClass('hidden');
			$row.find('.on-default').addClass('hidden');
		},

		rowSetActionsDefault: function ($row) {
			$row.find('.on-editing').addClass('hidden');
			$row.find('.on-default').removeClass('hidden');
		}
	};

	$(function () {
		EditableTable_classifier_list.initialize();
		editabletable_classifier_management_test_article_list();
		EditableTable_classifier_management_test_article_list.initialize();
		editabletable_classifier_management_test_expert_list();
		// EditableTable_classifier_management_test_expert_list.initialize();
		EditableTable_category_keyword_list.initialize();
		EditableTable_training_token_list.initialize();
		EditableTable_train_stopword_list.initialize();
		editabletable_test_article_list();
		EditableTable_test_article_list.initialize();
	});

	$("#train-data-inputs-select").change(function () {
		if ($(this).val() == '1') {
			$('#train-data-inputs').append(
				'<div id="train-data-inputs-folder" class="fileupload fileupload-new" data-provides="fileupload">'+
					'<div class="input-append">'+
						'<div class="uneditable-input">'+
							'<i class="fa fa-file fileupload-exists"></i>'+
							'<span class="fileupload-preview"></span>'+
						'</div>'+
						'<span class="btn btn-default btn-file">'+
							'<span class="fileupload-exists">Change</span>'+
							'<span class="fileupload-new">Select folder</span>'+
							'<input type="hidden" name="MAX_FILE_SIZE" value="20000"/>'+
							'<input type="file" name="train-data-input-file[]" id="train-data-input-file" multiple="" directory="" webkitdirectory="" mozdirectory=""/>'+
							'<input type="hidden" id="train-data-input-folder" name="train-data-input-folder"/>'+
							'<ol id="train-data-input-file-output" style=" display: none;"></ol>'+
						'</span>'+
						'<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>'+
					'</div>'+
				'</div>'
			);
			$('#train-data-inputs-excel').remove();

			$('#train-data-submit-folder').show();
			$('#train-data-submit-excel').hide();
		}
		else if ($(this).val() == '2') {
			$('#train-data-inputs-folder').remove();
			$('#train-data-inputs').append(
				'<div id="train-data-inputs-excel" class="fileupload fileupload-new" data-provides="fileupload">'+
					'<div class="input-append">'+
						'<div class="uneditable-input">'+
							'<i class="fa fa-file fileupload-exists"></i>'+
							'<span class="fileupload-preview"></span>'+
						'</div>'+
						'<span class="btn btn-default btn-file">'+
							'<span class="fileupload-exists">Change</span>'+
							'<span class="fileupload-new">Select dictionary (excel)</span>'+
							'<input type="file" name="train-data-input-excel" id="train-data-input-excel"/>'+
						'</span>'+
						'<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>'+
					'</div>'+
				'</div>'
			);

			$('#train-data-submit-folder').hide();
			$('#train-data-submit-excel').show();
		}
	});

	$(document).on("change", "#train-data-input-file", function (e) {
		e.preventDefault();
		
		files = e.target.files;
		$("#train-data-input-file-output").html("");

		// var span_text = files.length + ' Files';
		// //$(".fa-file").removeClass("fileupload-exists");
		// $(".fileupload-preview").html(span_text);
		
		for (var i = 0; i < files.length; i++) {
			file = files[i];
			extension = file.name.split(".").pop();

			$("#train-data-input-file-output").append("<li>" + file.webkitRelativePath + "</li>");
			folder.push(file.webkitRelativePath.split("/")[1]);
		}
		
		$("#train-data-input-folder").val(JSON.stringify(folder));
	});

	$("#train-text-preprocess-select").change(function () {
		if ($(this).val() == 'disabled') {
			$('#train-text-preprocess-edit').hide();
		} else {
			$('#train-text-preprocess-edit').show();
		}
	});

	$("#train-algo-select").change(function () {
		if ($(this).val() == 'svc') {
			$('#train-algo-select-svc').show().find(':input').attr('required', true);
			$('#train-algo-select-knn').hide().find(':input').attr('required', false);
			$('#train-algo-select-nb').hide().find(':input').attr('required', false);
		} else if ($(this).val() == 'knn') {
			$('#train-algo-select-svc').hide().find(':input').attr('required', false);
			$('#train-algo-select-knn').show().find(':input').attr('required', true);
			$('#train-algo-select-nb').hide().find(':input').attr('required', false);
		} else {
			$('#train-algo-select-svc').hide().find(':input').attr('required', false);
			$('#train-algo-select-knn').hide().find(':input').attr('required', false);
			$('#train-algo-select-nb').show().find(':input').attr('required', true);
		}
	});
	
	$('#article-list-table input[type=checkbox]:first').on('click', function (e) {
		if ($(this).is(':checked', true)) {
			$("input[type=checkbox]", article_list_table.rows().nodes()).prop('checked', true);
		}
		else {
			$("input[type=checkbox]", article_list_table.rows().nodes()).prop('checked', false);
		}

		$article_check_ids = [];
		$('input[type=checkbox]:checked', article_list_table.rows().nodes()).each(function (i) {
			$article_check_ids[i] = $(this).val();
		});
		
		if ($article_check_ids.length > 0) {
			var $base_url = $('#article-list-btn-next').attr('data-base-url');

			$(".panel-footer .pager li").removeClass("disabled");
			$("#article-list-btn-next").attr("href", $base_url + "admin/the-classifier/classifier-testing/classifier-selection");

			$("#article-list-btn-next").click(function () {
				$.ajax({
					type: "post",
					url: $base_url + '/classifier/get_article_list_checked',
					data: {
						article_check_ids: $article_check_ids,
					},
					success: function (response) {
						//$("#ajax-remove-success-alert").toggle();
						//console.log(response);
					},
					error: function (response) {
						alert("Invalid!");
						console.log(response);
					}
				});
			});
		} else {
			$(".panel-footer .pager li").addClass("disabled");
			$("#article-list-btn-next").removeAttr("href", $base_url + "admin/the-classifier/classifier-testing/feature-selection");
		}
	});
	
	$("input[type=checkbox]", article_list_table.rows().nodes()).on('change', function () {
		$article_check_ids = [];
		$('input[type=checkbox]:checked', article_list_table.rows().nodes()).each(function (i) {
			$article_check_ids[i] = $(this).val();
		});
		if ($article_check_ids.length > 0) {
			var $base_url = $('#article-list-btn-next').attr('data-base-url');
			
			$(".panel-footer .pager li").removeClass("disabled");
			$("#article-list-btn-next").attr("href", $base_url +"admin/the-classifier/classifier-testing/classifier-selection");

			$("#article-list-btn-next").click(function () {				
				$.ajax({
					type: "post",
					url: $base_url + '/classifier/get_article_list_checked',
					data: {
						article_check_ids: $article_check_ids,
					},
					success: function (response) {
						//$("#ajax-remove-success-alert").toggle();
						//console.log(response);
					},
					error: function (response) {
						alert("Invalid!");
						console.log(response);
					}
				});
			});
		} else {
			$(".panel-footer .pager li").addClass("disabled");
			$("#article-list-btn-next").removeAttr("href", $base_url + "admin/the-classifier/classifier-testing/feature-selection");
		}
	})
	
	$("#svc-kernal-type").change(function () {
		if ($(this).val() == '0') { //linear
			$('#svc-degree-kernel').hide().find(':input').attr('required', false);
			$('#svc-kernal-coef').hide().find(':input').attr('required', false);
			$('#svc-coef').hide().find(':input').attr('required', false);
		} else if ($(this).val() == '1') { //poly
			$('#svc-degree-kernel').show().find(':input').attr('required', true);
			$('#svc-kernal-coef').show().find(':input').attr('required', true);
			$('#svc-coef').show().find(':input').attr('required', true);
		} else if ($(this).val() == '2') { //rbf
			$('#svc-degree-kernel').hide().find(':input').attr('required', false);
			$('#svc-kernal-coef').show().find(':input').attr('required', true);
			$('#svc-coef').hide().find(':input').attr('required', false);
		} else { //sigmoid
			$('#svc-degree-kernel').hide().find(':input').attr('required', false);
			$('#svc-kernal-coef').show().find(':input').attr('required', true);
			$('#svc-coef').show().find(':input').attr('required', true);
		}
	});

	$("#knn-dist-metric").change(function () {
		if ($(this).val() == 'euclidean') { //euclidean
			$('#knn-lambda').hide().find(':input').attr('required', false);
		} else if ($(this).val() == 'manhattan') { //manhattan
			$('#knn-lambda').hide().find(':input').attr('required', false);
		} else if ($(this).val() == 'chebyshev') { //chebyshev
			$('#knn-lambda').hide().find(':input').attr('required', false);
		} else { //minkowski
			$('#knn-lambda').show().find(':input').attr('required', true);
		}
	});

	$("input[name='publication-predict-cat-prob[]']").inputSpinner();
	$("input[name='expert-predict-cat-prob[]']").inputSpinner();

	$("#reset-expert-btn").click(function (e) {
		e.preventDefault;

		var $base_url = $(this).attr('data-base-url');
		var $data_class_id = $(this).attr('data-class-id');

		$.ajax({
			type: "post",
			url: $base_url + '/classifier/reset_expert',
			data: {
				data_class_id: $data_class_id,
			},
			success: function (response) {
				//location.reload();
				console.log(response);
			},
			error: function (response) {
				alert("Invalid!");
				console.log(response);
			}
		});
	});
});

function select_value_next_page(base_url = null) {
	base_url = base_url + $('select.form-control').val();
	window.open(base_url, "_self");
}



