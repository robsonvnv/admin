<?php
	
	class Mascaras {

		public function texto_relacionado($data) {

			$data	=	$data[0];
			$retorno	=	$this->sistema->GetData($data['tabela'], $data['campo'], $data['indice'] . " = " . $data['valor'], "", "");
			$retorno	=	utf8_encode($retorno['data'][0][$data['campo']]);
			if ($retorno) {

				return $retorno;

			} else {

				return '- - -';

			}

		}

		public function texto($data) {

			return $data[0]['valor'];

		}

		public function flag_status($data) {

			return '<a href="">' . $data[0]['valor'] . '</a>';

		}

		public function ordem($data) {

			return '<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>';

		}

		public function __construct($sistema) {

			$this->sistema	=	$sistema;

		}

	}

?>