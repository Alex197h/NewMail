<?php

require 'mail/PHPMailerAutoload.php';

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASE', 'filrouge');

try {
    $bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DATABASE, DB_USER, DB_PASS);
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}


if(isset($_POST['desabonner']) && isset($_POST['email'])){
    $user = $bdd->query("SELECT * FROM registers WHERE email='".$_POST['email']."' AND valide=1")->fetchObject();
    if($user){
        $update = $bdd->prepare("UPDATE registers SET valide=0 WHERE email='".$_POST['email']."'");
        $update->execute();
        echo 'Vous avez bien été désabonné !';
    }else echo 'Le compte n\'est pas enregistré !';
}

$mail = new PHPMailer;
$mail->SMTPDebug = 2;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = 'smtp.gmail.com';
$mail->Username = 'mail@gmail.com';
$mail->Port = 587;

$mail->Password = '';
$mail->SMTPSecure = 'tls';

$mail->setFrom('mail@gmail.com', 'Mailer');

$mail->isHTML(true);

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold !</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


$req = $bdd->query("SELECT * FROM registers WHERE valide=1");
$users = [];

while($user = $req->fetchObject()){
    $mail->addAddress($user->email);
    
    if($mail->send()) {
        echo 'Message envoyé à '.$user->email.'.';
    } else {
        echo $user->email.' not valide.';
        $update = $bdd->prepare("UPDATE registers SET valide=0");
        $update->execute();
    }
    $success = true;
    
    
    
    /*while(!$success && $user->erreur < 10){
        if($user->erreur < 10){
            if(false){$mail->addAddress($user->email);$success = true;}
            else{$user->erreur++;}
        }else{
            $update = $bdd->prepare("UPDATE registers SET valide=0, erreur=10");
            $update->execute();
        }
    }*/
    
    $mail->ClearAddresses();
    sleep(1);
    $users[] = $user;
}



?>

<form method="post" action="">
    <input type="text" placeholder="E-mail" name="email" required>
    <input type="submit" name="desabonner" value="Se désabonner">
</form>




