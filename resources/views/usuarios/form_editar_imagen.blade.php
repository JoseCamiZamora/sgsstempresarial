  
  <form   action="{{ url('editar_imagen') }}"  method="post" id="f_editar_imagen" style="margin-top:35px;">
          <input type="hidden" name="_token" id='_token_avatar' value="<?php echo csrf_token(); ?>"> 
          <input type="hidden" name="id_usuario" id='id_usuario_avatar' value="{{ $usuario->id }}"> 
                    <!-- Quotation -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="inputGroupFileAddon01">Archivo</span>
                        </div>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input input-file" name='file_avatar' id="file_avatar" aria-describedby="inputGroupFileAddon01" accept="image/*"  required>
                          <label class="custom-file-label" id='fileavatar_text' for="fileavatar_text">Seleccione una imagen</label>
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-top:20px;">
                             
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary"  >Actualizar Imagen</button>
                         </div>
                      
                    </div>
              
</form>