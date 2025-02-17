<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/*
$email='soporte.producto@medisyslabs.onmicrosoft.com';
$atach='';
$asunto="Ha recibido una queja a traves del SYSARCAWEB";
$contenido="Medico: MARIO CRUZ JIMENEZ "."<br>"."Paciente: JULIO CESAR SOLIS CANELA"."<br>". "Queja: 333";


$resultao=envia_email($email,$atach,$asunto,$contenido);

*/

function envia_email($tipo,$email,$atach,$asunto,$contenido){
/*
    echo $email;
    echo $asunto;
    echo $contenido;
    */
    // Mostrar errores PHP (Desactivar en producción)
    /*
    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */

    // Incluir la libreria PHPMailer
    /*
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
*/

        $email_jaz="jaz.carapia@laboratoriosarca.com.mx";
        $email_sop="soporte.producto@medisyslabs.onmicrosoft.com";
        $email_lab="servicios.laboratorio@laboratoriosarca.mx";
        $email_acu="acuses@laboratoriosarca.mx";

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    // Inicio
    $mail = new PHPMailer(true);
    $estenv =0;
    try {
        // Configuracion SMTP
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                         // Mostrar salida (Desactivar en producción)
        $mail->isSMTP();                                               // Activar envio SMTP
        $mail->Host  = 'smtp.ionos.mx';                     // Servidor SMTP
        $mail->SMTPAuth  = true;                                       // Identificacion SMTP
        $mail->Username  = 'atencion.clientes@laboratoriosarca.mx';                  // Usuario SMTP
        $mail->Password  = 'Arca_2023';	          // Contraseña SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port  = 587;
        $mail->setFrom('atencion.clientes@laboratoriosarca.mx', 'Servicio al paciente');                // Remitente del correo

        // Destinatarios

        $mail->addAddress($email, $email);  // Email y nombre del destinatario
        switch ($tipo) {
            case '4':
                $mail->addCC($email_sop,'Acuse SP');
                $mail->addCC($email_jaz,'Acuse JI');
                $mail->addCC($email_lab,'Acuse LB');
                $mail->addAttachment();
                break;
            case '5':
                $mail->addCC($email_sop,'Acuse SP');
                $mail->addCC($email_jaz,'Acuse JI');
                $mail->addCC($email_lab,'Acuse LB');
                $mail->addAttachment();
                break;  
            case '6':
                $mail->addCC($email_acu,'Acuse ER'); // acuse a Envio de resultado
                $mail->addAttachment("../../pdf_resenv/".$atach);
                break;          
            default:
                $mail->addAttachment();
                break;
        }
        /*
        if($tipo == 4 or $tipo == 5){
            $mail->addCC($email_sop,'Acuse SP');
            $mail->addCC($email_jaz,'Acuse JI');
            $mail->addCC($email_lab,'Acuse LB');
            $mail->addAttachment();
        }else{
            $mail->addCC($email_acu,'Acuse Enviado');
            $mail->addAttachment("../../pdf_resenv/".$atach);
        }
        */
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto; //'Asunto del correo';
        $mail->Body  = $contenido; //'Contenido del correo <b>en HTML!</b>';
       // $mail->addAttachment("../../pdf_resenv/".$atach);
        $mail->AltBody = 'Contenido del correo en texto plano para los clientes de correo que no soporten HTML';
        $mail->send();

        $estenv = 1; //'El mensaje se ha enviado';
        //echo $estenv;
        //echo 'se envio';
        //echo 1;

    } catch (Exception $e) {
        $estenv =0; //"El mensaje no se ha enviado. Mailer Error: {$mail->ErrorInfo}";
        //echo 'no se envio';
        //echo $estenv;
    }
    //echo 'no se';
    echo $estenv;
    //echo 1;

}
?>