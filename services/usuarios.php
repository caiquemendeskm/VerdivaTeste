<?php
// services/usuarios.php

class UsuariosService {
    private $pdo;

    public function __construct() {
        $host = 'localhost';
        $db   = 'verdivabd';
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->json(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()], 500);
        }
    }

    public function handle($method, $input) {
        switch ($method) {
            case 'POST':  $this->cadastrar($input); break;
            case 'GET':   $this->listar(); break;
            case 'PUT':   $this->atualizar($input); break;
            case 'DELETE':$this->remover($input); break;
            default:      $this->json(['success' => false, 'message' => 'Método inválido'], 405);
        }
    }

    private function cadastrar($data) {
        if (!$data || !isset($data['cpf'], $data['email'], $data['senha'], $data['telefone'])) {
            return $this->json(['success' => false, 'message' => 'Dados obrigatórios faltando.'], 400);
        }

        $cpf = preg_replace('/\D/', '', $data['cpf']);
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $senha = password_hash($data['senha'], PASSWORD_DEFAULT);
        $telefone = preg_replace('/\D/', '', $data['telefone']);

        if (!$email) return $this->json(['success' => false, 'message' => 'E-mail inválido.'], 400);
        if (strlen($cpf) !== 11) return $this->json(['success' => false, 'message' => 'CPF deve ter 11 dígitos.'], 400);

        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE cpf = ? OR email = ?");
        $stmt->execute([$cpf, $email]);
        if ($stmt->rowCount() > 0) {
            return $this->json(['success' => false, 'message' => 'CPF ou e-mail já cadastrado.'], 409);
        }

        $sql = "INSERT INTO usuarios (cpf, email, senha, telefone, data_cadastro) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute([$cpf, $email, $senha, $telefone])) {
            $this->json(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
        } else {
            $this->json(['success' => false, 'message' => 'Erro ao salvar no banco.'], 500);
        }
    }

    // LISTAR: Suporta busca por e-mail (para login)
    private function listar() {
    $email = $_GET['email'] ?? null;

    if ($email) {
        // PARA LOGIN E REDEFINIR SENHA: RETORNA A SENHA
        $stmt = $this->pdo->prepare("SELECT id, cpf, email, senha, telefone FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
    } else {
        // LISTAGEM NORMAL: NÃO RETORNA SENHA
        $stmt = $this->pdo->query("SELECT id, cpf, email, telefone, data_cadastro FROM usuarios ORDER BY data_cadastro DESC");
    }

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $this->json(['success' => true, 'usuarios' => $usuarios]);
}

    private function atualizar($data) {
        $this->json(['success' => false, 'message' => 'Atualização não implementada.'], 501);
    }

    private function remover($data) {
        $this->json(['success' => false, 'message' => 'Remoção não implementada.'], 501);
    }

    private function json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}