<?php

session_start();


//dados dos horários do monitor
$monitor = $_SESSION['monitor'];
$segunda = '';
if (isset($_POST['seg'])) {
    $segunda = $_POST['seg'];
}
$seg_ini = $_POST['seg_ini'];
$seg_fim = $_POST['seg_fim'];
$terca = '';
if (isset($_POST['ter'])) {
    $terca = $_POST['ter'];
}
$ter_ini = $_POST['ter_ini'];
$ter_fim = $_POST['ter_fim'];
$quarta = '';
if (isset($_POST['qua'])) {
    $quarta = $_POST['qua'];
}
$qua_ini = $_POST['qua_ini'];
$qua_fim = $_POST['qua_fim'];
$quinta = '';
if (isset($_POST['qui'])) {
    $quinta = $_POST['qui'];
}
$qui_ini = $_POST['qui_ini'];
$qui_fim = $_POST['qui_fim'];
$sexta = '';
if (isset($_POST['sex'])) {
    $sexta = $_POST['sex'];
}
$sex_ini = $_POST['sex_ini'];
$sex_fim = $_POST['sex_fim'];
$sabado = '';
if (isset($_POST['sab'])) {
    $sabado = $_POST['sab'];
}
$sab_ini = $_POST['sab_ini'];
$sab_fim = $_POST['sab_fim'];
$domingo = '';
if (isset($_POST['dom'])) {
    $domingo = $_POST['dom'];
}
$dom_ini = $_POST['dom_ini'];
$dom_fim = $_POST['dom_fim'];
$grade = $_POST['grade'];
$agend_min = $_POST['agend_min'];
$agend_padrao = $_POST['agend_padrao'];
$agend_max = $_POST['agend_max'];
$pausa = $_POST['pausa'];
$retorno = $_POST['retorno'];
$status = $_POST['status'];

//dados de configurações avançadas
include_once('./conexao.php');
$ret = $pdo->query("update tb_horarios_monitor set segunda = '$segunda', seg_ini = '$seg_ini', seg_fim = '$seg_fim', terca = '$terca', ter_ini = '$ter_ini', "
        . "ter_fim = '$ter_fim', quarta = '$quarta', qua_ini = '$qua_ini', qua_fim = '$qua_fim', quinta = '$quinta', qui_ini = '$qui_ini', qui_fim = '$qui_fim', "
        . "sexta = '$sexta', sex_ini = '$sex_ini', sex_fim = '$sex_fim', sabado = '$sabado', sab_ini = '$sab_ini', sab_fim = '$sab_fim', domingo = '$domingo', "
        . "dom_ini = '$dom_ini', dom_fim = '$dom_fim', exibicao_grade = '$grade', agend_minimo = '$agend_min', agend_padrao = '$agend_padrao', agend_maximo = '$agend_max', "
        . "pausa = '$pausa', retorno = '$retorno', status = '$status' where monitor_id = '$monitor'");
if ($ret) {
    echo "ok";
}    
    

    

