﻿<?php
	
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
$id_factura= $_SESSION['id_factura'];
include("../../INVENTARIOs/funciones.php");
$numero_factura= $_SESSION['numero_factura'];
if (isset($_POST['id'])){$id=intval($_POST['id']);}
if (isset($_POST['cantidad'])){$cantidad=intval($_POST['cantidad']);}
if (isset($_POST['precio_venta'])){$precio_venta=floatval($_POST['precio_venta']);}
if (isset($_POST['canal'])){$precio_venta=floatval($_POST['canal']);}
if (isset($_POST['f_pago'])){$precio_venta=floatval($_POST['f_pago']);}

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
if (!empty($id) and !empty($cantidad) and !empty($precio_venta))
{
$insert_tmp=mysqli_query($con, "INSERT INTO detalle_factura (numero_factura, id_producto,cantidad,precio_venta) VALUES ('$numero_factura','$id','$cantidad','$precio_venta')");

}
if (isset($_GET['id']))//codigo elimina un elemento del array
{
$id_detalle=intval($_GET['id']);	
$delete=mysqli_query($con, "DELETE FROM detalle_factura WHERE id_detalle='".$id_detalle."'");
}

?>
<table class="table">
<tr>
	<th class='text-center'>CODIGO</th>
	<th class='text-center'>CANT.</th>
	<th>DESCRIPCION</th>
	<th class='text-right'>PRECIO UNIT.</th>
	<th class='text-right'>PRECIO TOTAL</th>
	<th></th>
</tr>
<?php
	$sumador_total=0;
	$sql=mysqli_query($con, "select * from products, facturas, detalle_factura where facturas.numero_factura=detalle_factura.numero_factura and  facturas.id_factura='$id_factura' and products.id_producto=detalle_factura.id_producto");

		

	while ($row=mysqli_fetch_array($sql))
	{
	$id_detalle=$row["id_detalle"];
	$codigo_producto=$row['codigo_producto'];
	$cantidad=$row['cantidad'];
	$nombre_producto=$row['nombre_producto'];
	
	
	$precio_venta=$row['precio_venta'];
	$precio_venta_f=number_format($precio_venta,2);//Formateo variables
	$precio_venta_r=str_replace(",","",$precio_venta_f);//Reemplazo las comas
	$precio_total=$precio_venta_r*$cantidad;
	$precio_total_f=number_format($precio_total,2);//Precio total formateado
	$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
	$sumador_total+=$precio_total_r;//Sumador


	

		?>
		<tr>
			<td class='text-center'><?php echo $codigo_producto;?></td>
			<td class='text-center'><?php echo $cantidad;?></td>
			<td><?php echo $nombre_producto;?></td>
			<td class='text-right'><?php echo $precio_venta_f;?></td>
			<td class='text-right'><?php echo $precio_total_f;?></td>
			<td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_detalle ?>')"><i class="glyphicon glyphicon-trash"></i></a></td>
		</tr>		
		<?php
	}
	$subtotal=number_format($sumador_total,2,'.','');
	$total_iva=($subtotal * TAX )/100;
	$total_iva=number_format($total_iva,2,'.','');
	$total_factura=$subtotal+$total_iva;
	$update=mysqli_query($con,"update facturas set total_venta='$total_factura' where id_factura='$id_factura'");

		// DOY DE BAJA PRODUCTO *********************************
		//$row2=mysqli_fetch_array($sql)
		//$id_producto=$row2["id_producto"];
		//eliminar_stock($id_producto,$cantidad);
		// GUARDO HISTORIAL *************************************
		//$quantity=$cantidad;
		//$reference="FACTURA";
		//$user_id=$_SESSION['user_id'];
		//$firstname=$_SESSION['firstname'];
		//$nota="$firstname eliminó por FACTURA: $quantity producto(s) al inventario - ref $codigo_producto";
		//$fecha=date("Y-m-d H:i:s");
		//$canal='FACTURA' . $row2["canal"];
		//$f_pago='FACTURA' . $row2["f_pago"];
		
		//guardar_historial($id_producto,$user_id,$fecha,$nota,$reference,$quantity, $canal, $f_pago);//
		
		//********************************************************

	

?>
<tr>
	<td class='text-right' colspan=4>SUBTOTAL $</td>
	<td class='text-right'><?php echo number_format($subtotal,2);?></td>
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>IVA (<?php echo TAX?>)% $</td>
	<td class='text-right'><?php echo number_format($total_iva,2);?></td>
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>TOTAL $</td>
	<td class='text-right'><?php echo number_format($total_factura,2);?></td>
	<td></td>
</tr>

</table>

