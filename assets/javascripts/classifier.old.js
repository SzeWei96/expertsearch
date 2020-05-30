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

									var $base_url = $('a.remove-row').attr('data-base-url');
									var $data_id_name = $('a.remove-row').attr('data-id-name');
									var $data_id = $('a.remove-row').attr('data-id');
									var $data_table = $('a.remove-row').attr('data-table');

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

									var $base_url = $('a.remove-row').attr('data-base-url');
									var $data_id_name = $('a.remove-row').attr('data-id-name');
									var $data_id = $('a.remove-row').attr('data-id');
									var $data_table = 'keyword_' + $('a.remove-row').attr('data-table');
									
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

	// var EditableTable_training_token_frequency_list = {

	// 	options: {
	// 		//addButton: '#addToTable',
	// 		table: '#datatable-editable-training-token-frequency-list',
	// 		dialog: {
	// 			wrapper: '#dialog-training-token-frequency-list',
	// 			cancelButton: '#dialogCancel-training-token-frequency-list',
	// 			confirmButton: '#dialogConfirm-training-token-frequency-list',
	// 		}
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

	// 			.on('click', 'a.edit-row', function (e) {
	// 				e.preventDefault();

	// 				_self.rowEdit($(this).closest('tr'));
	// 			})

	// 			.on('click', 'a.remove-row', function (e) {
	// 				e.preventDefault();

	// 				var $row = $(this).closest('tr');

	// 				$.magnificPopup.open({
	// 					items: {
	// 						src: '#dialog-training-token-frequency-list',
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
	// 			});

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

	// var article_list_table = $('#article-list-table').DataTable({
	// 	'columnDefs': [
	// 		{
	// 			'targets': 0,
	// 			'checkboxes': {
	// 				'selectRow': true
	// 			}
	// 		}
	// 	],
	// 	'select': {
	// 		'style': 'multi'
	// 	},
	// 	'order': [[1, 'asc']]
	// });

	var article_list_table = $("#article-list-table").DataTable({
		aoColumns: [
			{ "bSortable": false },
			null,
			null,
			null,
			null,
			null
		],
		order: [['1', "asc"]],
		stateSave: true
	});
	
	// var EditableTable_testing_token_list = {

	// 	options: {
	// 		//addButton: '#addToTable',
	// 		table: '#datatable-editable-testing-token-list',
	// 		dialog: {
	// 			wrapper: '#dialog-testing-token-list',
	// 			cancelButton: '#dialogCancel-testing-token-list',
	// 			confirmButton: '#dialogConfirm-testing-token-list',
	// 		}
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

	// 			.on('click', 'a.edit-row', function (e) {
	// 				e.preventDefault();

	// 				_self.rowEdit($(this).closest('tr'));
	// 			})

	// 			.on('click', 'a.remove-row', function (e) {
	// 				e.preventDefault();

	// 				var $row = $(this).closest('tr');

	// 				$.magnificPopup.open({
	// 					items: {
	// 						src: '#dialog-testing-token-list',
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
	// 			});

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

	var EditableTable_test_article_list = {

		options: {
			//addButton: '#addToTable',
			table: '#datatable-editable-test-article-list',
			dialog: {
				wrapper: '#dialog-test-article-list',
				cancelButton: '#dialogCancel-test-article-list',
				confirmButton: '#dialogConfirm-test-article-list',
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
		EditableTable_category_keyword_list.initialize();
		EditableTable_training_token_list.initialize();
		//EditableTable_training_token_frequency_list.initialize();
		EditableTable_train_stopword_list.initialize();

		//EditableTable_testing_token_list.initialize();
		EditableTable_test_article_list.initialize();
	});

	$("#train-data-inputs-select").change(function () {
		if ($(this).val() == '1') {
			$('#train-data-inputs-folder').show();
			$('#train-data-submit-folder').show();
			train_data_inputs_folder.appendTo('#train-data-inputs-folder');

			$('#train-data-inputs-excel').hide();
			$('#train-data-submit-excel').hide();
		}
		else if ($(this).val() == '2') {
			$('#train-data-inputs-folder').hide();
			$('#train-data-submit-folder').hide();
			train_data_inputs_folder = $('#train-data-inputs-folder input,input+ol').detach();

			$('#train-data-inputs-excel').show();
			$('#train-data-submit-excel').show();
		}
	});

	$("#train-data-input-file").change(function(e) {
		files = e.target.files;
		$("#train-data-input-file-output").html("");
		
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
		console.log($article_check_ids);
		if ($article_check_ids.length > 0) {
			var $base_url = $('#article-list-btn-next').attr('data-base-url');

			$(".panel-footer .pager li").removeClass("disabled");
			$("#article-list-btn-next").attr("href", $base_url + "admin/classifier/classifier-testing/classifier-selection");

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
			$("#article-list-btn-next").removeAttr("href", $base_url + "admin/classifier/classifier-testing/feature-selection");
		}
	});
	
	$("input[type=checkbox]", article_list_table.rows().nodes()).on('change', function () {
		$article_check_ids = [];
		$('input[type=checkbox]:checked', article_list_table.rows().nodes()).each(function (i) {
			$article_check_ids[i] = $(this).val();
		});
		console.log($article_check_ids);
		if ($article_check_ids.length > 0) {
			var $base_url = $('#article-list-btn-next').attr('data-base-url');
			
			$(".panel-footer .pager li").removeClass("disabled");
			$("#article-list-btn-next").attr("href", $base_url +"admin/classifier/classifier-testing/classifier-selection");

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
			$("#article-list-btn-next").removeAttr("href", $base_url + "admin/classifier/classifier-testing/feature-selection");
		}
	})	
});

function select_value_next_page(base_url = null) {
	base_url = base_url + $('select.form-control').val();
	window.open(base_url, "_self");
}



