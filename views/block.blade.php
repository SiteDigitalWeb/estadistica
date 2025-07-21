 @extends ('adminsite.layout')
 

  @section('ContenidoSite-01')

   <div class="content-header">
     <ul class="nav-horizontal text-center">
 <li>
       <a href="/sd/stadistics"><i class="gi gi-signal"></i> Estadistica</a>
      </li>
      <li class="active">
       <a href="/sd/stadistics-block"><i class="gi gi-eye_close"></i>IPs Bloqueadas</a>
      </li>
      <li>
       <a href="/sd/stadistics-block/create"><i class="gi gi-floppy_remove"></i> Bloquear IPs</a>
      </li>
     </ul>
    </div>


 <div class="container">
  <?php $status=Session::get('status'); ?>
  @if($status=='ok_create')
   <div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>IP registrada con éxito</strong> CMS...
   </div>
  @endif

  @if($status=='ok_delete')
   <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>IP eliminada con éxito</strong> CMS...
   </div>
  @endif

  @if($status=='ok_update')
   <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>IP actualizado con éxito</strong> CMS...
   </div>
  @endif

 </div>








<div class="container">
  


 <div class="block full">
                            <div class="block-title">
                                <h2><strong>Ips</strong> Bloqueadas</h2>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="example-datatable" class="table table-vcenter table-condensed table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">Ip</th>
                                            <th class="text-center">Estado</th>
                                      
                                    
                                            
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                       @foreach($ips as $ips)
                                        <tr>
                                            <td class="text-center">{{$ips->id}}</td>
                                            <td class="text-center">{{$ips->ip}}</td>
                                             <td class="text-center">Bloqueada</td>
                                            
                                            
                                          
                                            <td class="text-center">
                                            <script language="JavaScript">
                                              function confirmar ( mensaje ) {
                                              return confirm( mensaje );}
                                            </script>
                                            <a href="<?=URL::to('gestion/estadistica/eliminar/');?>/{{$ips->id}}" onclick="return confirmar('¿Está seguro que desea eliminar el registro?')"><span id="tup" data-toggle="tooltip" data-placement="bottom" title="Eliminar IP" class="btn btn-danger"><i class="hi hi-trash sidebar-nav-icon"></i></span></a>

                                            </td>
                                        </tr>
                            			@endforeach
                                        
                                         
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END Datatables Content -->




</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#example-datatable').DataTable();
});
</script>


<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

    <script src="/adminsite/js/pages/tablesDatatables.js"></script>
        <script>$(function(){ TablesDatatables.init(); });</script>
  


  @stop

  
     
    

