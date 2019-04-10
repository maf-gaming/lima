<?php
if(!session_id()) {
	session_start();
}
error_reporting(0);

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == "contact_form_request") {

        $ourMail = "xshisu92@gmail.com"; //Insert your email address here

        $required_fields = array("nombre", "telefono","email", "asunto", "empresa", "mensaje");
        $pre_messagebody_info = "";
        $subject = "";
        $errors = array();
        $data = array();
        parse_str($_REQUEST['values'], $data);
		
        //check for required and assemble message

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $name = strtolower(trim($key));
                if (in_array($name, $required_fields)) {
                    if (empty($value)) {
                        $errors[$name] = "Por favor ingresa tu " . $name . "!";
                    }
                }

                if ($name == "email") {
                    if (!check_email_address($value)) {
                        $errors[$name] = "ingresaste un email incorrecto";
                    }
                }
            }
        }

//***	
     
        $verify = $_SESSION['verify'];
        if ($verify != $data['verify']) {
            $errors["verify"] = "codigo captcha invalido";
        }

//***
        $result = array(
            "is_errors" => 0,
            "info" => ""
        );
        if (!empty($errors)) {
            $result['is_errors'] = 1;
            $result['info'] = $errors;
            echo json_encode($result);
            exit;
        }
		
		$pre_messagebody_info.="<strong>Nombre</strong>" . ": " . $data['nombre'] . "<br />";
        $pre_messagebody_info.="<strong>E-mail</strong>" . ": " . $data['email'] . "<br />";
		
		if (!empty($data["empresa"])){
			$pre_messagebody_info.="<strong>Empresa</strong>" . ": " . $data['empresa'] . "<br />";
		}
		if (!empty($data["telefono"])){
			$pre_messagebody_info.="<strong>telefono</strong>" . ": " . $data['telefono'] . "<br />";
		}
		
		if (empty($data["asunto"])){
			$subject.="asunto";
		}

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers.= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers.= "DE: ".$data['email']."\r\n";

        $after_message = "\r\n<br />--------------------------------------------------------------------------------------------------\r\n<br /> This mail was sent via contact form";

        if (mail($ourMail, $subject, $pre_messagebody_info .= "<strong>Mensaje</strong>" . ": " . $data['mensaje'] .$after_message, $headers)) {
            $result["info"] = "Enviado";
        } else {
            $result["info"] = "Fallo al Enviar :C";
        }

        echo json_encode($result);
        exit;
    }
}

function check_email_address($email) {
// First, we check that there's one @ symbol,
// and that the lengths are right.
    if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
// Email invalid because wrong number of characters
// in one section or wrong number of @ symbols.
        return false;
    }
// Split it into sections to make life easier
    $email_array = explode("@", $email);
    $local_array = explode(".", $email_array[0]);
    for ($i = 0; $i < sizeof($local_array); $i++) {
        if
        (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
в†Є'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
            return false;
        }
    }
// Check if domain is IP. If not,
// it should be valid domain name
    if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
        $domain_array = explode(".", $email_array[1]);
        if (sizeof($domain_array) < 2) {
            return false; // Not enough parts to domain
        }
        for ($i = 0; $i < sizeof($domain_array); $i++) {
            if
            (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
в†Є([A-Za-z0-9]+))$", $domain_array[$i])) {
                return false;
            }
        }
    }
    return true;
}
?>
