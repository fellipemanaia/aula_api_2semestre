<?php
    header('Content-Type:application/json');
    include 'conexao.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];
    $path = parse_url($url, PHP_URL_PATH);
    $path = trim($path,'/');
    $path_parts = explode('/',$path);

    $primeiraparte = isset($path_parts[0]) ? $path_parts[0]: '';
    $segundaparte = isset($path_parts[1]) ? $path_parts[1]: '';
    $terceiraparte = isset($path_parts[2]) ? $path_parts[2]: '';
    $quartaparte = isset($path_parts[3]) ? $path_parts[3]: '';

    $resposta = [
        'metodo' => $metodo,
        'primeiraparte' => $primeiraparte,
        'segundaparte' => $segundaparte,
        'terceiraparte' => $terceiraparte,
        'quartaparte' => $quartaparte,
    ];

    switch($metodo){
        case 'GET':
            if($terceiraparte == 'alunos' && $quartaparte == ''){
                lista_alunos();
            }elseif($terceiraparte == 'alunos' &&  $quartaparte != ''){
                lista_um_aluno($quartaparte);
            }elseif ($terceiraparte == 'cursos' && $quartaparte == ''){
                lista_cursos();
            }elseif($terceiraparte == 'cursos' &&  $quartaparte != ''){
                lista_um_curso($quartaparte);
            };
            break;
        case 'POST':
            if($terceiraparte == 'alunos'){
                insere_aluno();
            }elseif($terceiraparte == 'cursos'){
                insere_curso();  
            };
            break;
        case 'PUT':
            if($terceiraparte == 'alunos'){
                atualiza_aluno();
            }elseif($terceiraparte == 'cursos'){
                atualiza_curso();  
            };
            break;
        case 'DELETE':
            if($terceiraparte == 'alunos'){
                remove_aluno();
            }elseif($terceiraparte == 'cursos'){
                remove_curso();  
            };
            break;
        default:
            echo json_encode([
                'mensagem' => 'Método não permitido'
            ]);
            break;
    };

    
    function lista_alunos(){
        global $conexao;

        $resultado = $conexao->query("SELECT * FROM alunos");
        $alunos = $resultado->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'mensagem'=> 'Lista de todos os alunos',
            'dados' => $alunos
        ]);
    }
    function lista_um_aluno($quartaparte){
        global $conexao;
        $stmt = $conexao->prepare("SELECT * FROM alunos WHERE id = ?");
        $stmt->bind_param('i',$quartaparte);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $um_aluno = $resultado->fetch_assoc();
        
        
        echo json_encode([
            'mensagem' => 'Lista de 1 aluno',
            'dados do aluno' => $um_aluno
        ]);
    }
    function lista_cursos(){
        global $conexao;

        $resultado = $conexao->query("SELECT * FROM cursos");
        $cursos = $resultado->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'mensagem'=> 'Lista de todos os cursos',
            'dados' => $cursos
        ]);
    }
    function lista_um_curso($quartaparte){
        global $conexao;

        $stmt = $conexao->prepare("SELECT * FROM cursos WHERE id_curso = ?");
        $stmt->bind_param('i',$quartaparte);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $um_curso = $resultado->fetch_assoc();
        
        echo json_encode([
            'mensagem' => 'Lista de 1 curso',
            'dados do curso' => $um_curso
        ]);
    }
    function insere_curso(){
        global $conexao;
        $input = json_decode(file_get_contents('php://input'),true);
        $nome_curso = $input['nome_curso'];

        $sql = "INSERT INTO cursos (nome_curso) VALUES ('$nome_curso')";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Curso cadastrado com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO NO CADASTRO DO CURSO'
            ]);
        };
    }
    function insere_aluno(){
        global $conexao;
        $input = json_decode(file_get_contents('php://input'),true);
        $id_curso = $input['fk_cursos_id_curso'];
        $nome = $input['nome'];
        $email = $input['email'];
        
        $sql = "INSERT INTO alunos (nome,email,fk_cursos_id_curso) VALUES ('$nome','$email','$id_curso')";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Aluno cadastrado com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO NO CADASTRO DO Aluno'
            ]);
        };
    }
    function atualiza_aluno(){
        global $conexao;

        $input = json_decode(file_get_contents('php://input'),true);
        $id = $input['id'];
        $nome_novo = $input['nome_novo'];
        $email_novo = $input['email_novo'];

        $sql = "UPDATE alunos SET nome = '$nome_novo',email = '$email_novo' WHERE id='$id'";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Aluno atualizado com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO ATUALIZAÇÃO DO Aluno'
            ]);
        };
    }
    function atualiza_curso(){
        global $conexao;

        $input = json_decode(file_get_contents('php://input'),true);
        $id_curso = $input['id_curso'];
        $nome_curso_novo = $input['nome_curso_novo'];

        $sql = "UPDATE cursos SET nome_curso = '$nome_curso_novo' WHERE id_curso='$id_curso'";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Curso atualizado com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO ATUALIZAÇÃO DO Curso'
            ]);
        };
    }
    function remove_aluno(){
        global $conexao;

        $input = json_decode(file_get_contents('php://input'),true);
        $id = $input['id'];

        $sql = "DELETE FROM alunos WHERE id='$id'";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Aluno removido com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO REMOÇÃO DO Aluno'
            ]);
        };
    }
    function remove_curso(){
        global $conexao;

        $input = json_decode(file_get_contents('php://input'),true);
        $id_curso = $input['id_curso'];

        $sql = "DELETE FROM cursos WHERE id_curso='$id_curso'";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem'=> 'Curso removido com sucesso'
            ]);
        }else{
            echo json_encode([
                'mensagem'=> 'ERRO REMOÇÃO DO Curso'
            ]);
        };
    }
?>