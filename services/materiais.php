<?php

class MateriaisService {
    
    public function handle($method, $input) {
        switch ($method) {
            case 'GET':
                $this->getMateriais();
                break;
                
            default:
                http_response_code(405);
                echo json_encode([
                    'error' => 'Método não permitido. Use GET para consultar materiais.'
                ]);
                break;
        }
    }
    
    private function getMateriais() {
        $materiaisDisponiveis = [
            [
                'id' => 1,
                'Material' => [
                    'Tipo' => 'papel',
                    'Categoria' => 'Papel e Papelão'
                ],
                'pontosConfig' => [
                    'porKg' => 10,
                    'porUnidade' => null,
                    'minimo' => '100g'
                ],
                'exemplos' => [
                    'Material' => [
                        'Peso' => '1kg',
                        'Quantidade' => '1',
                        'Tipo' => 'papel'
                    ],
                    'pontos' => 10
                ],
                'status' => 'accepted',
                'instrucoes' => 'Remova grampos e fitas adesivas'
            ],
            [
                'id' => 2,
                'Material' => [
                    'Tipo' => 'vidro',
                    'Categoria' => 'Vidros e Cristais'
                ],
                'pontosConfig' => [
                    'porKg' => 3,
                    'porUnidade' => 2,
                    'minimo' => '50g'
                ],
                'exemplos' => [
                    'Material' => [
                        'Peso' => '430g',
                        'Quantidade' => '5',
                        'Tipo' => 'vidro'
                    ],
                    'pontos' => 10
                ],
                'status' => 'accepted',
                'instrucoes' => 'Remova tampas e rótulos'
            ],
            [
                'id' => 3,
                'Material' => [
                    'Tipo' => 'plastico',
                    'Categoria' => 'Plásticos Recicláveis'
                ],
                'pontosConfig' => [
                    'porKg' => 5,
                    'porUnidade' => 1,
                    'minimo' => '10g'
                ],
                'exemplos' => [
                    'Material' => [
                        'Peso' => '2kg',
                        'Quantidade' => '10',
                        'Tipo' => 'plastico'
                    ],
                    'pontos' => 10
                ],
                'status' => 'accepted',
                'instrucoes' => 'Lave antes de depositar'
            ],
            [
                'id' => 4,
                'Material' => [
                    'Tipo' => 'metal',
                    'Categoria' => 'Metais'
                ],
                'pontosConfig' => [
                    'porKg' => 15,
                    'porUnidade' => 3,
                    'minimo' => '50g'
                ],
                'exemplos' => [
                    'Material' => [
                        'Peso' => '500g',
                        'Quantidade' => '20',
                        'Tipo' => 'metal'
                    ],
                    'pontos' => 7
                ],
                'status' => 'accepted',
                'instrucoes' => 'Latas de alumínio e ferro'
            ],
            [
                'id' => 5,
                'Material' => [
                    'Tipo' => 'eletronico',
                    'Categoria' => 'Eletrônicos'
                ],
                'pontosConfig' => [
                    'porKg' => 25,
                    'porUnidade' => 50,
                    'minimo' => '1 unidade'
                ],
                'exemplos' => [
                    'Material' => [
                        'Peso' => '200g',
                        'Quantidade' => '1',
                        'Tipo' => 'eletronico'
                    ],
                    'pontos' => 50
                ],
                'status' => 'accepted',
                'instrucoes' => 'Celulares, baterias, cabos'
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Materiais disponíveis para depósito',
            'data' => $materiaisDisponiveis,
            'total' => count($materiaisDisponiveis),
            'info' => [
                'sistema_pontos' => 'Pontos calculados por peso ou unidade',
                'conversao' => '100 pontos = R$ 1,00',
                'horario_funcionamento' => '24 horas por dia'
            ],
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
?>