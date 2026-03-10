<?php

// Funções de armazenamento

function salvar_no_arquivo($reator) {
    $conteudo = serialize($reator); 
    file_put_contents('banco_de_dados.txt', $conteudo);
}

function carregar_do_arquivo() {
    if (file_exists('banco_de_dados.txt')) {
        $conteudo = file_get_contents('banco_de_dados.txt');
        return unserialize($conteudo) ?: [];
    }
    return [];
}

// Funçoes do Sistema

// 1° Registrar novo disparo
function registrar_disparo(&$reator, $codigo, $temperatura, $tempo, $ignicao) {
    $novo_ID = count($reator) > 0 ? max(array_column($reator, 'id')) + 1 : 1;
    
    $reator[] = [
        "id" => $novo_ID, 
        "codigo" => $codigo,
        "temperatura" => (int)$temperatura,
        "tempo" => (int)$tempo,
        "ignicao" => (bool)$ignicao
    ];
}

// 2° Listar disparos (Ordenação Alfabética)
function listar_disparos($reator) {
    usort($reator, fn($a, $b) => strcasecmp($a['codigo'], $b['codigo']));
    return $reator;
}

// 3° Busca parcial por código
function buscar_disparo($reator, $termo) {
    return array_filter($reator, function($d) use ($termo) {
        return stripos($d["codigo"], $termo) !== false;
    });
}

// 4° Remover um registro
function remover_disparo(&$reator, $id) {
    foreach ($reator as $key => $d) {
        if ($d['id'] == $id) {
            unset($reator[$key]);
            $reator = array_values($reator); 
            return true;
        }
    }
    return false;
}

// 5° Relatório Científico (Critério de Lawson)
function gerar_estatisticas($reator) {
    $total = count($reator);
    if ($total === 0) return null;

    // alvo simplificado para proximidade de ignição
    $ALVO_LAWSON = 1000000; 

    $soma_temp = array_sum(array_column($reator, "temperatura"));
    $soma_tempo = array_sum(array_column($reator, "tempo"));
    $ignicoes = count(array_filter($reator, fn($d) => $d["ignicao"]));
    
    $soma_produtos = 0;
    foreach ($reator as $d) {
        $soma_produtos += ($d["temperatura"] * $d["tempo"]);
    }

    $produto_medio = $soma_produtos / $total;
    $proximidade_lawson = ($produto_medio / $ALVO_LAWSON) * 100;

    return [
        "total" => $total,
        "temp_media" => round($soma_temp / $total, 2),
        "tempo_medio" => round($soma_tempo / $total, 2),
        "taxa_sucesso" => round(($ignicoes / $total) * 100, 2),
        "produto_triplo" => round($produto_medio, 2),
        "status_lawson" => round($proximidade_lawson, 4)
    ];
}