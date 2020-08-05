<?php

if (file_exists("data.txt")) {
    $jsonClientes = file_get_contents("data.txt");//abre el contenido del 
    $aClientes = json_decode($jsonClientes, true);
} else {
   $aClientes = array();
}

$id = isset($_GET["id"]) ? $_GET["id"] : '';

if(isset($_GET["id"]) && isset($_GET["do"]) && $_GET["do"] == "eliminar"){
    echo "archivos/" . $aClientes[$id]["imagen"];
    unlink("archivos/" . $aClientes[$id]["imagen"]);
    unset($aClientes[$id]);
    
    
    
    $jsonClientes = json_encode($aClientes);
    file_put_contents("data.txt", $jsonClientes);
}

if($_POST){

    $dni = trim($_POST["txtDni"]);
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $nombreImagen = "";// en caso de que no se pongan imagenes

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = $nombreAleatorio . "." . $extension;
        move_uploaded_file($archivo_tmp, "archivos/$nombreImagen");
        //renombra la imagen y la mueve de la carpeta temporal a la carpeta archivos
    }


    if(isset($_GET["id"])){
        //Si hay una imagen eliminarla, siempre y cuando se suba una imagen sino no borrar nada
        $imagenAnterior = $aClientes[$id]["imagen"];

        if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
            if($imagenAnterior != ""){
                unlink("archivos/$imagenAnterior");
            }
        }        
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nombreImagen = $imagenAnterior;
        }

        //Actualizada
        $aClientes[$id] = array("dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen
        );
    } else {
        //Es nuevo
        $aClientes[] = array("dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen
        );
    }
    
    $jsonClientes = json_encode($aClientes); //Convierte el array en json

    //Guardar el json en un file_put_contents en el archivo data.txt
    file_put_contents("data.txt", $jsonClientes);
    $id = "";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="css/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="css/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center py-3">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-12">
            <!-- //permite subir multiples archivos -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtDni">DNI:</label>
                            <!-- //sirve para completar los label cuando se quiere modificar un registro para que aparezca ese mismo en los label -->
                            <input type="text" id="txtDni" name="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ''; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre:</label>
                            <input type="text" id="txtNombre" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ''; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ''; ?>"> 
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo:</label>
                            <input type="text" id="txtCorreo" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : '';?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Archivo adjunto:</label>
                            <?php if(isset($aClientes[$id])){ ?>
                             <div class="col-sm-4 col-12">
                                <img src="archivos/<?php echo $aClientes[$id]["imagen"]; ?>" class="img-thumbnail"><?php echo $aClientes[$id]["imagen"]; ?>
                             </div>
                             <div class="col-sm-8 col-12 align-center">
                                <input type="file" id="archivo" name="archivo" class="form-control"> 
                             </div>
                            <?php } else { ?>
                                <input type="file" id="archivo" name="archivo" class="form-control">
                            <?php } ?>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="center">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($aClientes as $key => $cliente): ?>
                    <tr>
                            <td><img src="archivos/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td style="width: 110px;">
                            <a href="index.php?id=<?php echo $key ?>"><i class="fas fa-edit"></i></a>
                            <a href="index.php?id=<?php echo $key ?>&do=eliminar"><i class="fas fa-trash-alt"></i></a>
                    </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <a href="index.php"><i class="fas fa-plus"></i></a>
            </div>
        </div>
    </div>
    
</body>
</html>