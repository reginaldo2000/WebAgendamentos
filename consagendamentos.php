<!DOCTYPE html>
<?php
//include_once './php/SessaoUsuario.php';
try {
    include_once('./php/conexao.php');
    $stmt = $pdo->prepare("select id_category, name from vw_categorias where parent = 0 order by name");
    $stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage();
}
$pag = 'con';
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
                <div class="title">Consultar Agendamentos</div>
                <form class="form-inline">
                    <div class="form-group">
                        <label>Categoria:</label>
                        <select id="sel-categoria" name="categoria" class="form-control">
                            <option value="">Selecione uma</option>
                            <?php
                            while ($con_cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $st = $pdo->prepare("select * from vw_categorias where parent = :pai order by name");
                                $st->bindValue(":pai", $con_cat['id_category']);
                                $st->execute();
                                echo '<optgroup label="' . utf8_encode($con_cat['name']) . '">';
                                while ($filha = $st->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $filha['id_category']; ?>"><?php echo utf8_encode($filha['name']); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Curso:</label>
                        <select id="sel-curso" name="curso" class="form-control">
                            <option value="">Selecione um</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tutor:</label>
                        <select id="sel-monitor" class="form-control" onchange="buscaAgendamentos(this.value);">
                            <option value="">Selecione uma opção</option>
                        </select>
                    </div>
                </form>
                <br>

                <div class="table-responsive" id="demo">
                    <table class="table table-bordered table-hover" >
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nome do Aluno</th>
                                <th>Agendado para</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody id="tabela-agendamentos">

                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <?php
        include_once('./imports/import_footer.php');
        ?>

        <script type="text/javascript">
            $(document).ready(function () {
                $("#sel-categoria").change(function () {
                    $("#sel-curso").empty();
                    $("#sel-monitor").empty();
                    $("#tabela-agendamentos").empty();
                    var categoria = $("#sel-categoria option:selected").val();
                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        url: './php/select_cursos.php',
                        data: {
                            cat: categoria
                        },
                        success: function (dados) {
                            $("#sel-curso").append('<option value="">Selecione um</option>' + dados);
                        }
                    });
                });

                $("#sel-curso").change(function () {
                    var id_curso = $("#sel-curso option:selected").val();
                    $("#sel-monitor").empty();
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: './php/seleciona_monitores_curso.php',
                        data: {
                            curso: id_curso
                        },
                        success: function (dados) {
                            $("#sel-monitor").append('<option value="">Selecione um</option>');
                            for (var i = 0; dados.length > i; i++) {
                                $('#sel-monitor').append('<option value="' + dados[i].monitor_id + '">' + dados[i].firstname + '</option>');
                            }
                        }
                    });
                });
            });

            function buscaAgendamentos(id_monitor) {
                $('#tabela-agendamentos').empty();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: './php/BuscaAgendamentos.php',
                    data: {
                        monitor_id: id_monitor
                    },
                    success: function (dados) {
                        if (dados === "not") {
                            $('#tabela-agendamentos').append('<tr><td colspan="4">Nenhum agendamento encontrado!</td></tr>');
                        } else {
                            for (var i = 0; i < dados.length; i++) {
                                $('#tabela-agendamentos').append('<tr><td>' + dados[i].username + '</td><td>' + dados[i].firstname + '</td>\n\
                                    <td>' + dados[i].horario + '</td>\n\
                                    <td class="text-center"><a onclick="abrirEdicao(' + dados[i].id + ',' + dados[i].monitor_id + ');" href="#"><i class="material-icons md-dark">settings</i></a>\n\
                                    <a href="#" onclick="abrirDialogo(' + dados[i].id + ');"><i class="material-icons md-dark-red">delete_forever</i></a></td>\n\
                                </tr>');
                            }
                            footer();
                        }
                    }
                });
            }
            function abrirEdicao(agend, moni) {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: './php/ArmazenaIDAgendamento.php',
                    data: {
                        agendamento: agend,
                        monitor: moni
                    },
                    success: function (dados2) {
                        if (dados2 === "ok") {
                            location.href = 'editar_agendamento.php';
                        }
                    }
                });
            }
            function abrirDialogo(id) {
                agendamento_id = id;
                $('#dialogo-confirmacao').css('display', 'block');
                $('.confirm').css('opacity', '1').css('margin', '20px auto').css('transition', '3s');
            }
            function fecharDialogo() {
                $('#dialogo-confirmacao').css('display', 'none');
            }
            function excluirAgendamento() {
                $.ajax({
                    type: 'post',
                    dataType: 'html',
                    url: './php/ExcluirAgendamento.php',
                    data: {
                        agendamento: agendamento_id
                    },
                    success: function (msg) {
                        if (msg === "ok") {
                            fecharDialogo();
                            buscaAgendamentos();
                            $('.mensagem span').html('Agendamento excluído com sucesso!');
                            $('.mensagem').css('display', 'block').css('transition', '1s').css('background-color', '#32BF32').removeClass('bg-red-60');
                        } else {
                            $('.mensagem span').html('Erro ao tentar excluir o agendamento!');
                            $('.mensagem').css('display', 'block').css('transition', '1s').addClass('bg-red-60');
                        }
                    }
                });
            }
            function fecharMsgErro() {
                $('.mensagem').css('display', 'none').css('transition', '1s');
            }
        </script>

    </body>
</html>

