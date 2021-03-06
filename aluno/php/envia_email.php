<?php

//include_once('../php/conexao.php');
//retorna os dados referentes ao monitor e ao aluno
include_once './pega_ultimo_agendamento.php';
$email = $_POST["email"];//pega email do aluno
$monitor_id = $_SESSION['monitor_selecionado'];

$divOpen = "<div style='background-color: silver;width:500px;margin:0 auto;'>";
$header = "<div style='font-size:20px;text-align:center;'>SCA 1.0</div>";
$divClose = "</div>";

//Mensagem que o sistema irá enviar para o aluno da disciplina
$mensagem = $divOpen.$header."<h3>Olá, você confirmou a sua presença no encontro de monitoria. <br>Categoria: " . utf8_encode($_SESSION['nome_categoria']) . ""
        . "<br>Curso: " . utf8_encode($_SESSION['disciplina']) . "<br>Monitor: " . $_SESSION['monitor_selecionado2'] . "<br>Agendado para: " . $_SESSION['dia_marcado'] . " de "
        . "" . $_SESSION['mes_marcado'] . " de " . $_SESSION['ano_marcado'] . " " . $_SESSION['hora_marcada'] . "<br><br><br>"
        . "<a href='http://neadteste.fcrs.edu.br/uniagendamentos/aluno/php/pega_dados_via_email.php?id=$lastid&email=$email'>Clique aqui para cancelar o agendamento</a><br>"
        . "<a href='http://neadteste.fcrs.edu.br/uniagendamentos/aluno/php/pega_dados_via_email.php?id=$lastid&email=$email&edt=$monitor_id'>Clique aqui para editar o agendamento</a></h3>".$divClose;
$mensagem_full = utf8_decode($mensagem);

//Mensagem que o sistema irá enviar para o tutor da disciplina
$mensagem_monitor = "<h2>".utf8_encode('Confirmação de agendamento')."</h2>"
        . "<h3>Aluno: " . $nome_aluno . "<br>"
        . "Curso: ".utf8_encode($_SESSION['disciplina'])."<br>"
        . "Agendado para: " . $_SESSION['dia_marcado'] . " de "
        . "" . $_SESSION['mes_marcado'] . " de " . $_SESSION['ano_marcado'] . " " . $_SESSION['hora_marcada']."</h3>";

require_once("../../phpmailer/class.phpmailer.php");

define('GUSER', 'arcs.si3@gmail.com'); //email responsável por enviar os emails para os alunos e tutores
define('GPWD', '22041996#$'); // senha do email

//método que envia os emails
function smtpmailer($para, $de, $de_nome, $assunto, $corpo) {
    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();  // Ativar SMTP
    $mail->SMTPDebug = 0;  // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
    $mail->SMTPAuth = true;  // Autenticação ativada
    $mail->SMTPSecure = 'ssl'; // SSL REQUERIDO pelo GMail
    $mail->Host = 'smtp.gmail.com'; // SMTP utilizado
    $mail->Port = 465;    // A porta 465 deverá estar aberta em seu servidor
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    $mail->SetFrom($de, $de_nome);
    $mail->Subject = $assunto;
    $mail->Body = $corpo;
    $mail->AddAddress($para);
    $mail->ContentType = "text/html";
    if (!$mail->Send()) {
        $error = 'Mail error: ' . $mail->ErrorInfo;
        return false;
    } else {
        $error = 'Mensagem enviada!';
        return true;
    }
}

//envia email para o aluno
if (smtpmailer($email, 'arcs.si3@gmail.com', 'Sistema de Agendamento', 'Agendamento', $mensagem_full)) {
    
}

//envia email para o monitor
if (smtpmailer('antonio.regi.silva@gmail.com', 'arcs.si3@gmail.com', 'Sistema de Agendamento', 'Novo inscrito', $mensagem_monitor)) {
    
}


