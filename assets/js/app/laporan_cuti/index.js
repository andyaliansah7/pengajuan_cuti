/**
 * Javascript Programs
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 *
 */
window.REPORT = (function ($) {

	return {

		elDatatable: null,
		elVue: '#vue-master',
		elKaryawan: '.select-karyawan',
		elFromDate: '.fromdate',
		elToDate: '.todate',
		elStatus: '.select-status',

		urlRequestData: window.APP.siteUrl + 'adm/laporan_cuti/get_data_detail',

		urlBahasa: window.APP.baseUrl + 'assets/js/vendor/indonesia.json',

		init: function () {
			var that = this;

			that.handleVue();
		},

		// Master
		handleVue: function () {
			var that = this;

			// Vue Js
			new Vue({
				el: that.elVue,
				delimiters: ['<%', '%>'],
				data: {
					karyawan: '',
					fromdate: '',
					todate: '',
					status: '',
					detailData: []
				},
				methods: {
					getData: function () {
						var vue = this;
						var val_karyawan  = $(that.elKaryawan).val();
						var val_fromdate      = $(that.elFromDate).val();
						var val_todate      = $(that.elToDate).val();
						var val_status      = $(that.elStatus).val();
						
						var text_karyawan = $(that.elKaryawan + ' option:selected').text();
						text_status = '';
						if(val_status == 0){
							text_status = 'Menunggu Persetujuan';
						}
						if(val_status == 1){
							text_status = 'Diterima';
						}
						if(val_status == 2){
							text_status = 'Ditolak';
						}
						vue.$set(vue, 'karyawan', text_karyawan);
						vue.$set(vue, 'fromdate', val_fromdate);
						vue.$set(vue, 'todate', val_todate);
						vue.$set(vue, 'status', text_status);

						$.ajax({
							url: that.urlRequestData,
							type: 'post',
							data: {
								'karyawan': val_karyawan,
								'fromdate': val_fromdate,
								'todate'  : val_todate,
								'status'  : val_status
							},
							dataType: "json",
							beforeSend : function() {
								$(that.elVue).block({
									message: '<h4>Please Wait..</h4>'
								});
							},
							success: function (response) {
								vue.$set(vue, 'detailData', response);
								$(that.elVue).unblock();
							}
						});
					},

					exportExcel: function () {
						var vue = this;
						vue.getData();

						setTimeout(function () {
							window.location = window.APP.siteUrl + 'adm/laporan_cuti/export_excel';
						}, 500);
					}
				},
				mounted: function () {

				}
			});

		},

	}

})(jQuery);