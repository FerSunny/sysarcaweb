<?php include("../controladores/conex.php") ?>
<!-- Modal Agregar participaciones -->
  <div class="modal fade" id="myModals" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Nueva Dosis</h4>
        </div>
        <div class="modal-body">
          <table>
            <form id="login-form" class="text-left" action="controladores/registro_dosis.php" method="post">



            <tr>
            <div class="form-group row" align="">
              <div class="col-xs-8">
                <td><label>Descripcion de la dosis</label></td>
                <td><input type="text" class="form-control" id="fi_dosis" required name="fn_dosis"  maxlength="50" size="40" placeholder="Descripcion de la dosis"></td>
              </div>
            </div>
            </tr>

            <tr>
              <td>  <label for="" class="col-sm-2 control-label">Estado:</label></td>
              <td>  <div class="form-group">

                  <select class="col-sm-3 selectpicker" name="fn_estado" id="fi_estado">
                    <option value="A">Activo</option>
                    <option value="S">Suspendido</option>
                  </select>
                </div></td>
            </tr>
            </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="btniniciar"  >Ingresar</button>
          <button type="submit" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!--Editar Participacion-->
<form id="frmedit" class="form-horizontal" action="controladores/actualizar.php" method="POST">
<div class="row">
  <div id="cuadro1" class="col-sm-12 col-md-12 col-lg-12 ocultar">
      <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalEliminarLabel">Editar Dosis</h4>
            </div>
            <div class="modal-body">

                <input type="hidden" id="id_dosis" name="id_dosis" value="0">
                <input type="hidden" id="opcion" name="opcion" value="modificar">

                    <div class="form-group">
                      <label for="Descripcion de la dosis" class="col-sm-2 control-label">Descripcion de la dosis </label>
                      <div class="col-sm-8"><input id="fi_dosis" name="fn_dosis" maxkength="50" required type="text" class="form-control" ></div>
                    </div>

                    <div class="form-group">
                      <label for="estado" class="col-sm-2 control-label">Estado:</label>
                      <select class="col-sm-3 selectpicker" name="fn_estado" id="fi_estado">
                        <option value="A">Activo</option>
                        <option value="S">Suspendido</option>
                      </select>
                    </div>
            <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">


          </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="btniniciar"  >Ingresar</button>

        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>
    </div>
  </div>
</div>
</form>

<!-- Modal Eliminar-->
<form id="frmEliminaredocivil" action="controladores/eliminar_dosis.php" method="POST">
      <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalEliminarLabel">Eliminar Dosis</h4>
            </div>
            <div class="modal-body">
              
                ¿Está seguro de eliminar la Dosis?<strong data-name=""></strong>
                    <input type="hidden" id="id_dosis" name="id_dosis" value="">
                    <input type="hidden" id="opcion" name="opcion" value="eliminar">
                    <div class="form-group">
                      <div class="col-sm-8">
                        <input id="fi_dosis" name="fn_dosis" type="text" class="form-control" maxlength="8" >
                      </div>
                    </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" id="btniniciar"  >Aceptar</button>
            <button type="submit" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
           
          </div>
        </div>
      </div>
      <!-- Modal -->
    </form>
