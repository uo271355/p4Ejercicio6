<!DOCTYPE  html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ejercicio 6</title>
<link rel="stylesheet" href="Ejercicio6.css">
</head>
<body>
<header>
<h1>Gestion de la base de datos</h1>
</header>
<section>
<pre>
<code>
<?php
class BaseDatos{
	private $nombreServidor;
    private $usuario;
    private $contraseña;
    private $nombreBDatos;
	
	public function __construct(){
		session_start();
		$this->nombreServidor="localhost";
		$this->usuario="DBUSER2020";
		$this->contraseña="DBPSWD2020";
		$this->nombreBDatos="BDatosSEW";
	}
	public function inicializar(){
		if (count($_POST)>0) { 
            if(isset($_POST["crearB"])){
                $this->crearBaseDatos();
            }else if(isset($_POST["crearT"])){
                $this->crearTabla();
            }else if(isset($_POST["insertar"])){
                $this->insertar();
            }else if(isset($_POST["buscar"])){
                $this->buscar();
            }else if(isset($_POST["modificar"])){
                $this->modificar();
            }else if(isset($_POST["eliminar"])){
                $this->eliminar();
            }else if(isset($_POST["generar"])){
                $this->generar();
            }else if(isset($_POST["exportar"])){
                $this->exportar();
            }else if(isset($_POST["cargar"])){
                $this->cargar();
            }
		}
	}
	public function crearBaseDatos(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña);
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$creaBDSQL="create database if not exists BDatosSEW collate utf8_spanish_ci";
		
		if($baseDatos->query($creaBDSQL) !== TRUE){
			exit ("<p>ERROR EN BASE DE DATOS:  BDatosEjercicio7 </p>");  
		}else{
			echo "<h4>Creada BDatosSEW con exito</h4>";
		}
		
		$baseDatos->close();
	}
	public function crearTabla(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		$crearTablaSQL="create table if not exists PruebasUsabilidad(
			dni varchar(9) not null,
			nombre varchar(32) not null,
			apellidos varchar(255) not null,
			correo varchar(255) not null,
			telefono int not null,
			edad int not null,
			genero enum ('Mujer','Hombre','No binario','Otro') not null,
			nivel int not null,
			tiempo int not null,
			encuesta enum ('Si','No') not null,
			comentarios varchar(455) not null,
			mejoras varchar(455) not null,
			valoracion int not null,
			primary key(dni),
			check(nivel>=0 && nivel<=10),
			check(valoracion>=0 && valoracion<=10)
		)";
		
		if($baseDatos->query($crearTablaSQL) !== TRUE){
			exit ("<p>ERROR AL CREAR TABLA: PruebasUsabilidad </p>");  
		}else{
			echo "<h4>Creada tabla PruebasUsabilidad con exito</h4>";
		}
		
		$baseDatos->close();
	}
	public function insertar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$this->compruebaCampos();
		
		$insertarSQL=$baseDatos->prepare("insert into PruebasUsabilidad (dni,nombre,apellidos,correo,telefono,edad,genero,nivel,tiempo,encuesta,comentarios,mejoras,valoracion) values (?,?,?,?,?,?,?,?,?,?,?,?,?)");
		
		$insertarSQL->bind_param('ssssiisiisssi',$_POST["dni"],$_POST["nombre"],$_POST["apellidos"],$_POST["correo"],$_POST["telefono"],$_POST["edad"],$_POST["genero"],$_POST["nivel"],$_POST["tiempo"],$_POST["encuesta"],$_POST["comentarios"],$_POST["mejoras"],$_POST["valoracion"]);
		
		$insertarSQL->execute();
		echo "<h4>Se han insertado los datos</h4>";
		$insertarSQL->close();
		$baseDatos->close();
	}
	
	public function compruebaCampos(){
		if(strlen($_POST["dni"])!=9){
			echo "<p class='error'>El campo dni tiene el siguiente formato XXXXXXXXZ. (Siendo X un numero y Z una letra)</p>";
			return;
		}else if(strlen($_POST["nombre"])==0){
			echo "<p class='error'>El campo nombre esta vacio</p>";
			return;
		}else if(strlen($_POST["apellidos"])==0){
			echo "<p class='error'>El campo apellidos esta vacio</p>";
			return;
		}else if(strlen($_POST["correo"])==0){
			echo "<p class='error'>El campo correo esta vacio</p>";
			return;
		}else if(strpos($_POST["correo"],"@") === False){
			echo "<p class='error'>Al correo electronico le falta un @</p>";
			return;
		}else if(strlen($_POST["telefono"])==0){
			echo "<p class='error'>El campo telefono esta vacio</p>";
			return;
		}else if(strlen($_POST["telefono"])==0){
			echo "<p class='error'>El campo telefono esta vacio</p>";
			return;
		}else if(strlen($_POST["edad"])==0){
			echo "<p class='error'>El campo edad esta vacio</p>";
			return;
		}else if(intval($_POST["edad"]) <= 1 || intval($_POST["edad"]) >= 111){
			echo "<p class='error'>La edad tiene que estar entre este intervalo [1-111]</p>";
			return;
		}else if($_POST["genero"]!='Mujer'&&$_POST["genero"]!='Hombre'&&$_POST["genero"]!='No binario'&&$_POST["genero"]!='Otro'){
			echo "<p class='error'>El campo genero esta vacio o es incorrecto</p>";
			return;
		}else if(intval($_POST["nivel"])<0||intval($_POST["nivel"])>10){
			echo "<p class='error'>El nivel tiene que estar entre este intervalo [1-10]</p>";
			return;
		}else if(strlen($_POST["tiempo"])==0){
			echo "<p class='error'>El campo tiempo esta vacio</p>";
			return;
		}else if($_POST["encuesta"]!='Si'&&$_POST["encuesta"]!='No'){
			echo "<p class='error'>El campo encuesta esta vacio o es incorrecto</p>";
			return;
		}else if(strlen($_POST["comentarios"])==0){
			echo "<p class='error'>El campo comentarios esta vacio</p>";
			return;
		}else if(strlen($_POST["mejoras"])==0){
			echo "<p class='error'>El campo mejoras esta vacio</p>";
			return;
		}else if(intval($_POST["valoracion"])<0||intval($_POST["valoracion"])>10){
			echo "<p class='error'>La valoracion tiene que estar entre este intervalo [1-10]</p>";
			return;
		}
		
	}
	public function buscar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$buscarSQL=$baseDatos->prepare('select * from PruebasUsabilidad where dni=?');
		
		if(strlen($_POST["dniB"])==9){
			$buscarSQL->bind_param('s',$_POST["dniB"]);
			$buscarSQL->execute();
			$resultadoBusqueda = $buscarSQL->get_result();
			if($resultadoBusqueda->fetch_assoc()==NULL){
				echo "<p class='error'>No se ha podido realizar la busqueda</p>";
			}else{
				echo "<h5>La busqueda se ha realizado correctamente</h5>";
				$resultadoBusqueda->data_seek(0);
				while($fila=$resultadoBusqueda->fetch_assoc()){
					$mostrarBusqueda='<ul>';
					$mostrarBusqueda.='<li> DNI: '.$fila["dni"].'</li>';
					$mostrarBusqueda.='<li> Nombre: '.$fila["nombre"].'</li>';
					$mostrarBusqueda.='<li> Apellidos: '.$fila["apellidos"].'</li>';
					$mostrarBusqueda.='<li> Correo: '.$fila["correo"].'</li>';
					$mostrarBusqueda.='<li> Telefono: '.$fila["telefono"].'</li>';
					$mostrarBusqueda.='<li> Edad: '.$fila["edad"].'</li>';
					$mostrarBusqueda.='<li> Genero: '.$fila["genero"].'</li>';
					$mostrarBusqueda.='<li> Nivel: '.$fila["nivel"].'</li>';
					$mostrarBusqueda.='<li> Tiempo: '.$fila["tiempo"].'</li>';
					$mostrarBusqueda.='<li> Encuesta: '.$fila["encuesta"].'</li>';
					$mostrarBusqueda.='<li> Comentarios: '.$fila["comentarios"].'</li>';
					$mostrarBusqueda.='<li> Mejoras: '.$fila["mejoras"].'</li>';
					$mostrarBusqueda.='<li> Valoracion: '.$fila["valoracion"].'</li>';
					$mostrarBusqueda.='</ul>';
					
					echo $mostrarBusqueda;    
				}
			}
		}else{
			echo "<p class='error'>El campo dni tiene el siguiente formato XXXXXXXXZ. (Siendo X un numero y Z una letra)</p>";
			return;
		}
		$buscarSQL->close();
		$baseDatos->close();
	}
	
	public function modificar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$this->compruebaCamposAModificar();
		$modificarQL=$baseDatos->prepare('update PruebasUsabilidad set nombre=?,apellidos=?,correo=?,telefono=?,edad=?,genero=?,nivel=?,tiempo=?,encuesta=?,comentarios=?,mejoras=?,valoracion=? where dni=?');
		
		$modificarQL->bind_param('sssiisiisssis',$_POST["nombreM"],$_POST["apellidosM"],$_POST["correoM"],$_POST["telefonoM"],$_POST["edadM"],$_POST["generoM"],$_POST["nivelM"],$_POST["tiempoM"],$_POST["encuestaM"],$_POST["comentariosM"],$_POST["mejorasM"],$_POST["valoracionM"],$_POST["dniM"]);
		
		$modificarQL->execute();
		
		$modificarQL->close();
		$baseDatos->close();
	}
	public function compruebaCamposAModificar(){
		if(strlen($_POST["dniM"])!=9){
			echo "<p class='error'>El campo dni tiene el siguiente formato XXXXXXXXZ. (Siendo X un numero y Z una letra)</p>";
			return;
		}else if(strlen($_POST["nombreM"])==0){
			echo "<p class='error'>El campo nombre esta vacio</p>";
			return;
		}else if(strlen($_POST["apellidosM"])==0){
			echo "<p class='error'>El campo apellidos esta vacio</p>";
			return;
		}else if(strlen($_POST["correoM"])==0){
			echo "<p class='error'>El campo correo esta vacio</p>";
			return;
		}else if(strpos($_POST["correoM"],"@") === False){
			echo "<p class='error'>Al correo electronico le falta un @</p>";
			return;
		}else if(strlen($_POST["telefonoM"])==0){
			echo "<p class='error'>El campo telefono esta vacio</p>";
			return;
		}else if(strlen($_POST["telefonoM"])==0){
			echo "<p class='error'>El campo telefono esta vacio</p>";
			return;
		}else if(strlen($_POST["edadM"])==0){
			echo "<p class='error'>El campo edad esta vacio</p>";
			return;
		}else if(intval($_POST["edadM"]) <= 1 || intval($_POST["edadM"]) >= 111){
			echo "<p class='error'>La edad tiene que estar entre este intervalo [1-111]</p>";
			return;
		}else if($_POST["generoM"]!='Mujer'&&$_POST["generoM"]!='Hombre'&&$_POST["generoM"]!='No binario'&&$_POST["generoM"]!='Otro'){
			echo "<p class='error'>El campo genero esta vacio o es incorrecto</p>";
			return;
		}else if(intval($_POST["nivelM"])<0||intval($_POST["nivelM"])>10){
			echo "<p class='error'>El nivel tiene que estar entre este intervalo [1-10]</p>";
			return;
		}else if(strlen($_POST["tiempoM"])==0){
			echo "<p class='error'>El campo tiempo esta vacio</p>";
			return;
		}else if($_POST["encuestaM"]!='Si'&&$_POST["encuestaM"]!='No'){
			echo "<p class='error'>El campo encuesta esta vacio o es incorrecto</p>";
			return;
		}else if(strlen($_POST["comentariosM"])==0){
			echo "<p class='error'>El campo comentarios esta vacio</p>";
			return;
		}else if(strlen($_POST["mejorasM"])==0){
			echo "<p class='error'>El campo mejoras esta vacio</p>";
			return;
		}else if(intval($_POST["valoracionM"])<0||intval($_POST["valoracionM"])>10){
			echo "<p class='error'>La valoracion tiene que estar entre este intervalo [1-10]</p>";
			return;
		}
	}
	public function eliminar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$eliminarSQL=$baseDatos->prepare('delete from PruebasUsabilidad where dni=?');
		
		if(strlen($_POST["dniE"])==9){
			$eliminarSQL->bind_param('s',$_POST["dniE"]);
			$eliminarSQL->execute();			
		}else{
			echo "<p class='error'>El campo dni tiene el siguiente formato XXXXXXXXZ. (Siendo X un numero y Z una letra)</p>";
			return;
		}
		$eliminarSQL->close();
		$baseDatos->close();
	}
	public function generar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		$edadMediaUsuarios=$this->calculaMediaEdad($baseDatos);
		$porcentajeGenero=$this->calcularPorcentajes($baseDatos);
		list($mediaMujeres, $mediaHombres, $mediaNoBinarios, $mediaOtros) = $porcentajeGenero;
		$nivelMedio=$this->calcularMediaNivel($baseDatos);
		$tiempoMedio=$this->calcularTiempoMedio($baseDatos);
		$porcentajeEncuesta=$this->calcularPorcentajesEncuesta($baseDatos);
		list($mediaSi,$mediaNo)=$porcentajeEncuesta;
		$valoracionMedia=$this->calcularMediaValoracion($baseDatos);
		
		$mostrarInforme='<h4>Informe</h4>';
		$mostrarInforme.='<ul>';
		$mostrarInforme.='<li> Edad media: '.$edadMediaUsuarios.'</li>';
		$mostrarInforme.='<li> Media de mujeres: '.$mediaMujeres.' %</li>';
		$mostrarInforme.='<li> Media de hombres: '.$mediaHombres.' %</li>';
		$mostrarInforme.='<li> Media de no binarios: '.$mediaNoBinarios.' %</li>';
		$mostrarInforme.='<li> Media de otros: '.$mediaOtros.' %</li>';
		$mostrarInforme.='<li> Nivel medio: '.$nivelMedio.'</li>';
		$mostrarInforme.='<li> Tiempo medio: '.$tiempoMedio.'</li>';
		$mostrarInforme.='<li> Tarea realizada correctamente: '.$mediaSi.' %</li>';
		$mostrarInforme.='<li> Tarea realizada incorrectamente: '.$mediaNo.' %</li>';
		$mostrarInforme.='<li> Valoracion media: '.$valoracionMedia.'</li>';
		$mostrarInforme.='</ul>';
		
		echo $mostrarInforme;   
		
		$baseDatos->close();
	}
	public function calculaMediaEdad($baseDatos){
		$edadSQL=$baseDatos->prepare("select edad from PruebasUsabilidad");
		$edadSQL->execute();
		$resultadoBusqueda = $edadSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$sumaEdades=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $sumaEdades += $fila['edad'];
			}
			$media=$sumaEdades/$contador;
			return $media;
		}
		
		$edadSQL->close();
	}
	
	public function calcularPorcentajes($baseDatos){
		$generoSQL=$baseDatos->prepare("select genero from PruebasUsabilidad");
		$generoSQL->execute();
		$resultadoBusqueda = $generoSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$mujeres=0;
			$hombres=0;
			$noBinarios=0;
			$otros=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $genero= $fila['genero'];
				if(strcasecmp($genero,"mujer")==0){
					 $mujeres+=1;
				}else if(strcasecmp($genero,"hombre")==0){
					 $hombres+=1;
				}else if(strcasecmp($genero,"no binario")==0){
					 $noBinarios+=1;
				}else if(strcasecmp($genero,"otro")==0){
					 $otros+=1;
				}
			}
			$mediaMujeres=($mujeres/$contador)*100;
			$mediaHombres=($hombres/$contador)*100;
			$mediaNoBinarios=($noBinarios/$contador)*100;
			$mediaOtros=($otros/$contador)*100;
			return array($mediaMujeres, $mediaHombres, $mediaNoBinarios, $mediaOtros);
		}
		
		$generoSQL->close();
	}
	public function calcularMediaNivel($baseDatos){
		$nivelSQL=$baseDatos->prepare("select nivel from PruebasUsabilidad");
		$nivelSQL->execute();
		$resultadoBusqueda = $nivelSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$sumaNiveles=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $sumaNiveles += $fila['nivel'];
			}
			$media=$sumaNiveles/$contador;
			return $media;
		}
		
		$nivelSQL->close();
	}
	public function calcularTiempoMedio($baseDatos){
		$tiempoSQL=$baseDatos->prepare("select tiempo from PruebasUsabilidad");
		$tiempoSQL->execute();
		$resultadoBusqueda = $tiempoSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$sumaTiempo=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $sumaTiempo += $fila['tiempo'];
			}
			$media=$sumaTiempo/$contador;
			return $media;
		}
		
		$tiempoSQL->close();
	}
	
	public function calcularPorcentajesEncuesta($baseDatos){
		$encuestaSQL=$baseDatos->prepare("select encuesta from PruebasUsabilidad");
		$encuestaSQL->execute();
		$resultadoBusqueda = $encuestaSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$si=0;
			$no=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $encuesta= $fila['encuesta'];
				if(strcasecmp($encuesta,"Si")==0){
					 $si+=1;
				}else if(strcasecmp($encuesta,"No")==0){
					 $no+=1;
				}
			}
			$mediaSi=($si/$contador)*100;
			$mediaNo=($no/$contador)*100;
			return array($mediaSi, $mediaNo);
		}
		
		$encuestaSQL->close();
	}
	public function calcularMediaValoracion($baseDatos){
		$valoracionSQL=$baseDatos->prepare("select valoracion from PruebasUsabilidad");
		$valoracionSQL->execute();
		$resultadoBusqueda = $valoracionSQL->get_result();
		if($resultadoBusqueda->fetch_assoc()==NULL){
			echo "<p class='error'>No se ha podido realizar la busqueda</p>";
		}else{
			
			$resultadoBusqueda->data_seek(0);
			$sumaValoraciones=0;
			$contador=0;
			while($fila=$resultadoBusqueda->fetch_assoc()){
				$contador += 1;
                $sumaValoraciones += $fila['valoracion'];
			}
			$media=$sumaValoraciones/$contador;
			return $media;
		}
		
		$valoracionSQL->close();
	}
	
	public function exportar(){
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		$exportarSQL=$baseDatos->query('select * from PruebasUsabilidad');
		if($exportarSQL->num_rows > 0){
			try{
				$nombreFichero = "pruebasUsabilidad.csv";

				$fichero = fopen($nombreFichero, "w");

				while($fila = $exportarSQL->fetch_assoc()) {
					$linea = array($fila["dni"], $fila["nombre"], $fila['apellidos'], $fila['correo'], $fila['telefono'], $fila['edad'], $fila['genero'], $fila['nivel'], $fila['tiempo'], $fila['encuesta'], $fila['comentarios'], $fila['mejoras'], $fila['valoracion']); 
					fputcsv($fichero, $linea,";");
				} 

				fclose($fichero);
				echo "<h3> El archivo se ha exportado </h3>";

			}catch(Throwable $e){
				echo "<p class='error'>No se ha podido exportar el archivo</p>";
			}
		}
		$baseDatos->close();
	}
	public function cargar(){
		
		$baseDatos = new mysqli($this->nombreServidor,$this->usuario,$this->contraseña,$this->nombreBDatos);
		
		if($baseDatos->connect_error){
			exit ("<p>ERROR CONEXION:".$baseDatos->connect_error."</p>");  
		} else {
			echo "<h4>La conexión se ha establecido correctamente con: " . $baseDatos->host_info . "</h4>";
		}
		
		if($_FILES){
			$nombreFichero = $_FILES["datos"]["name"];
			$informacion = new SplFileInfo($nombreFichero);
			$extension = pathinfo($informacion->getFilename(), PATHINFO_EXTENSION);

			if($extension == "csv"){
				$handle = fopen($nombreFichero, "r"); 

				while(($datos = fgetcsv($handle, 1000, ";")) !== FALSE){
					$cargarSQL = "insert into PruebasUsabilidad (dni, nombre, apellidos, correo, telefono, edad, genero, nivel, tiempo, encuesta, comentarios, mejoras, valoracion) 
						values ('$datos[0]','$datos[1]','$datos[2]','$datos[3]',$datos[4],$datos[5],'$datos[6]',$datos[7],$datos[8],
						'$datos[9]','$datos[10]','$datos[11]',$datos[12])";
					$baseDatos->query($cargarSQL);

					
				}

			  
			}
        }
		$baseDatos->close();
	}
}
$bdatos =new BaseDatos();
$bdatos->inicializar();
?>
</code>
</pre>
</section>
<section class='calculadora'>
<form action='#' method='post' name='clase' enctype='multipart/form-data'>
<h2>Crear base de datos</h2>
<input type='submit' class='button' name ='crearB' value='Crear'/>
<h2>Crear una tabla</h2>
<input type='submit' class='button' name ='crearT' value='Crear'/>
<h2>Insertar Datos</h2>
<p class='info'>Rellena los datos</p>
<div>
<label for='dni'>DNI:		</label> 
<input type='text' id='dni' name ='dni'/>
</div>

<div>
<label for='nombre'>Nombre:		</label> 
<input type='text' id='nombre' name ='nombre'/>
</div>

<div>
<label for='apellidos'>Apellidos:		</label> 
<input type='text' id='apellidos' name ='apellidos'/>
</div>

<div>
<label for='correo'>Correo electronico:		</label> 
<input type='text' id='correo' name ='correo'/>
</div>

<div>
<label for='telefono'>Telefono:		</label> 
<input type='text' id='telefono' name ='telefono'/>
</div>

<div>
<label for='edad'>Edad:		</label> 
<input type='text' id='edad' name ='edad'/>
</div>

<div>
<label for='genero'>Genero [Mujer|Hombre|No binario|Otro]:		</label> 
<input type='text' id='genero' name ='genero'/>
</div>

<div>
<label for='nivel'>Nivel informatico [0-10]:		</label> 
<input type='text' id='nivel' name ='nivel'/>
</div>

<div>
<label for='tiempo'>Tiempo invertido (s):		</label> 
<input type='text' id='tiempo' name ='tiempo'/>
</div>

<div>
<label for='encuesta'>¿Se ha realizado correctamente? [Si|No]:		</label> 
<input type='text' id='encuesta' name ='encuesta'/>
</div>

<div>
<label for='comentarios'>Comentarios:		</label> 
<input type='text' id='comentarios' name ='comentarios'/>
</div> 

<div>
<label for='mejoras'>Mejoras en la aplicacion:		</label> 
<input type='text' id='mejoras' name ='mejoras'/>
</div> 

<div>
<label for='valoracion'>Valoracion de la aplicacion [0-10]:		</label> 
<input type='text' id='valoracion' name ='valoracion'/>
</div> 


<input type='submit' class='button' name ='insertar' value='Insertar'/>

<h2>Buscar en una tabla</h2>
<p class='info'>Rellena el campo para realizar la busqueda</p>
<div>

<label for='dniB'>DNI:		</label> 
<input type='text' id='dniB' name ='dniB'/>
</div>
<input type='submit' class='button' name ='buscar' value='Buscar'/>

<h2>Modificar datos en la tabla</h2>
<p class='info'>Rellena el campo para buscar donde se hara la modificacion</p>
<div>
<label for='dniM'>DNI:		</label> 
<input type='text' id='dniM' name ='dniM'/>
</div>
<p class='info'>Rellena los campos que se van a modificar</p>
<div>
<label for='nombreM'>Nombre:		</label> 
<input type='text' id='nombreM' name ='nombreM'/>
</div>

<div>
<label for='apellidosM'>Apellidos:		</label> 
<input type='text' id='apellidosM' name ='apellidosM'/>
</div>

<div>
<label for='correoM'>Correo electronico:		</label> 
<input type='text' id='correoM' name ='correoM'/>
</div>

<div>
<label for='telefonoM'>Telefono:		</label> 
<input type='text' id='telefonoM' name ='telefonoM'/>
</div>

<div>
<label for='edadM'>Edad:		</label> 
<input type='text' id='edadM' name ='edadM'/>
</div>

<div>
<label for='generoM'>Genero [Mujer|Hombre|No binario|Otro]:		</label> 
<input type='text' id='generoM' name ='generoM'/>
</div>

<div>
<label for='nivelM'>Nivel informatico [0-10]:		</label> 
<input type='text' id='nivelM' name ='nivelM'/>
</div>

<div>
<label for='tiempoM'>Tiempo invertido (s):		</label> 
<input type='text' id='tiempoM' name ='tiempoM'/>
</div>

<div>
<label for='encuestaM'>¿Se ha realizado correctamente? [Si|No]:		</label> 
<input type='text' id='encuestaM' name ='encuestaM'/>
</div>

<div>
<label for='comentariosM'>Comentarios:		</label> 
<input type='text' id='comentariosM' name ='comentariosM'/>
</div> 

<div>
<label for='mejorasM'>Mejoras en la aplicacion:		</label> 
<input type='text' id='mejorasM' name ='mejorasM'/>
</div> 

<div>
<label for='valoracionM'>Valoracion de la aplicacion [0-10]:		</label> 
<input type='text' id='valoracionM' name ='valoracionM'/>
</div> 
<input type='submit' class='button' name ='modificar' value='Modificar'/>

<h2>Eliminar en una tabla</h2>
<p class='info'>Rellena el campo para eliminar lo que desea</p>
<div>
<label for='dniE'>DNI:		</label> 
<input type='text' id='dniE' name ='dniE'/>
</div>
<input type='submit' class='button' name ='eliminar' value='Eliminar'/>


<h2>Generar un Informe</h2>
<input type='submit' class='button' name ='generar' value='Generar'/>

<h2>Exporta tus datos</h2>
<input type='submit' class='button' name ='exportar' value='Exportar'/>

<h2>Carga tus datos</h2>
<label for='datos'>Selecciona tu archivo:		</label> 
<input type="file" id="datos" name="datos" /> 
<input type='submit' class='button' name ='cargar' value='Cargar'/>
</form>
</section>
</body>
</html>