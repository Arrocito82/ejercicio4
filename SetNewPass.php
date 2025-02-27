<?php 
    require "Components/header.php";
    use Utils\ResetPassword as Reset;
    
    if(!isset($_SESSION['userId'])){
        header("Location: index.php");
             
    }
    
    if(isset($_POST['pass1']) & isset($_POST['pass2'])){
        $resultReset = Reset::ResetPass($_POST['pass1'] , $_SESSION['userId']);
        if($resultReset){
            
            print('<div class="alert alert-success" role="alert">
            Se ha cambiado la contraseña exitosamente <a  class="btn btn-primary ml-4" href="/">Ir al inicio</a>
                </div>');
        }
        else{
            print('<div class="alert alert-danger" role="alert">
            Ha ocurrido un error, intenta mas tarde <a  class="btn btn-primary ml-4" href="/">Ir al inicio</a>
                </div>');
        }
        unset($_SESSION['userId']);
    }
?>
<div class="container pt-3">

    <form action="SetNewPass.php" method="post" class="mt-3">
        <div class="form-group">
            <label for="pass1">Nueva Contraseña</label>
            <input type="password" name="pass1" class="form-control" id="pass1" aria-describedby="emailHelp" placeholder="Ingrese la nueva contraseña">
            
        </div>
        <div class="form-group">
            <label for="pass2">Repetir contraseña</label>
            <input type="password" name="pass2" class="form-control" id="pass2" aria-describedby="emailHelp" placeholder="Confirme la nueva contraseña">
            
        </div>
        
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>