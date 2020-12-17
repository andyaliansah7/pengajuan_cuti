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
		elTahun: '.select-tahun',

		urlRequestData: window.APP.siteUrl + 'adm/laporan_sisa_cuti/get_data_detail',

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
					tahun: '',
					detailData: []
				},
				methods: {
					getData: function () {
						var vue = this;
						var val_karyawan  = $(that.elKaryawan).val();
						var val_tahun      = $(that.elTahun).val();
						
						var text_karyawan = $(that.elKaryawan + ' option:selected').text();
						vue.$set(vue, 'karyawan', text_karyawan);
						vue.$set(vue, 'tahun', val_tahun);

						$.ajax({
							url: that.urlRequestData,
							type: 'post',
							data: {
								'karyawan': val_karyawan,
								'tahun': val_tahun
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
							window.location = window.APP.siteUrl + 'adm/laporan_sisa_cuti/export_excel';
						}, 500);
					}
				},
				mounted: function () {

				}
			});

		},

	}

})(jQuery);