<?php
	
	class Gerenciar_menu {

		private function ListarMenu() {

			$colunas	=	array(
				
				'menu_nome'	=>	array(

					'tipo'	=>	'texto',
					'nome'	=>	'Nome'

				),

				'modulo_id'	=>	array(

					'tipo'		=>	'texto_relacionado',
					'nome'		=>	'Modulo',
					'tabela'	=>	'table_modulos',
					'indice'	=>	'modulo_id',
					'campo'		=>	'modulo_nome'

				),

				'menu_status'	=>	array(

					'tipo'	=>	'flag_status',
					'nome'	=>	'Status'

				),

				'menu_ordem'	=>	array(

					'tipo'	=>	'ordem',
					'nome'	=>	'Ordem'

				)

			);

			$listar =	$this->sistema->GerarPaginacao("table_menu", 'menu_id', $colunas, 10, 'menu_ordem', '');

			return $listar;
			

		}


		private function GerarAcoes() {

			$cabecalho	=	'<div class="page-header">';
				
				$cabecalho	.=	'<h2>Gerenciar Menu</h2>';
				
			$cabecalho	.=	'</div>';
			$cabecalho	.=	'<div style="position: relative; display: table; width: 100%; margin-bottom: 20px;">';

				$cabecalho	.=	'<a href="' . $this->sistema->link . 'listar/" class="btn btn-primary btn-lg" role="button">Listar menu</a> &nbsp;';
				$cabecalho	.=	'<a href="' . $this->sistema->link . 'inserir/" class="btn btn-primary btn-lg" role="button">Cadastrar menu</a>';

			$cabecalho	.=	'</div>';

			return $cabecalho;

		}

		public function __construct($sistema, $acao) {

			$this->sistema	=	$sistema;
			$result	=	$this->GerarAcoes();

			if ($acao == '' || !$acao || $acao == 'listar') {
				
				$result	.=	$this->ListarMenu();

			}			

			echo $result;

		}

	}

?>