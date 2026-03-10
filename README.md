Projeto Tokamak - SISM
Sistema em PHP para monitorar e registrar testes de fusão nuclear em um reator Tokamak. Intuito: praticar lógica de programação e persistência de dados.

O que o sistema faz?
Gerencia Testes: Cadastra, edita, mostra as estatísticas, lista e remove disparos do reator.

Dados: são gravados em um arquivo .txt, então as informações não somem ao fechar o programa.

Cálculo Científico: Calcula o "Produto Triplo" (Temperatura × Tempo) e compara com o Critério de Lawson para medir a eficiência da fusão.

Arquivos
index.php: Menu interativo e interface do usuário.

biblioteca.php: Funções de cálculo e banco de dados.

banco_de_dados.txt: Onde os dados ficam salvos.
