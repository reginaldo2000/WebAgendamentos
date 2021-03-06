<!DOCTYPE html>
<?php
session_name('aluno');
session_start();

include_once './php/mes_por_extenso.php';
include_once ('../php/conexao.php');
include_once './php/DataHoraAtual.php';

if (!isset($_SESSION['id_agendamento'])) {
    header("location:index.php");
}

//vetor com os dados dos dias
$semana = array(
    'en' => array('/Sun/', '/Mon/', '/Tue/', '/Wed/', '/Thu/', '/Fri/', '/Sat/', '/Jan/', '/Feb/', '/Mar/', '/Apr/', '/May/', '/Jun/', '/Jul/', '/Aug/', '/Sep/', '/Oct/', '/Nov/', '/Dec/'),
    'short' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez')
);
//busca dados do agendamento e armazena na variável $ag
$st1 = $pdo->prepare("select * from tb_agendamentos where id = :id");
$st1->bindValue(":id", $_SESSION['id_agendamento']);
$st1->execute();
$ag = $st1->fetch(PDO::FETCH_ASSOC);

if ($ag['cont_agend'] >= 2) {
    header('location:limite_agendamentos.php');
}

$data_agendada = date('Y-m-d H:i', strtotime($ag['data'] . " " . $ag['hora']));
$data_atualizada = date('Y-m-d H:i', strtotime($data_atual_servidor . " " . $hora_atual_servidor . " + 24 hours"));

//trata os dados da grade1
$dia = date('Y-m-d', strtotime($ag['data']));
$mes_num = intval(date("m", strtotime($dia)));
$quantidade_dias = intval(date("d", strtotime($dia))) + 8;
$i = 1;
$dia_inicial = intval(date("d", strtotime($dia . " + " . $i . " days")));

//trata dados da grade de horários
$st2 = $pdo->prepare("select * from tb_horarios_monitor hm, vw_usuarios us, vw_cursos c, vw_categorias cat where hm.monitor_id = :monitor and us.id = hm.monitor_id and c.id_course = hm.curso_id and c.category = cat.id_category");
$st2->bindValue(":monitor", $_SESSION['monitor_selecionado']);
$st2->execute();
$con = $st2->fetch(PDO::FETCH_ASSOC);

$_SESSION['monitor_selecionado2'] = $con['firstname'];
$_SESSION['disciplina'] = $con['fullname'];
$_SESSION['nome_categoria'] = $con['name'];
?>
<html>
    <?php include_once('./imports/import_head.php'); ?>
    <body>
        <?php
        include_once('./imports/import_header.php');
        include_once('./imports/import_menu.php');
        ?>
        <div class="container-fluid">
            <section class="main-content">
                <div class="title">Reagendar Horário de Tutoria</div>
                <div class="alert ocultar" role="alert">
                    <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="alert-msg"></span>
                </div>

                <center>
                    <?php
                    if ($data_atualizada >= $data_agendada) {
                        echo '<div class="alert alert-danger"> ';
                        echo '<strong>Período para reagendar o horário expirou!</strong>';
                        echo '</div><br>';
                        echo '<a href="consagendamentos.php"><input type="button" class="btn btn-danger" value="Voltar"></a>';
                    } else {
                        ?>
                        <div class="alert alert-warning" role="alert">
                            <strong>Agendamento Anterior: </strong> <?php echo date('d/m/Y H:i', strtotime($data_agendada)); ?>
                        </div>
                        <!--primeira grade de horários-->
                        <div class="grade1">
                            <?php
                            if (intval(date('d', strtotime($dia . ' + ' . $i . ' days'))) > intval(date('d', strtotime($dia . ' + 14 days')))) {
                                echo "<h3>" . $mes[intval($mes_num)] . " - " . $mes[intval(($mes_num + 1))] . "</h3><br>";
                            } else {
                                echo "<h3>" . $mes[intval($mes_num)] . "</h3><br>";
                            }
                            ?>
                            <div class="dias">
                                <?php
                                while ($dia_inicial <= $quantidade_dias) {
                                    ?>
                                    <div class="dia1" onclick="pintar(this, <?php echo date('d', strtotime($dia . ' + ' . $i . ' days')); ?>, <?php echo intval(date("d", strtotime($dia))); ?>);">
                                        <center>
                                            <?php
                                            echo preg_replace($semana['en'], $semana['short'], date('D', strtotime($dia . ' + ' . $i . ' days'))) . "<br>";
                                            echo date('d', strtotime($dia . ' + ' . $i . ' days'));
                                            ?>
                                        </center>
                                    </div>
                                    <?php
                                    $i++;
                                    $dia_inicial++;
                                }
                                ?>
                            </div>
                        </div>

                        <div class="horario1">
                            <?php include_once('./grade_horarios.php'); ?>
                        </div>
                    <?php } ?>
                </center>
            </section>
        </div>

        <?php include_once('./imports/import_footer.php'); ?>
        <!--<script src="functionsJS/agendamento.js"></script>-->
    </body>
</html>

<script type="text/javascript">
    $(document).ready(function () {
        $('.cad_horario').click(function () {
            var str = $(this).text();
            document.form_horarios.hora_marcada.value = str.replace(/\s/g, '');

            var dados = $("#form-horario").serialize();
            $.ajax({
                type: 'post',
                dataType: 'html',
                url: './php/armazena_dados.php',
                data: dados,
                success: function (dados) {
                    if (dados === "ok") {
                        location.href = "confirmar_reagendamento.php";
                    } else {
                        $('.alert').addClass('alert-danger').fadeIn(500);
                        $('.alert .alert-msg').html('Não há mais vagas disponíveis neste horário, por favor selecione um novo horário!');
                    }
                }
            });
        });
    });

    function pintar(dia, num, dia_atual) {

        $(".dia1").css("background-color", "#777");
        $(".dia1").removeClass("slt");
        $(dia).css("background-color", "#eb6c05");
        $(dia).addClass("slt");
        var nome_dia = $('.slt').text();

        if (nome_dia.indexOf("Seg") !== -1) {
            $(".ter, .qua, .qui, .sex, .sab, .dom").css("display", "none");
            $(".seg").css("display", "inherit");
        } else if (nome_dia.indexOf("Ter") !== -1) {
            $(".seg, .qua, .qui, .sex, .sab, .dom").css("display", "none");
            $(".ter").css("display", "inherit");
        } else if (nome_dia.indexOf("Qua") !== -1) {
            $(".seg, .ter, .qui, .sex, .sab, .dom").css("display", "none");
            $(".qua").css("display", "inherit");
        } else if (nome_dia.indexOf("Qui") !== -1) {
            $(".seg, .ter, .qua, .sex, .sab, .dom").css("display", "none");
            $(".qui").css("display", "inherit");
        } else if (nome_dia.indexOf("Sex") !== -1) {
            $(".seg, .ter, .qua, .qui, .sab, .dom").css("display", "none");
            $(".sex").css("display", "inherit");
        } else if (nome_dia.indexOf("Sab") !== -1) {
            $(".seg, .ter, .qua, .qui, .sex, .dom").css("display", "none");
            $(".sab").css("display", "inherit");
        } else if (nome_dia.indexOf("Dom") !== -1) {
            $(".seg, .ter, .qua, .qui, .sex, .sab").css("display", "none");
            $(".dom").css("display", "inherit");
        }
        var mes_marcado;

        if (num > dia_atual) {
            mes_marcado = "<?php echo $mes[intval(date('m', strtotime($dia)))]; ?>";
            document.form_horarios.mes_num.value = <?php echo date('m', strtotime($dia)); ?>;
        } else {
            mes_marcado = "<?php echo (intval(date('m', strtotime($dia))) == 12) ? 'Janeiro' : $mes[intval(date('m', strtotime($dia)) + 1)]; ?>";
            document.form_horarios.mes_num.value = <?php echo (intval(date('m', strtotime($dia))) == 12) ? 01 : intval(date('m', strtotime($dia))) + 1; ?>;
        }
        if (num > 9) {
            document.form_horarios.dia_marcado.value = num;
        } else {
            document.form_horarios.dia_marcado.value = "0" + num;
        }
        document.form_horarios.mes_marcado.value = mes_marcado;
    }
</script>
