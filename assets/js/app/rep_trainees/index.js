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
		elBatch: '.select-batch',
		elProgram: '.select-program',
		elProgramClass: '.select-program-class',
		elProgramType: '.select-program-type',
		elCertificate: '.text-certificate',
		elTrainee: '.select-trainee',
		elCompany: '.select-company',
		elStatus: '.select-status',

		urlRequestData: window.APP.siteUrl + 'adm/rep_trainees/get_data_detail',
		urlExportExcel: window.APP.siteUrl + 'adm/rep_trainees/export_excel',
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
					batch: '',
					program: '',
					programclass: '',
					programtype: '',
					trainee: '',
					company: '',
					status: '',
					detailData: []
				},
				methods: {
					getData: function () {
						var vue = this;
						var val_batch        = $(that.elBatch).val();
						var val_program      = $(that.elProgram).val();
						var val_programclass = $(that.elProgramClass).val();
						var val_programtype  = $(that.elProgramType).val();
						var val_trainee      = $(that.elTrainee).val();
						var val_company      = $(that.elCompany).val();
						var val_certificate  = $(that.elCertificate).val();

						var text_batch        = $(that.elBatch + ' option:selected').text();
						var text_program      = $(that.elProgram + ' option:selected').text();
						var text_programclass = $(that.elProgramClass + ' option:selected').text();
						var text_programtype  = $(that.elProgramType + ' option:selected').text();
						var text_trainee      = $(that.elTrainee + ' option:selected').text();
						var text_company      = $(that.elCompany + ' option:selected').text();

						vue.$set(vue, 'batch', text_batch);
						vue.$set(vue, 'program', text_program);
						vue.$set(vue, 'programclass', text_programclass);
						vue.$set(vue, 'programtype', text_programtype);
						vue.$set(vue, 'trainee', text_trainee);
						vue.$set(vue, 'company', text_company);

						$.ajax({
							url: that.urlRequestData,
							type: 'post',
							data: {
								'batch': text_batch,
								'program': text_program,
								'programclass': val_programclass,
								'programtype': val_programtype,
								'trainee': val_trainee,
								'company': val_company,
								'certificate': val_certificate,
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

					exportExcel: function()
					{	
						var vue = this;
						vue.getData();

						setTimeout(function () {
							window.location = window.APP.siteUrl + 'adm/rep_trainees/export_excel';
						}, 500);
					}
				},
				mounted: function () {

				}
			});

		},

		getDataClass: function(program_name) {
			var that = this;

			$.ajax({
				type: "POST",
				url: window.APP.siteUrl + 'adm/rep_trainees/get_data_class',
				dataType: "JSON",
				data: {
					program_name: program_name,
				},
				success: function (data) {

					$("#class").html("<option value=''>(Semua)</option>");
					$.each(data, function (i, d) {
						$('#class').append('<option value="' + d.program_class_id + '">' + d.program_class_name + '</option>');
					});

				},
			});
		},

		getDataType: function(program_name, class_name) {
			var that = this;

			$.ajax({
				type: "POST",
				url: window.APP.siteUrl + 'adm/rep_trainees/get_data_type',
				dataType: "JSON",
				data: {
					program_name: program_name,
					program_class_name: class_name
				},
				success: function (data) {

					$("#type").html("<option value=''>(Semua)</option>");
					$.each(data, function (i, d) {
						$('#type').append('<option value="' + d.program_type_id + '">' + d.program_type_name + '</option>');
					});

				},
			});
		},

		getDataBatch: function(program_name, class_name, type_name) {
			var that = this;

			$.ajax({
				type: "POST",
				url: window.APP.siteUrl + 'adm/rep_trainees/get_data_batch',
				dataType: "JSON",
				data: {
					program_name: program_name,
					program_class_name: class_name,
					program_type_name: type_name
				},
				success: function (data) {

					$("#batch").html("<option value=''>(Semua)</option>");
					$.each(data, function (i, d) {
						$('#batch').append('<option value="' + d.batch_header_id + '">' + d.batch_header_name + '</option>');
					});

				},
			});
		},

		getDataCompany: function(program_name, class_name, type_name, batch_name) {
			var that = this;

			$.ajax({
				type: "POST",
				url: window.APP.siteUrl + 'adm/rep_trainees/get_data_company',
				dataType: "JSON",
				data: {
					program_name: program_name,
					program_class_name: class_name,
					program_type_name: type_name,
					batch_name: batch_name
				},
				success: function (data) {

					$("#company").html("<option value=''>(Semua)</option>");
					$.each(data, function (i, d) {
						$('#company').append('<option value="' + d.company_id + '">' + d.company_name + '</option>');
					});

				},
			});
		},

		getDataTrainee: function(program_name, class_name, type_name, batch_name, company_name) {
			var that = this;

			$.ajax({
				type: "POST",
				url: window.APP.siteUrl + 'adm/rep_trainees/get_data_trainee',
				dataType: "JSON",
				data: {
					program_name: program_name,
					program_class_name: class_name,
					program_type_name: type_name,
					batch_name: batch_name,
					company_name: company_name
				},
				success: function (data) {

					$("#trainee").html("<option value=''>(Semua)</option>");
					$.each(data, function (i, d) {
						$('#trainee').append('<option value="' + d.trainee_id + '">' + d.trainee_name + '</option>');
					});

				},
			});
		},

		handleProgram: function () {
			var that = this;
			var vue = that.initVue;

			$(that.elProgram).change(function (e) {
				var value = $(e.target).val();
				var text_program = $(that.elProgram + ' option:selected').text();

				that.getDataClass(text_program);
				that.getDataType(text_program);
				that.getDataBatch(text_program);
				that.getDataCompany(text_program);
				that.getDataTrainee(text_program);
				
			})
		},
		
		handleClass: function () {
			var that = this;
			var vue = that.initVue;

			$(that.elProgramClass).change(function (e) {
				var value = $(e.target).val();
				var text_program = $(that.elProgram + ' option:selected').text();
				var text_program_class = $(that.elProgramClass + ' option:selected').text();

				that.getDataType(text_program, text_program_class);
				that.getDataBatch(text_program, text_program_class);
				that.getDataCompany(text_program, text_program_class);
				that.getDataTrainee(text_program, text_program_class);
			})
		},

		handleType: function () {
			var that = this;
			var vue = that.initVue;

			$(that.elProgramType).change(function (e) {
				var value = $(e.target).val();
				var text_program = $(that.elProgram + ' option:selected').text();
				var text_program_class = $(that.elProgramClass + ' option:selected').text();
				var text_program_type = $(that.elProgramType + ' option:selected').text();

				that.getDataBatch(text_program, text_program_class, text_program_type);
				that.getDataCompany(text_program, text_program_class, text_program_type);
				that.getDataTrainee(text_program, text_program_class, text_program_type);
			})
		},

		handleBatch: function () {
			var that = this;
			var vue = that.initVue;

			$(that.elBatch).change(function (e) {
				var value = $(e.target).val();
				var text_program = $(that.elProgram + ' option:selected').text();
				var text_program_class = $(that.elProgramClass + ' option:selected').text();
				var text_program_type = $(that.elProgramType + ' option:selected').text();
				var text_batch   = $(that.elBatch + ' option:selected').text();

				that.getDataCompany(text_program, text_program_class, text_program_type, text_batch);
				that.getDataTrainee(text_program, text_program_class, text_program_type, text_batch);
			})
		},

		handleCompany: function () {
			var that = this;
			var vue = that.initVue;

			$(that.elCompany).change(function (e) {
				var value = $(e.target).val();
				var text_program = $(that.elProgram + ' option:selected').text();
				var text_program_class = $(that.elProgramClass + ' option:selected').text();
				var text_program_type = $(that.elProgramType + ' option:selected').text();
				var text_batch   = $(that.elBatch + ' option:selected').text();
				var text_company   = $(that.elCompany + ' option:selected').text();

				that.getDataTrainee(text_program, text_program_class, text_program_type, text_batch, text_company);
			})
		},

	}

})(jQuery);