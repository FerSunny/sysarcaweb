		$(document).ready(function(){
			
			listar();
			//guardar();
			//eliminar();
	//		getData();
			register();
			editRegister();
			deleteRow();
		});

		function deleteRow(){
			$('#dt_agenda tbody').on( 'click', '#delete ', function () {
				var data =$('#dt_agenda').DataTable().row( $(this).parents('tr') ).data();
				

				swal({
					title: '¿Eliminar Registro?',
					showCancelButton: true,
					showLoaderOnConfirm: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Si!',
					type: 'info',
					preConfirm: function() {
							return new Promise(function(resolve, reject) {
									setTimeout(function() {
										$.ajax({
											url:"./services/delete_row.php",
										   type: 'POST',
										   data:{datas:JSON.stringify({"id_factura":data.id_factura,"id_studio":data.fk_id_estudio})},
										   dataType: "json",
											success: function(datas){
												$('#dt_agenda').DataTable().ajax.reload();
												resolve();
											},
											   error:function(xhr, status, error){
												   console.log(xhr.responseText);
													swal(
														'Oops...',
														'Error del servidor',
														xhr.responseText
													)
											   }
										}) //fin del ajax
									}, 300)
							})
					},
					allowOutsideClick: false
					}).then(function(datoReturn) {
						swal({
							title: '<i>Se elimino el registro correctamente</i>',
							type: 'success',
							showCloseButton: true,
							showCancelButton: true,
							focusConfirm: false,
							confirmButtonText:
								'<a  class="fa fa-thumbs-up"></a> Ok!',
							confirmButtonAriaLabel: 'Thumbs up, great!',
							});
			
					});
			
			});	
		}

		function getData(){
			var table = $('#dt_agenda').DataTable();
			$('#dt_agenda tbody').on( 'click', '#editar', function () {
					
					 var data =$('#dt_agenda').DataTable().row( $(this).parents('tr') ).data();
					 console.log("data",data);
	
	
			});
		}

		function register(){
			$('#dt_agenda tbody').on( 'click', '#register ', function () {
				var data =$('#dt_agenda').DataTable().row( $(this).parents('tr') ).data();
				console.log("data",data);
				if(data.Registrado=='Si'){
					swal({
						type: 'info',
						title: 'Este Registro ya fue capturado',
					})	
				}else{
					var obj={
						v_id:data.id_factura,
						v_fk_id_estudio:data.fk_id_estudio
					};
					// var obj={
					// 	v_id:1727,
					// 	v_fk_id_estudio:485
					// };
					$.ajax({
						url:'./formularios/frm_registro.php',
						data:{datas:JSON.stringify(obj)},
						type: 'POST',
						success:function(data){
							$('.container').empty();
							$("#styloRemove").attr("disabled", "disabled");
							$(".container").append(data);
		
							//load javascript
							var s = document.createElement("script");
								s.type = "text/javascript";
								s.src = "./js/frm_registro.js";
								$("head").append(s);
	
						},
						error:function(xhr, status, error){
							console.log("click");
							console.log(xhr.responseText);
						}
					});
				}
			});
		}


		function editRegister(){
			$('#dt_agenda tbody').on( 'click', '#edit ', function () {
				var data =$('#dt_agenda').DataTable().row( $(this).parents('tr') ).data();
				
				if(data.Registrado=='Si'){
					var obj={
						v_id:data.id_factura,
						v_fk_id_estudio:data.fk_id_estudio
					};
					
					$.ajax({
						url:'./formularios/frm_registro_update.php',
						data:{datas:JSON.stringify(obj)},
						type: 'POST',
						success:function(data){
							$('.container').empty();
							$("#styloRemove").attr("disabled", "disabled");
							$(".container").append(data);
		
							//load javascript
							var s = document.createElement("script");
								s.type = "text/javascript";
								s.src = "./js/frm_registro.js";
								$("head").append(s);

							$('#btn_guardar').attr('id','btn_update');
							
							$.ajax({
								url:'./services/get_data_for_update.php',

								data:{datas:JSON.stringify({'id_factura':obj.v_id,'id_estudio':obj.v_fk_id_estudio})},
								//data:{datas:JSON.stringify({'id_factura':obj.v_id})},
								type: 'POST',
								success:function(data){
									var dataTable = $('#t_plantilla2').DataTable();
									var dataForTable = dataTable
									.rows()
									.data();
									
									for (var i=0;i<dataForTable.length;i++){
										dataForTable[i][3]=dataForTable[i][3].substring(0,dataForTable[i][3].length-1)+'value="'+data.array_datos[i]+'">';
										dataForTable[i][4] =dataForTable[i][4].substring(0,dataForTable[i][4].length-1)+'value="'+data.array_verificados[i]+'">';
										dataTable.row(i).data(dataForTable[i]).draw();	
									}
									$('#observaciones').val(data.comentarios);
								}
							});

	
						},
						error:function(xhr, status, error){
							console.log("click");
							console.log(xhr.responseText);
						}
					});
				}else{
					swal({
						type: 'info',
						title: 'Es necesario registrar primero',
					})
				}
			});
		}


		$("#btn_listar").on("click", function(){
			listar();
		});

// listar datos en la tabla de perfiles
		function listar(){
			var table=$('#dt_agenda').DataTable({
								"createdRow": function ( row, data, index ) {
					
              		$.post("./services/validar.php",{'id_factura' : data.id_factura,'fk_id_estudio' : data.fk_id_estudio} , function(dat,status)
                	{
                		var email = data.email
                    	dat = jQuery.parseJSON(dat);
                      	var val = dat.var
                    	if(val == 0)
                    	{
                    		$('.btn_print', row).css("display", "none")
                      		$('.btn_noprint', row).css("display", "block")
                      		
                    	}else
                    	if(val == 1 && email == '' || email == null)
                    	{
                      		$('.btn_print', row).css("display", "block")
                      		$('.btn_noprint', row).css("display", "none")
                    	}else
                    	{
                    		$('.btn_print', row).css("display", "none")
                      		$('.btn_noprint', row).css("display", "noe")
                    	}

                	});

            	},
				processing: true,
				serverSide: false,
				lengthMenu: [10, 25, 50],
				select: true,
				"ajax":{
					"url":"listar.php",
					"type": "POST",
				// 	success: function(response) {
				// 		console.log(response);
				
				// },
				// error:function(xhr, status, error){
				// 	console.log("click");
				// 	console.log(xhr.responseText);
				// }
				},
				"columns":[
					{"data" : "id_factura"},
					//{"data" : "estado"},
					{"data" : "fecha_factura"},
					//{"data" : "hora_entrega"},
					{"data" : "sucursal"},
					{"data" : "paciente"},
					{"data" : "estudio"},
					{"data" : "Registrado"},
					{"data" : "fecha_registro"},
                    {"data" : "num_imp"},
                    {"data" : "fecha_impresion"},
					{
						render:function(data,type,row){
							return "<form-group style='text-align:center;'>"+
							"<button  id='register'  type='button' class='btn btn-success btn-md'><i class='fas fa-file-alt'></i></button>"+
							"</form-group>";
							},
					},
					{
						render:function(data,type,row){
				            var perfil;
							perfil=row['perfil'];
							email=row['email'];
							if(perfil==1){
    							return "<form-group style='text-align:center;'>"+
    							"<button  id='edit'  type='button' class='btn btn-info btn-md'><i class='fas fa-pencil-alt'></i></span></button>"+
    							"</form-group>";
							}else{
							    return "<form-group style='text-align:center;'>"+
    							"<button  id='out'  type='button' class='btn btn-info btn-md'><i class='fas fa-hand-paper'></i></button>"+
    							"</form-group>";
							    
							}
							},
					},
					{
						render:function(data,type,row){
						    var perfil;
							perfil=row['perfil'];
							if(perfil==1){
    							return "<form-group style='text-align:center;'>"+
    							"<a id='delete' style='color:white;' class='btn btn-danger btn-md' role='button'><i class='fas fa-trash-alt'></i></a>"+
    							"</form-group>";
							}else{
							    return "<form-group style='text-align:center;'>"+
    							"<a id='out' style='color:white;' class='btn btn-info btn-md' role='button'><i class='fas fa-hand-paper'></i></a>"+
    							"</form-group>";
							}
							},
					},
					{
						render:function(data,type,row){
							var saldo;
							saldo=row['resta'];
							/*
							return "<form-group style='text-align:center;'>"+
							"<a id='printer' target='_blank' href='./reports/print_result.php?numero_factura="+row['id_factura']+"&studio="+row['fk_id_estudio']+"' class='btn btn-warning btn-md' role='button'><span  class='fa fa-print'></span></a>"+
							"</form-group>";
							*/
							switch(saldo)
							 {
							 	case '0.00':
							 		if(row['Registrado']=='Si' && email == null || email == ""){
											return "<form-group style='text-align:center;'>"+
											"<a id='printer' target='_blank' href='./reports/print_result.php?numero_factura="+row['id_factura']+"&studio="+row['fk_id_estudio']+"' class='btn btn-warning btn-md' role='button'><i class='fas fa-print'></i></a>"+
											"</form-group>";
									}else{
											return "<form-group style='text-align:center;'>"+
											"<a id='printer' target='_blank'  class='btn btn-warning btn-md' role='button'><span  class='fa fa-ban'></span></a>"+
											"</form-group>";
									}
									break;
								default:
									return "<form-group style='text-align:center;'>"+
									"<a id='printer' target='_blank'  class='btn btn-warning btn-md' role='button'><span  class='fa fa-usd'></span></a>"+
									"</form-group>";
							}
							},
					},
					{
						render:function(data,type,row)
						{
							var email = row['email']

							if(email == null || email == '')
							{
								return ''
							}else
							{
								return "<button type='button' class='email btn btn-primary'><i class='fas fa-paper-plane'></i></button>";
							}
						},
					}
				 ],
				 "language": {
					"info":"Mostrando _START_ a _END_ de _TOTAL_ registros",
					"infoEmpty":      "No existen registros",
					"emptyTable":     "No existen registros",
					"search":         "Buscar:",
					"lengthMenu":     "Mostrar _MENU_ registros",
					"paginate": {
						"next":       "Siguiente",
						"previous":   "Anterior"
					},
				},
		 		columnDefs: [
			 	{
					 orderable: false,
					 targets: [2]
			 	}],
			 	order: [[2, 'asc']]
			});

			// var table = $("#dt_agenda").DataTable({
				
			// 	"sRowSelect": "multi",
			// 	"ajax":{
			// 		"method":"POST",
			// 		"url": "/ag_orden_dia/listar.php"
			// 	},
			// 	"columns":[
			// 		{"data" : "id_factura"},
			// 		{"data" : "estado"},
			// 		{"data" : "fecha_factura"},
			// 		{"data" : "hora_entrega"},
			// 		{"data" : "sucursal"},
			// 		{"data" : "paciente"},
			// 		{"data" : "estudio"},
			// 		//{"data" : "unidad_medida"},
					
			// 		{"defaultContent": "<button type='button' class='editar btn btn-primary' data-toggle='modal' data-target='#modalEditar'>.<i class='fa fa-pencil-square-o'></i></button>"},
			// 		{"defaultContent": "<button type='button' class='editar btn btn-primary' data-toggle='modal' data-target='#modalEditar'>.<i class='fa fa-pencil-square-o'></i></button>"},
			// 		//{"defaultContent": "<button type='button' class='eliminar btn btn-danger' data-toggle='modal' data-target='#modalEliminar' ><i class='fa fa-trash-o'></i></button>"}
			// 	],
			// 	"language": idioma_espanol
			// });


			// obtener_data_editar("#dt_agenda tbody", table);
			send_email("#dt_agenda tbody", table);
		}
		var send_email = function(tbody, table){
			$(tbody).on("click", "button.email", function(){
				var data = table.row( $(this).parents("tr") ).data();
				var fac = data.id_factura
				var est = data.fk_id_estudio
				  $.post("./reports/result_email.php",{'numero_factura': fac, 'studio' : est },function(data, status){
				    $.post("./services/send_mail.php",{'numero_factura': fac, 'studio' : est },function(data, status){
					    console.log(data)
					   	if(data == 1)
					    {
					    	Swal.fire('Mensaje enviado')
					    }else
					    {
					    	Swal.fire('Error al enviar ensaje: ' + data)
					    }
					  });
				  });
			});
		}



		var idioma_espanol = {
		    "sProcessing":     "Procesando...",
		    "sLengthMenu":     "Mostrar _MENU_ registros",
		    "sZeroRecords":    "No se encontraron resultados",
		    "sEmptyTable":     "Ningún dato disponible en esta tabla",
		    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
		    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
		    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		    "sInfoPostFix":    "",
		    "sSearch":         "Buscar:",
		    "sUrl":            "",
		    "sInfoThousands":  ",",
		    "sLoadingRecords": "Cargando...",
		    "oPaginate": {
		        "sFirst":    "Primero",
		        "sLast":     "Último",
		        "sNext":     "Siguiente",
		        "sPrevious": "Anterior"
		    },
		    "oAria": {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
		}
