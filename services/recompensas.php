<?php
// services/recompensas.php

class RecompensasService {
    
    public function handle($method, $input) {
        switch ($method) {
            case 'GET':
                $this->getRecompensas();
                break;
                
            case 'POST':
                $this->resgatarPontos($input);
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido']);
                break;
        }
    }
    
    private function getRecompensas() {
        $recompensasDisponiveis = [
            [
                'id' => 1,
                'nome' => 'Desconto Supermercado Verde',
                'descricao' => 'Desconto de R$ 10,00 em compras acima de R$ 50,00',
                'pontosNecessarios' => 1000,
                'valor' => 'R$ 10,00',
                'categoria' => 'alimentacao',
                'parceiro' => 'Supermercado Verde',
                'validadeDias' => 30,
                'disponivel' => true,
                'termos' => 'Válido apenas para compras presenciais'
            ],
            [
                'id' => 2,
                'nome' => 'Desconto Loja Eco Fashion',
                'descricao' => 'Desconto de R$ 25,00 em roupas sustentáveis',
                'pontosNecessarios' => 2500,
                'valor' => 'R$ 25,00',
                'categoria' => 'moda',
                'parceiro' => 'Eco Fashion Store',
                'validadeDias' => 45,
                'disponivel' => true,
                'termos' => 'Válido para toda linha eco'
            ],
            [
                'id' => 3,
                'nome' => 'Vale Transporte Público',
                'descricao' => 'Créditos para ônibus e metrô',
                'pontosNecessarios' => 500,
                'valor' => 'R$ 5,00',
                'categoria' => 'transporte',
                'parceiro' => 'TransVerde',
                'validadeDias' => 60,
                'disponivel' => true,
                'termos' => 'Válido em toda rede de transporte'
            ],
            [
                'id' => 4,
                'nome' => 'Desconto Cinema Sustentável',
                'descricao' => 'Ingresso com 50% de desconto',
                'pontosNecessarios' => 1500,
                'valor' => 'R$ 15,00',
                'categoria' => 'entretenimento',
                'parceiro' => 'CineVerde',
                'validadeDias' => 20,
                'disponivel' => true,
                'termos' => 'Válido de segunda a quinta-feira'
            ],
            [
                'id' => 5,
                'nome' => 'Kit Plantio Urbano',
                'descricao' => 'Kit com sementes e vaso biodegradável',
                'pontosNecessarios' => 800,
                'valor' => 'R$ 8,00',
                'categoria' => 'jardinagem',
                'parceiro' => 'Jardim Verde',
                'validadeDias' => 90,
                'disponivel' => true,
                'termos' => 'Retirada em loja parceira'
            ],
            [
                'id' => 6,
                'nome' => 'Curso Online Sustentabilidade',
                'descricao' => 'Acesso gratuito ao curso completo',
                'pontosNecessarios' => 3000,
                'valor' => 'R$ 30,00',
                'categoria' => 'educacao',
                'parceiro' => 'EcoEducação',
                'validadeDias' => 120,
                'disponivel' => true,
                'termos' => 'Certificado incluso'
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Recompensas disponíveis obtidas com sucesso',
            'data' => $recompensasDisponiveis,
            'total' => count($recompensasDisponiveis),
            'categorias' => [
                'alimentacao' => 'Descontos em supermercados e restaurantes',
                'moda' => 'Roupas e acessórios sustentáveis',
                'transporte' => 'Créditos para transporte público',
                'entretenimento' => 'Cinema, teatro e eventos',
                'jardinagem' => 'Produtos para cultivo urbano',
                'educacao' => 'Cursos e workshops sobre sustentabilidade'
            ],
            'info' => [
                'conversao' => '100 pontos = R$ 1,00',
                'pontuacaoMinima' => 500,
                'validadeMedia' => '30-90 dias',
                'novosParceirosMensais' => 2
            ],
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function resgatarPontos($input) {
        if (!$input || !isset($input['Recompensa'])) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Dados da recompensa são obrigatórios',
                'required_fields' => ['Recompensa' => ['Total-Pontos', 'Resgatar']]
            ]);
            return;
        }
        
        $recompensa = $input['Recompensa'];
        $totalPontos = intval($recompensa['Total-Pontos']);
        $pontosResgatar = intval($recompensa['Resgatar']);
        
        // Validações
        if ($pontosResgatar <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Pontos para resgatar deve ser maior que zero']);
            return;
        }
        
        if ($pontosResgatar > $totalPontos) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Pontos insuficientes para resgate',
                'pontosDisponveis' => $totalPontos,
                'pontosSolicitados' => $pontosResgatar
            ]);
            return;
        }
        
        // Determinar que tipo de recompensa com base nos pontos
        $recompensaInfo = $this->determinarRecompensa($pontosResgatar);
        
        // Calcular valor do resgate (100 pontos = R$ 1,00)
        $valorResgate = $pontosResgatar / 100;
        $pontosRestantes = $totalPontos - $pontosResgatar;
        
        // Gerar código de desconto único
        $codigoDesconto = $this->gerarCodigoDesconto($pontosResgatar);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pontos resgatados com sucesso!',
            'data' => [
                'Recompensa' => [
                    'Total-Pontos' => (string)$pontosRestantes,
                    'Resgatar-pontos' => (string)$pontosResgatar
                ],
                'transacao' => [
                    'id' => 'RSG' . rand(10000, 99999),
                    'tipo' => 'resgate',
                    'valor' => 'R$ ' . number_format($valorResgate, 2, ',', '.'),
                    'pontosUtilizados' => $pontosResgatar,
                    'recompensa' => $recompensaInfo['nome'],
                    'parceiro' => $recompensaInfo['parceiro'],
                    'categoria' => $recompensaInfo['categoria'],
                    'codigoDesconto' => $codigoDesconto,
                    'validoAte' => date('Y-m-d', strtotime('+' . $recompensaInfo['validadeDias'] . ' days')),
                    'instrucoes' => $recompensaInfo['instrucoes']
                ],
                'detalhesUso' => [
                    'comoUsar' => 'Apresente o código no estabelecimento parceiro',
                    'restricoes' => $recompensaInfo['termos'],
                    'suporte' => 'WhatsApp: (11) 99999-8888'
                ],
                'economiaAmbiental' => [
                    'equivalenteKgReciclados' => number_format($pontosResgatar / 10, 1),
                    'co2Evitado' => number_format($pontosResgatar * 0.05, 1) . 'kg'
                ],
                'timestamp' => date('c'),
                'status' => 'success'
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function determinarRecompensa($pontos) {
        $recompensas = [
            500 => [
                'nome' => 'Vale Transporte Público',
                'parceiro' => 'TransVerde',
                'categoria' => 'transporte',
                'validadeDias' => 60,
                'termos' => 'Válido em toda rede de transporte',
                'instrucoes' => 'Use o código no app TransVerde'
            ],
            800 => [
                'nome' => 'Kit Plantio Urbano',
                'parceiro' => 'Jardim Verde',
                'categoria' => 'jardinagem',
                'validadeDias' => 90,
                'termos' => 'Retirada em loja parceira',
                'instrucoes' => 'Apresente o código na loja mais próxima'
            ],
            1000 => [
                'nome' => 'Desconto Supermercado Verde',
                'parceiro' => 'Supermercado Verde',
                'categoria' => 'alimentacao',
                'validadeDias' => 30,
                'termos' => 'Válido apenas para compras presenciais',
                'instrucoes' => 'Apresente o código no caixa'
            ],
            1500 => [
                'nome' => 'Desconto Cinema Sustentável',
                'parceiro' => 'CineVerde',
                'categoria' => 'entretenimento',
                'validadeDias' => 20,
                'termos' => 'Válido de segunda a quinta-feira',
                'instrucoes' => 'Use o código na compra online ou bilheteria'
            ],
            2500 => [
                'nome' => 'Desconto Loja Eco Fashion',
                'parceiro' => 'Eco Fashion Store',
                'categoria' => 'moda',
                'validadeDias' => 45,
                'termos' => 'Válido para toda linha eco',
                'instrucoes' => 'Apresente o código na loja ou site'
            ],
            3000 => [
                'nome' => 'Curso Online Sustentabilidade',
                'parceiro' => 'EcoEducação',
                'categoria' => 'educacao',
                'validadeDias' => 120,
                'termos' => 'Certificado incluso',
                'instrucoes' => 'Acesse o site e use o código para liberação'
            ]
        ];
        
        // Encontrar a recompensa mais próxima (menor ou igual aos pontos disponíveis)
        $pontosDisponiveis = array_keys($recompensas);
        rsort($pontosDisponiveis); // Ordenar do maior para o menor
        
        foreach ($pontosDisponiveis as $pontosNecessarios) {
            if ($pontos >= $pontosNecessarios) {
                return $recompensas[$pontosNecessarios];
            }
        }
        
        // Fallback para recompensa básica
        return [
            'nome' => 'Desconto Parceiro Verdiva',
            'parceiro' => 'Parceiro Verde',
            'categoria' => 'geral',
            'validadeDias' => 30,
            'termos' => 'Válido conforme disponibilidade',
            'instrucoes' => 'Consulte os termos no app'
        ];
    }
    
    private function gerarCodigoDesconto($pontos) {
        $prefixes = ['VERDE', 'ECO', 'SUST', 'RECIC', 'VIDA'];
        $prefix = $prefixes[array_rand($prefixes)];
        $numero = str_pad($pontos, 4, '0', STR_PAD_LEFT);
        $sufixo = strtoupper(substr(md5(time() . $pontos), 0, 3));
        
        return $prefix . $numero . $sufixo;
    }
}
?>