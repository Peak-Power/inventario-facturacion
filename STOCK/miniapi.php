<?php
	header('Content-Type: application/json');

	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos

	$active_productos = "active";
	$title = "MiniAPI";

	$jsondata = array();
	
	if ( isset($_GET['all']))
	{
		$query = mysqli_query($con,"select p.id_producto as id, p.precio_producto as precio, p.stock as stock, e.nombre_estado as estado "
									."from products p "
									."inner join estado e on e.id_estado = p.id_estado "
									."order by id_producto");
		while( $rw = mysqli_fetch_array($query) )
		{
			$entry = array(
				'id' => $rw['id'],
				'precio' => $rw['precio'],
				'stock' => $rw['stock'],
				'estado' => $rw['estado']
			);
			array_push($jsondata,$entry);
		}
	}
	else if ( isset($_GET['id']))
	{
		$querystring = "select p.id_producto as id, "
						. "p.codigo_producto as codigo, "
						. "p.nombre_producto as nombre, "
						. "p.precio_producto as precio, "
						. "p.stock as stock, "
						. "c.nombre_categoria as categoria, "
						. "t.descripcion_talle as talle, "
						. "p.modelo as modelo, "
						. "p.codigo_barras as codigo_barras, "
						. "p.img as img "
						.  "from products p "
						.   "inner join estado e on e.id_estado = p.id_estado "
						.   "inner join talles t on t.id_talle = p.id_talle "
						.   "inner join categorias c on c.id_categoria = p.id_categoria "
						.    "where p.id_producto in (";
		$itemids = explode(',', $_GET['id']);
		$itemcount = min( 50, count($itemids) );
		for ( $i=0 ; $i<$itemcount ; $i++ )
		{
			if ( !is_numeric($itemids[$i]) )
				die("SQL injection attempted, stopping");
			if ( $i > 0 )
				$querystring .= ",";
			$querystring .= "'".$itemids[$i]."'";
		}
		$querystring .= ")";
		$query = mysqli_query($con,$querystring);
		while ( $rw = mysqli_fetch_array($query) )
		{
			$entry = array(
				'id' => $rw['id'],
				'codigo' => $rw['codigo'],
				'nombre' => $rw['nombre'],
				'precio' => $rw['precio'],
				'stock' => $rw['stock'],
				'categoria' => $rw['categoria'],
				'talle' => $rw['talle'],
				'modelo' => $rw['modelo'],
				'codigo_barras' => $rw['codigo_barras'],
				'img' => $rw['img']
			);
			array_push($jsondata,$entry);
		}
	}
	
	$jsonstring = json_encode($jsondata);
	echo $jsonstring;
	
	die();
?>