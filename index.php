<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
    <h5><?php include 'conexion.php' ?></h5>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" autocomplete="off">
        <label for="tarea">Tarea</label>
        <div>
            <input type="text" name="tarea" value="<?php if(isset($_SESSION['tarea'])){echo $_SESSION['tarea'];} ?>" required>
        </div>
        <label for="descripcion">Descripción</label>
        <div>
            <textarea name="descripcion" cols="30" rows="5"><?php if(isset($_SESSION['descripcion'])){echo $_SESSION['descripcion'];} ?></textarea>
        </div>
        <label for="estado">Estado</label>
        <div>
            <select name="estado" required>
                <option value=""> Selecciona una opción</option>
                <option value="0" <?php echo (isset($_SESSION['estado']) && $_SESSION['estado'] == 0) ? 'selected': ' ' ?> >En Progreso</option>
                <option value="1" <?php echo (isset($_SESSION['estado']) && $_SESSION['estado'] == 1) ? 'selected': ' ' ?> >Finalizado</option>
            </select>
        </div>

        <input type="hidden" name="id" value="<?php if(isset($_SESSION['id'])){echo $_SESSION['id'];} ?>">

        <input type="submit" value="<?php echo(isset($_SESSION['editar']))?'Editar': 'Agregar' ?>" name="btnGuardar">
        <input type="submit" value="Limpiar" name="btnLimpiar" >
        
    </form>
    
    <hr>
    <?php 
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            
            if (isset($_POST['btnEditar'])) {
                /**  
                Editar 
                */

                $mensaje = "Actualizando la tarea con el ID: ".$_POST['id']."<br>";
                $_SESSION['mensaje'] = $mensaje;

                $id = $_POST['id'];
                $sql = "SELECT * FROM tareas WHERE id='$id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                // output data of each row
                    while($row = $result->fetch_assoc()) {
                        
                        $_SESSION['id'] = $row["id"];
                        $_SESSION['tarea'] = $row["tarea"];
                        $_SESSION['descripcion'] = $row["descripcion"];
                        $_SESSION['estado'] = $row["estado"];
                        $_SESSION['editar'] = 'Editar';
                        header("Location: index.php");
                    }
                    
                } else {
                    echo "No se encontraron datos";
                }
                
                

            }elseif (isset($_POST['btnEliminar'])) {
                /**  
                Eliminar 
                */
                echo "Eliminando la tarea con el ID: ".$_POST['id'];
                $id = $_POST['id'];

                $sql = "DELETE FROM tareas WHERE id='$id'";

                if ($conn->query($sql) === TRUE) {
                echo "<br>Tarea eliminada correctamente";
                } else {
                echo "Error al eliminar la tarea: " . $conn->error;
                }


            }elseif (isset($_POST['btnLimpiar'])) {
                /**  
                Restablecer Formulario 
                */
                echo "Limpiando formulario";
                
                RestablecerFormulario();

            }else{

                $tarea = mysqli_real_escape_string($conn,$_POST['tarea']);
                $descripcion = mysqli_real_escape_string($conn,$_POST['descripcion']);
                $estado = $_POST['estado'];

                if(!empty($_POST['id'])){
                    /**  
                    Actualizar 
                    */
                    $id = $_POST['id'];

                    $sql = "UPDATE tareas SET tarea='$tarea', descripcion='$descripcion', estado='$estado' WHERE id=$id";

                    if ($conn->query($sql) === TRUE) {
                    echo "Tarea actualizada correctamente";
                    } else {
                    echo "Error al actualizar: " . $conn->error;
                    }
                    RestablecerFormulario();
                }else {
                    /**  
                    Insertar 
                    */
                    $sql = "INSERT INTO tareas (tarea, descripcion, estado) VALUES ('$tarea', '$descripcion', $estado)";
            
                    if ($conn->query($sql) === TRUE) {
                        echo "Tarea agregada correctamente";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
            }
        }

        function RestablecerFormulario(){
            unset($_SESSION['id']);
            unset($_SESSION['tarea']);
            unset($_SESSION['descripcion']);
            unset($_SESSION['estado']);
            unset($_SESSION['editar']);
            unset($_SESSION['mensaje']);
            header("Location: index.php");
        }
    ?>
    <p><?php echo isset($_SESSION['mensaje']) ? $_SESSION['mensaje']: '' ?></p>
    <hr>

    <section>
        <table>
            <thead>
            <th>Id</th>
            <th>Tarea</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Opciones</th>
            </thead>
            <tbody>
                <?php
                
                $sql = "SELECT * FROM tareas";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo($row["id"]); ?></td>
                        <td><?php echo($row["tarea"]); ?></td>
                        <td><?php echo($row["descripcion"]); ?></td>
                        <td>
                            <?php
                                switch ($row['estado']) {
                                    case '0':
                                        echo "En Progreso";
                                        break;
                                    case '1':
                                        echo "Finalizado";
                                        break;
                                    
                                    default:
                                        echo "Estado no proporcionado";
                                        break;
                                } 
                                 
                            ?>
                        </td>
                        <td>
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo($row["id"]); ?>">
                            <input type="submit" name="btnEditar"
                                    value="Editar"/>
                            
                            <input type="submit" name="btnEliminar"
                                    value="Eliminar"/>
                        </form>
                        </td>
                    </tr>
                <?php
                }
                } else {
                ?>
                <tr>
                    <td colspan = "5">
                        <?php echo "0 results";?>
                    </td>
                </tr>
                <?php
                }                
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>