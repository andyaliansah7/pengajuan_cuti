/**
 * Javascript Persetujuan
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 *
 */
window.MASTER = (function ($) {

	var renderCheckbox = function (data, type, full, meta) {
		return '<input class="check-sub-master" type="checkbox" value="' + full['id'] + '">';
	}

	var renderEdit = function (data, type, full, meta) {
		var approve = APP.siteUrl + 'adm/persetujuan/save/approve/' + full['id'];
		var reject  = APP.siteUrl + 'adm/persetujuan/save/reject/' + full['id'];
		return '<a href="' + approve + '" class="btn btn-success btn-xs"><i class="far fa-check-circle"></i>&nbsp;Terima</a><a href="' + reject + '" class="btn btn-danger btn-xs" style="margin-left:2%"><i class="far fa-times-circle"></i>&nbsp;Tolak</a><button type="button" class="btn btn-primary btn-xs" style="margin-left:2%"><i class="far fa-sticky-note"></i>&nbsp;Cetak</button>';
	}

	return {

		elDatatable: null,
		elVue: '#vue-master',
		elForm: '.master-form',
		elEdit: '.master-edit',
		elTable: '#master-table',
		elModal: '.master-modal',
		elBtnDelete: '.master-delete',
		elModalClose: '.master-cancel',
		elSubCheckbox: '.check-sub-master',
		elParentCheckbox: '.check-all-master',
		elModalContent: '.master-modal-content',
		urlDeleteData: window.APP.siteUrl + 'adm/persetujuan/delete',
		urlRequestData: window.APP.siteUrl + 'adm/persetujuan/get_data',

		urlBahasa: window.APP.baseUrl + 'assets/js/vendor/indonesia.json',

		init: function () {
			var parentThis = this;
		},

		// Master
		handleVueMaster: function () {
			var parentThis = this;

			// Vue Js
			new Vue({
				el: parentThis.elVue,
				delimiters: ['<%', '%>'],
				methods: {
					addRowType: function () {
						var vue = this;
					}
				},
				mounted: function () {
					parentThis.handleDataTable();
				}
			});

		},

		// Persetujuan : handleDataTable
		handleDataTable: function () {
			var parentThis = this;

			// Datatable
			parentThis.elDatatable = $(parentThis.elTable).DataTable({
				ajax: {
					url: parentThis.urlRequestData
				},
                columns: 
                [   
                    {
						data: 'no',
						className: 'fit-width',
						render: renderCheckbox
					},
					{
						data: 'nomor',
						className: 'fit-width'
					},
					{
						data: 'jenis_cuti',
						className: 'fit-width'
					},
					{
						data: 'tanggal',
						className: 'fit-width'
					},
					{
						data: 'penugasan_nama',
						className: 'fit-width'
					},
					{
						data: 'pekerjaan',
						className: 'fit-width'
					},
					{
						data: 'keterangan',
						className: 'fit-width'
					},
					{
						data: 'status',
						className: 'fit-width'
					},
					{
						render: renderEdit,
						className: 'fit-width'
					}
				],

				order: [],
				deferRender: true,
				scrollX: true,
				"columnDefs": [{
					"targets": [0,8],
					"orderable": false,
				}],
				"language": {
					"url": parentThis.urlBahasa,
					"sEmptyTable": "Tidads"
				},

				initComplete: function () {

					// handle form
					window.FORM.handleEditModal(
						parentThis.elForm,
						parentThis.elEdit,
						parentThis.elModal,
						parentThis.elModalContent,
						parentThis.elModalClose,
						parentThis.elDatatable
					);

					parentThis.handleDelete();
					window.INPUT.handleCheckboxAll(parentThis.elParentCheckbox, parentThis.elSubCheckbox);
				}

			});
		},

		// Persetujuan : handleDelete
		handleDelete: function () {
			var parentThis = this;

			$(parentThis.elBtnDelete).click(function () {

				var Items = $(parentThis.elTable).find('input[class="check-sub-master"]:checked');

				var types = [];
				for (var i = 0; i < Items.length; i++) {
					types.push($(Items[i]).val());
				}

				if (!types.length) {

					toastr.warning('Silahkan pilih data yang akan dihapus terlebih dahulu!')

					return false;

				} else {

					Swal.fire({
						title: 'Anda yakin?',
						text: "Ingin menghapus data ini?",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ya, Hapus!',
						cancelButtonText: 'Batal'
					}).then((result) => {
						if (result.value) {
							$.ajax({
								type: "POST",
								dataType: 'json',
								url: parentThis.urlDeleteData,
								data: {
									id: types,
								},
								success: function (response) {
									window.FORM.showNotification(response.message, response.status);
									parentThis.elDatatable.ajax.reload();
								}
							});
						}
					})

					
				}
			});
		},

	}

})(jQuery);