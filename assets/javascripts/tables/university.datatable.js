

(function( $ ) {

	'use strict';

	var EditableTable = {

		options: {
			addButton: '#addToUniversityTable',
			table: '#university_datatable',
			dialog: {
				wrapper: '#dialog',
				cancelButton: '#dialogCancel',
				confirmButton: '#dialogConfirm',
			}
		},

		initialize: function() {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function() {
			this.$table				= $( this.options.table );
			this.$addButton			= $( this.options.addButton );

			// dialog
			this.dialog				= {};
			this.dialog.$wrapper	= $( this.options.dialog.wrapper );
			this.dialog.$cancel		= $( this.options.dialog.cancelButton );
			this.dialog.$confirm	= $( this.options.dialog.confirmButton );

			return this;
		},

		build: function() {
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

		events: function() {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function( e ) {
					e.preventDefault();

					_self.rowSave( $(this).closest( 'tr' ) );
				})
				.on('click', 'a.cancel-row', function( e ) {
					e.preventDefault();

					_self.rowCancel( $(this).closest( 'tr' ) );
				})
				.on('click', 'a.edit-row', function( e ) {
					e.preventDefault();

					_self.rowEdit( $(this).closest( 'tr' ) );
				})
				.on( 'click', 'a.remove-row', function( e ) {
					e.preventDefault();

					var $row = $(this).closest( 'tr' );

					$.magnificPopup.open({
						items: {
							src: '#dialog',
							type: 'inline'
						},
						preloader: false,
						modal: true,
						callbacks: {
							change: function() {
								_self.dialog.$confirm.on( 'click', function( e ) {
									e.preventDefault();

									_self.rowRemove( $row );
									$.magnificPopup.close();
								});
							},
							close: function() {
								_self.dialog.$confirm.off( 'click' );
							}
						}
					});
				});

			this.$addButton.on( 'click', function(e) {
				e.preventDefault();

				_self.rowAdd();
			});

			this.dialog.$cancel.on( 'click', function( e ) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function() {
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

			data = this.datatable.row.add([ '', '', actions ]);
			$row = this.datatable.row( data[0] ).nodes().to$();

			$row
				.addClass( 'adding' )
				.find( 'td:last' )
				.addClass( 'actions' );

			this.rowEdit( $row );

			this.datatable.order([0,'asc']).draw(); // always show fields
		},

		rowCancel: function( $row ) {
			var _self = this,
				$actions,
				i,
				data;

			if ( $row.hasClass('adding') ) {
				this.rowRemove( $row );
			} else {

				data = this.datatable.row( $row.get(0) ).data();
				this.datatable.row( $row.get(0) ).data( data );

				$actions = $row.find('td.actions');
				if ( $actions.get(0) ) {
					this.rowSetActionsDefault( $row );
				}

				this.datatable.draw();
			}
		},

		rowEdit: function( $row ) {
			var _self = this,
				data;

			data = this.datatable.row( $row.get(0) ).data();

			$row.children( 'td' ).each(function( i ) {
				var $this = $( this );

				if ( $this.hasClass('actions') ) {
					_self.rowSetActionsEditing( $row );
				} else {
					$this.html( '<input type="text" class="form-control input-block" value="' + data[i] + '"/>' );
				}
			});
		},
 
		rowSave: function( $row ) {
			var _self     = this,
				$actions,
				values    = [];

			if ( $row.hasClass( 'adding' ) ) {
				this.$addButton.removeAttr( 'disabled' );
				$row.removeClass( 'adding' );
			}

			values = $row.find('td').map(function() {
				var $this = $(this);

				if ( $this.hasClass('actions') ) {
					_self.rowSetActionsDefault( $row );
					return _self.datatable.cell( this ).data();
				} else {
					return $.trim( $this.find('input').val() );
				}
			});

			
			var getUrl = window.location;
			var $base_url = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/";
			
			var $university_id = values['context']['dataset']['universityId'];
			var $university_name = values[0];
			var $additional_info = values[1];
			
			if (typeof $university_id === "undefined") {
				//university_id is undefined means that it comes from rowAdd
				//Ajax post data to add new university
				
				var ajax_add_university = $.ajax({
					type: "post",
					url: $base_url + 'school/add_university',
					data: {
						university_name: $university_name,
						additional_info: $additional_info
					}
				});
	
				/*
				$.when(ajax_add_university).done(function () {
					document.getElementById('noti_div').className = "alert alert-success";
					document.getElementById('close_btn').style.display = "block";
					document.getElementById('noti_msg').innerHTML = "University information is added sucessfully!";
				});

				$.when(ajax_add_university).fail(function () {
					document.getElementById('noti_div').className = "alert alert-danger";
					document.getElementById('close_btn').style.display = "block";
					document.getElementById('noti_msg').innerHTML = "University information failed to add!";
				});
				*/
			} else {
				//University ID is defined means that it comes from RowEdit
				//Ajax post data to update new university

				var ajax_update_university = $.ajax({
					type: "post",
					url: $base_url + 'school/update_university',
					data: {
						university_id: $university_id,
						university_name: $university_name,
						additional_info: $additional_info
					}
				});
				
				$.when(ajax_update_university).done(function () {
					document.getElementById('noti_div').className = "alert alert-success";
					document.getElementById('close_btn').style.display = "block";
					document.getElementById('noti_msg').innerHTML = "University information is updated sucessfully!";
				});

				$.when(ajax_update_university).fail(function () {
					document.getElementById('noti_div').className = "alert alert-danger";
					document.getElementById('close_btn').style.display = "block";
					document.getElementById('noti_msg').innerHTML = "University information failed to update!";
				});
			}

			this.datatable.row( $row.get(0) ).data( values );

			$actions = $row.find('td.actions');
			if ( $actions.get(0) ) {
				this.rowSetActionsDefault( $row );
			}

			this.datatable.draw();
		},

		rowRemove: function( $row ) {
			if ( $row.hasClass('adding') ) {
				this.$addButton.removeAttr( 'disabled' );
			}

			var _self     = this,
				values    = [];

			values = $row.find('td').map(function() {
				var $this = $(this);
	
				if ( $this.hasClass('actions') ) {
					_self.rowSetActionsDefault( $row );
					return _self.datatable.cell( this ).data();
				} else {
					return $.trim( $this.find('input').val() );
				}
			});
 
			var getUrl = window.location;
			var $base_url = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/";
			
			var $university_id = values['context']['dataset']['universityId'];

			var ajax_delete_university = $.ajax({
				type: "post",
				url: $base_url + 'school/delete_university',
				data: {
					university_id: $university_id
				}
			});
			
			if ( !$row.hasClass('adding') ) {
			
			$.when(ajax_delete_university).done(function () {
				document.getElementById('noti_div').className = "alert alert-success";
				document.getElementById('close_btn').style.display = "block";
				document.getElementById('noti_msg').innerHTML = "University deleted sucessfully.";
			});

			$.when(ajax_delete_university).fail(function () {
				document.getElementById('noti_div').className = "alert alert-danger";
				document.getElementById('close_btn').style.display = "block";
				document.getElementById('noti_msg').innerHTML = "University failed to delete.";
			});
			}

			this.datatable.row( $row.get(0) ).remove().draw();
		},

		rowSetActionsEditing: function( $row ) {
			$row.find( '.on-editing' ).removeClass( 'hidden' );
			$row.find( '.on-default' ).addClass( 'hidden' );
		},

		rowSetActionsDefault: function( $row ) {
			$row.find( '.on-editing' ).addClass( 'hidden' );
			$row.find( '.on-default' ).removeClass( 'hidden' );
		}

	};

	$(function() {
		EditableTable.initialize();
	});

}).apply( this, [ jQuery ]);