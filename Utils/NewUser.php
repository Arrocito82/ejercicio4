<?php
namespace Utils; 
use MongoDB\Client as Mongo;
use MongoDB\Client;
use Utils\DBConnection\DBConnection as Con;
use Utils\MailSender;
use Repositories\ListasRepo;

class NewUser{
    /**
     * Registra un nuevo usuario y envia un correo de verificacion, devuelve boolean segun caso de exito
     *
     * $userName:  Nombre del Nuevo usuario
     * 
     * $fullName:  Nombre completo de la persona
     * 
     * $password:  Contraseña del nuevo usuario
     * 
     * $email:  El email del nuevo usuario
     *  
     */
    public static function RegisterNewUser(String $userName , String $fullName , String $password , String $email){

        $Client = new Mongo(Con::getConnectionString());
        $TempUsersCollection = $Client->grupo03->TempUsers;

        $newToken = md5(time() . $userName . $password);

        $usuarioCollection=$Client->grupo03->Usuario;
        $usuarioResult=$usuarioCollection->find(['email'=>$email])->toArray();
        if(count($usuarioResult)>0)
            return FALSE;
        $result = $TempUsersCollection->insertOne([
            'login'     => $userName,
            'email'     => $email,
            'nombre'    => $fullName,
            'clave'     => $password,
            'tempToken' => $newToken, //Genera un token
        ]);

        if($result->getInsertedCount() > 0){
            $HtmlMessage = Self::getRegisterHtmlMessage($newToken , $fullName);
            $Body = Self::getRegisterBody();
            return MailSender::sendMail( $email ,  $HtmlMessage ,  "Nuevo Registro en " ,  $Body);            
        }
        return FALSE;
    }
    /**
     * Valida el email del nuevo usuario usando un token, devuelve boolean segun validacion
     *
     * $token: Token Md5 generado
     *  
     */
    public static function ValidateNewUser(String $token){

        $Client = new Mongo(Con::getConnectionString());
        $TempUsersCollection = $Client->grupo03->TempUsers;

        $result = $TempUsersCollection->find(['tempToken'=>$token ])->toArray();

        if(count($result) < 1){
            return FALSE;
        }
        $id_fav = ListasRepo::CrearLista("Favoritos");
        settype($id_fav,"string");
        $id_verMasTarde = ListasRepo::CrearLista("Ver Mas Tarde");
        settype($id_verMasTarde,"string");
        $insertResult = $Client->grupo03->Usuario->insertOne([
            'login'     => $result[0]['login'],
            'email'     => $result[0]['email'],
            'nombre'    => $result[0]['nombre'],
            'clave'     => $result[0]['clave'],
            'listas'    => [$id_fav,$id_verMasTarde]
        ]);
        
        if($insertResult->getInsertedCount() > 0){
            $deleteResult = $TempUsersCollection->deleteOne(['tempToken' => $token]);
            return $deleteResult->getDeletedCount();
        }
        
        return TRUE;
    }

    private function getRegisterHtmlMessage(String $token , String $fullName){
        $html ='<main>
                    <p>Bienvenido ' . $fullName . '</p>
                    <p>Para verificar tu registro has click en el siguiente enlace</p>
                    <a href="https://audafreemp3.xyz/validate.php?token=' . $token . '">Verificar</a>
                    <p>Si no te has registrado ingora este mail</p>
                </main>';
        //Se debe cambiar por uno con estilos
        return $html;
    }
    private function getRegisterBody(){
        return 'Nuevo registro'; //Se debe cambiar
    }
}
