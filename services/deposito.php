<?php

class DepositoService {
    
    public function handle($method, $input) {
        switch ($method) {
            case 'GET':
                $this->getHistoricoDepositos();
                break;
                
            case 'POST':
                $this->registrarDeposito($input);
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido']);
                break;
        }
    }
    
    private function getHistoricoDepositos() {
        $historico = [
            [
                'id' => 1,
                'usuarioId' => 1,
                'maquinaId' => 'VRD001',
                'Registro' => [
                    'Material' => 'Papel',
                    'Quantidade' => '1kg',
                    'Pontos' => '10',
                    'Total-Pontos' => '100'
                ],
                'detalhes' => [
                    'peso' => 1000,
                    'unidades' => 1,
                    'categoria' => 'papel',
                    'pontosGanhos' => 10
                ],
                'timestamp' => '2024-11-15T10:30:00Z',
                'transacaoId' => 'TXN789123',
                'status' => 'processed',
                'localizacao' => 'Shopping Verde - Piso 2'
            ],
            [
                'id' => 2,
                'usuarioId' => 1,
                'maquinaId' => 'VRD001',
                'Registro' => [
                    'Material' => 'Vidro',
                    'Quantidade' => '5 unidades',
                    'Pontos' => '10',
                    'Total-Pontos' => '110'
                ],
                'detalhes' => [
                    'peso' => 430,
                    'unidades' => 5,
                    'categoria' => 'vidro',
                    'pontosGanhos' => 10
                ],
                'timestamp' => '2024-11-15T11:45:00Z',
                'transacaoId' => 'TXN789124',
                'status' => 'processed',
                'localizacao' => 'Shopping Verde - Piso 2'
            ],
            [
                'id' => 3,
                'usuarioId' => 2,
                'maquinaId' => 'VRD002',
                'Registro' => [
                    'Material' => 'Plástico',
                    'Quantidade' => '2kg',
                    'Pontos' => '10',
                    'Total-Pontos' => '50'
                ],
                'detalhes' => [
                    'peso' => 2000,
                    'unidades' => 15,
                    'categoria' => 'plastico',
                    'pontosGanhos' => 10
                ],
                'timestamp' => '2024-11-15T14:20:00Z',
                'transacaoId' => 'TXN789125',
                'status' => 'processed',
                'localizacao' => 'Supermercado Eco - Entrada'
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Histórico de depósitos obtido com sucesso',
            'data' => $historico,
            'total' => count($historico),
            'estatisticas' => [
                'totalPontos' => 30,
                'totalMateriais' => 3,
                'pesoTotal' => '3.43kg',
                'economiaAmbiente' => 'CO2 evitado: 2.1kg'
            ],
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function registrarDeposito($input) {
        if (!$input || !isset($input['Registro'])) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Dados do registro são obrigatórios',
                'required_fields' => ['Registro' => ['Material', 'Quantidade', 'Pontos', 'Total-Pontos']]
            ]);
            return;
        }
        
        $registro = $input['Registro'];
        
        // Validação
        if (empty($registro['Material']) || empty($registro['Quantidade'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Material e Quantidade são obrigatórios']);
            return;
        }
        
        // Calcular pontos baseado no material
        $pontosPorMaterial = [
            'papel' => 10,
            'vidro' => 3,
            'plastico' => 5,
            'metal' => 15,
            'eletronico' => 25
        ];
        
        $materialLower = strtolower($registro['Material']);
        $pontosPorKg = isset($pontosPorMaterial[$materialLower]) ? $pontosPorMaterial[$materialLower] : 1;
        
        // Extrair peso da quantidade (simples parse)
        preg_match('/(\d+(?:\.\d+)?)/', $registro['Quantidade'], $matches);
        $peso = isset($matches[1]) ? floatval($matches[1]) : 1;
        
        $pontosCalculados = (int)($peso * $pontosPorKg);
        $totalPontosAntigo = isset($registro['Total-Pontos']) ? intval($registro['Total-Pontos']) : 0;
        $novoTotalPontos = $totalPontosAntigo + $pontosCalculados;
        
        // Simular ID da máquina
        $maquinasDisponiveis = ['VRD001', 'VRD002', 'VRD003', 'VRD004'];
        $maquinaId = $maquinasDisponiveis[array_rand($maquinasDisponiveis)];
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Depósito registrado com sucesso',
            'data' => [
                'id' => rand(1000, 9999),
                'usuarioId' => rand(1, 100),
                'maquinaId' => $maquinaId,
                'Registro' => [
                    'Material' => $registro['Material'],
                    'Quantidade' => $registro['Quantidade'],
                    'Pontos' => (string)$pontosCalculados,
                    'Total-Pontos' => (string)$novoTotalPontos
                ],
                'detalhes' => [
                    'peso' => $peso * 1000, // em gramas
                    'categoria' => $materialLower,
                    'pontosGanhos' => $pontosCalculados,
                    'valorEquivalente' => 'R$ ' . number_format($pontosCalculados * 0.01, 2, ',', '.')
                ],
                'timestamp' => date('c'),
                'transacaoId' => 'TXN' . rand(100000, 999999),
                'status' => 'processed',
                'localizacao' => $this->getLocalizacaoMaquina($maquinaId),
                'impactoAmbiental' => [
                    'co2Evitado' => number_format($peso * 0.5, 1) . 'kg',
                    'aguaEconomizada' => number_format($peso * 2.3, 1) . 'L'
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function getLocalizacaoMaquina($maquinaId) {
        $localizacoes = [
            'VRD001' => 'Shopping Verde - Piso 2',
            'VRD002' => 'Supermercado Eco - Entrada',
            'VRD003' => 'Estação Metro Verdiva - Hall Principal',
            'VRD004' => 'Universidade Sustentável - Biblioteca'
        ];
        
        return isset($localizacoes[$maquinaId]) ? $localizacoes[$maquinaId] : 'Localização não identificada';
    }
}
?>