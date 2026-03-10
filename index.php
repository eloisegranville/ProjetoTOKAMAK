<?php
require_once 'biblioteca.php';

// 1. Carrega os dados salvos ao iniciar o programa
$reator = carregar_do_arquivo();

while (true) {
    echo "\n ☢️  PROJETO TOKAMAK - MONITOR DE REATOR DE FUSÃO NUCLEAR ☢️ \n";
    echo " 1. Registrar Disparo \n 2. Histórico de Disparos \n 3. Pesquisar Disparo \n 4. Ajustar Dados (Editar) \n 5. Deletar Registro \n 6. Relatório Científico \n 0. Desligar Sistema \n";
    
    $opcao = trim(readline("Comando: "));

    switch ($opcao) { 
        case 1:
            echo "\n ✒ --- NOVO REGISTRO --- ✒ \n";
            $cod = readline("Código do Disparo: ");
            $tmp = readline("Temperatura (M Kelvin): ");
            $tpo = readline("Tempo Confinamento (ms): ");
            $ignInput = strtolower(readline("Alcançou Ignição? (s/n): "));
            $ign = ($ignInput === 's' || $ignInput === 'sim');

            if (empty($cod) || !is_numeric($tmp) || !is_numeric($tpo)) {
                echo "❌ Erro: Parâmetros inválidos. \n";
            } else {
                registrar_disparo($reator, $cod, $tmp, $tpo, $ign); 
                salvar_no_arquivo($reator);
                echo "✅ Disparo registrado e salvo com sucesso. \n";
            }
            break;

        case 2:
            echo "\n 📁 --- HISTÓRICO --- 📁 \n";
            $lista = listar_disparos($reator);
            if (empty($lista)) {
                echo "📁 Nenhum disparo registrado. \n";
            } else {
                foreach ($lista as $d) {
                    $status = $d['ignicao'] ? "[IGNIÇÃO]" : "[FALHA]";
                    echo "ID: {$d["id"]} | {$d["codigo"]} | Temp: {$d["temperatura"]}MK | Tempo: {$d["tempo"]}ms | $status \n";
                }
            }
            break;

        case 3:
            echo "\n 🔎 --- PESQUISA --- 🔍 \n";
            $termo = readline("Digite o código para busca parcial: ");
            $resultados = buscar_disparo($reator, $termo); 
            if (empty($resultados)) {
                echo "🔍 Nenhum resultado encontrado para '$termo'. \n";
            } else {
                foreach ($resultados as $d) {
                    echo "ID: {$d["id"]} | Código: {$d["codigo"]} | Temp: {$d["temperatura"]}MK \n";
                }
            }
            break;

        case 4:
            echo "\n 📝--- AJUSTAR DADOS --- 🖋 \n";
            $id = (int)readline("ID para editar: ");
            $encontrado = false;
            foreach ($reator as &$d) {
                if ($d["id"] === $id) {
                    $d["codigo"] = readline("Novo Código [{$d["codigo"]}]: ") ?: $d["codigo"];
                    $d["temperatura"] = (int)(readline("Nova Temp [{$d["temperatura"]}]: ") ?: $d["temperatura"]);
                    $d["tempo"] = (int)(readline("Novo Tempo [{$d["tempo"]}]: ") ?: $d["tempo"]);
                    
                    salvar_no_arquivo($reator);
                    echo "🗂 Dados atualizados e salvos com sucesso. \n";
                    $encontrado = true;
                    break; 
                }
            }
            if (!$encontrado) echo "❌ ID não encontrado.\n";
            break;

        case 5:
            echo "\n 🗑️ --- REMOVER REGISTRO --- 🗑️ \n";
            $id = (int)readline("Digite o ID do disparo que deseja eliminar: ");
            $confirma = strtolower(readline("Tem a certeza que deseja apagar o ID $id? (s/n): "));
            
            if ($confirma === 's' || $confirma === 'sim') {
                if (remover_disparo($reator, $id)) {
                    salvar_no_arquivo($reator);
                    echo "🗑️ Registro ID $id removido. \n";
                } else {
                    echo "❌ ID $id não encontrado. \n";
                }
            } else {
                echo "↩️ Remoção cancelada. \n";
            }
            break; 

        case 6:
            $rel = gerar_estatisticas($reator);
            if ($rel) {
                echo "\n 📊 --- RELATÓRIO CIENTÍFICO (SISM) --- 📊 \n";
                echo " Total de Disparos: {$rel["total"]} \n";
                echo " Temperatura Média: {$rel["temp_media"]} MK \n";
                echo " Tempo de Confinamento Médio: {$rel["tempo_medio"]} ms \n";
                echo " Taxa de Ignição Alcançada: {$rel["taxa_sucesso"]}% \n";
                echo " Produto Triplo Médio: {$rel["produto_triplo"]} \n";
                echo " Proximidade do Critério de Lawson: {$rel["status_lawson"]}% \n";
                
                if ($rel["status_lawson"] > 80) {
                    echo "⚠️  ALERTA: Condições de Ignição Iminentes! \n";
                }
            } else {
                echo "📉 Sem dados no banco para análise. \n";
            }
            break;

        case 0:
            echo "🔌 Desligando sistema do reator... Até logo! \n";
            exit;

        default:
            echo "⚙ Opção inválida. Tente novamente. \n";
            break;
    }
}