<?php
	
	class Pescador {

		public $conexao;
		public $base;
		public $mascaras;
		public $link;
		public $titulo;
		public $atual;
		public $acao;
		public $pagina;
		public $modulo;

		public function Conexao($servidor, $usuario, $senha, $banco) {

			$connection	=	new PDO('mysql:host=' . $servidor . ';dbname=' . $banco, $usuario, $senha);
			$this->conexao	=	$connection;
			return true;

		}


		public function GerarSlug($text) {
			// replace non letter or digits by -
			$text = preg_replace('~[^\pL\d]+~u', '-', $text);

			// transliterate
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

			// remove unwanted characters
			$text = preg_replace('~[^-\w]+~', '', $text);

			// trim
			$text = trim($text, '-');

			// remove duplicate -
			$text = preg_replace('~-+~', '-', $text);

			// lowercase
			$text = strtolower($text);

			if (empty($text)) {
			return 'n-a';
			}

			return $text;

		}

		public function RemoverTraco($text) {

			return str_replace('-', '_', $text);

		}


		public function GerarLog($pagina) {

			$data	=	now();

		}


		public function GetData($tabela, $campos, $condicao = "", $limite = "", $ordem = "") {

			if ($condicao != "") {

				if ($limite == "") {

					if ($ordem == "") {

						$sql	=	"SELECT " . $campos . " FROM " . $tabela . " WHERE " . $condicao;

					} else {
						
						$sql	=	"SELECT " . $campos . " FROM " . $tabela . " WHERE " . $condicao . " ORDER BY " . $ordem;

					}

				} else {
					
					if ($ordem == "") {
						
						$sql	=	"SELECT " . $campos . " FROM " . $tabela . "LIMIT " . $limite;

					} else {
						
						$sql	=	"SELECT " . $campos . " FROM " . $tabela . "LIMIT " . $limite . " ORDER BY " . $ordem;

					}

				}

			} else {
				
				if ($limite == "") {
					
					if ($ordem == "") {

						$sql	=	"SELECT " . $campos . " FROM " . $tabela;

					} else {
						
						$sql	=	"SELECT " . $campos . " FROM " . $tabela . " ORDER BY " . $ordem;

					}

				} else {
					
					if ($ordem == "") {

						$sql	=	"SELECT " . $campos . " FROM " . $tabela . " LIMIT " . $limite;

					} else {
						
						$sql	=	"SELECT " . $campos . " FROM " . $tabela . " ORDER BY " . $ordem . " LIMIT " . $limite;

					}

				}

			}

			$preparar			=	$this->conexao->prepare($sql);
			$execute			=	$preparar->execute();
			$result				=	$preparar->fetchAll();
			$count				=	count($result);
			$retorno['data']	=	$result;
			$retorno['total']	=	$count;
			return $retorno;

		}


		public function GerarPaginacao($tabela, $indice, $colunas, $limite = 10, $ordem, $condicoes = "") {

			$colunasTotal	=	count($colunas);
			if ($colunasTotal >= 1) {
				
				$chaves			=	array_keys($colunas);
				$chavesTotal	=	count($chaves);
				if ($chavesTotal >= 1) {

					if ($chavesTotal == 1) {

						$campos	=	$indice . ', ' .$chaves[0];

					} else {

						$campos	=	$indice . ', ';
						$camposCount	=	1;
						foreach ($chaves as $campo) {

							$campos	.=	$campo;
							if ($camposCount < $chavesTotal) {
								
								$campos	.=	', ';

							}

							$camposCount++;
							
						}

					}

				} else {

					echo 'Falha na configuração da paginação';

				}


				$registros	=	$this->GetData($tabela, $campos, $condicoes, "", $ordem);
				$registrosTotal	=	$registros['total'];

				$numPaginas	=	ceil($registrosTotal/$limite);


				if (!$this->pagina || $this->pagina == '' || $this->pagina == '0' || $this->pagina == 0) {

					$pc	=	1;

				} else {
					
					$pc	=	$this->pagina;

				}

				$inicio		=	$pc - 1;
				$inicio		=	$inicio * $limite;
				$limite		=	$inicio . ', ' . $limite;



				$registros	=	$this->GetData($tabela, $campos, $condicoes, $limite, $ordem);
				
				if ($registrosTotal >= 1) {

					$paginacao	 =	'<div class="text-right">Exibindo ' . $limite . ' registro(s) de um total de ' . $registrosTotal . ' registro(s).</div>';

				} else {
					
					$paginacao	 =	'<div class="text-right">&nbsp;</div>';

				}

				$paginacao	 .=	'<table class="table table-bordered table-striped">';
					
					$paginacao	.=	'</thead>';
						
						$paginacao	.=	'<tr>';

							foreach ($colunas as $colunaValores) {
								
								$paginacao	.=	'<th class="text-center">' . $colunaValores['nome'] . '</th>';

							}
							
						$paginacao	.=	'</tr>';

					$paginacao	.=	'</thead>';
					$paginacao	.=	'<tbody>';

						if ($registrosTotal >= 1) {

							foreach ($registros['data'] as $registroValores) {
								
								$paginacao	.=	'<tr>';

									$registroIndices	=	array_keys($registroValores);
									foreach ($registroIndices as $registroIndice) {

										if ($registroIndice != '0') {
											
											if (in_array($registroIndice, array_keys($colunas))) {

												$data	=	array($colunas[$registroIndice]);
												$data[0]['valor']	=	utf8_encode($registroValores[$registroIndice]);

												$mascara	=	$colunas[$registroIndice]['tipo'];

												$paginacao	.=	'<td class="text-center">';

													$paginacao	.=	$this->mascaras->$mascara($data); //print_r($colunas[$registroIndice]);

												$paginacao	.=	'</td>';

											}

										}

									}

								$paginacao	.=	'</tr>';

							}


						} else {

							
							$paginacao	.=	'<tr>';
								
								$paginacao	.=	'<td style="text-align: center;" colspan="' . $colunasTotal .'">Nenhum registro encontrado</td>';

							$paginacao	.=	'</tr>';

						}
						
					$paginacao	.=	'</tbody>';

				$paginacao	.=	'</table>';

				if ($registrosTotal >= 1) {
					
					$paginacao	.=	'<nav aria-label="Page navigation" aria-label="...">';
						
						$anterior	=	$pc - 1;
						$proximo	=	$pc + 1;
						$paginacao	.=	'<ul class="pagination pagination-lg">';
							
							if ($pc > 1) {

								$paginacao	.=	'<li>';
									
									if ($this->acao) {
										
										$paginacao	.=	'<a href="' . $this->link . $this->acao . '/pagina/' . $anterior . '/" aria-label="Previous">';

											$paginacao	.=	'<span aria-hidden="true">&laquo; anterior</span>';

										$paginacao	.=	'</a>';

									} else {

										$paginacao	.=	'<a href="' . $this->link . 'pagina/' . $anterior . '/" aria-label="Previous">';

											$paginacao	.=	'<span aria-hidden="true">&laquo; anterior</span>';

										$paginacao	.=	'</a>';

									}
								
								$paginacao	.=	'</li>';

							} else {

								$paginacao	.=	'<li class="disabled">';

									$paginacao	.=	'<a href="#" aria-label="Previous">';
										
										$paginacao	.=	'<span aria-hidden="true">&laquo; anterior</span>';

									$paginacao	.=	'</a>';
									
								$paginacao	.=	'</li>';

							}

							$paginacao	.=	'</li>';
							for ($i = 1; $i < $numPaginas+1; $i++) { 
								
								if ($i == $pc) {

									$paginacao	.=	'<li class="active">';
										
										$paginacao	.=	'<a>' . $i . '</a>';

									$paginacao	.=	'</li>';
								
								} else {

									$paginacao	.=	'<li>';
										
										if ($this->acao) {

											$paginacao	.=	'<a href="' . $this->link . $this->acao . '/pagina/' . $i. '/">' . $i . '</a>';
											
										} else {

											$paginacao	.=	'<a href="' . $this->link . 'pagina/' . $i. '/">' . $i . '</a>';

										}

									$paginacao	.=	'</li>';
								}

							}

							if ($pc == $numPaginas) {

								$paginacao	.=	'<li class="disabled">';

									$paginacao	.=	'<a href="#" aria-label="Next">';
										
										$paginacao	.=	'<span aria-hidden="true"> próximo</span>';

									$paginacao	.=	'</a>';
									
								$paginacao	.=	'</li>';

							} else {

								$paginacao	.=	'<li>';

									if ($this->acao) {

										$paginacao	.=	'<a href="' . $this->link . $this->acao . '/pagina/' . $proximo . '/" aria-label="Next">';
										
											$paginacao	.=	'<span aria-hidden="true"> próximo</span>';

										$paginacao	.=	'</a>';

									} else {

										$paginacao	.=	'<a href="' . $this->link . 'pagina/' . $proximo . '/" aria-label="Next">';
										
											$paginacao	.=	'<span aria-hidden="true"> próximo</span>';

										$paginacao	.=	'</a>';
									}
									
								$paginacao	.=	'</li>';

							}

						$paginacao	.=	'</ul>';
					
					$paginacao	.=	'</nav>';
				}

			} else {

				$paginacao	.=	'Função de paginação mal configurada';

			}

			return $paginacao;

		}



		public function GerarFormulario() {

		}


		public function GerarMenu() {

			$menus	=	$this->GetData("table_menu", "*", "menu_pai = 0 AND menu_status = 'Ativo'", "", "menu_ordem");
			if ($menus['total'] >= 1) {

				
				$html	=	'<nav class="navbar navbar-inverse navbar-fixed-top">';

					$html	.=	'<div class="container">';

						$html	.=	'<div class="navbar-header">';

							$html	.=	'<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">';
								
								$html	.=	'<span class="sr-only">Toggle navigation</span>';
								$html	.=	'<span class="icon-bar"></span>';
								$html	.=	'<span class="icon-bar"></span>';
								$html	.=	'<span class="icon-bar"></span>';

							$html	.=	'</button>';
						
							$html	.=	'<div id="navbar" class="collapse navbar-collapse">';
								
								$html	.=	'<ul class="nav navbar-nav">';
									
									foreach ($menus['data'] as $menu) {
										
										$submenus	=	$this->GetData("table_menu", "*", "menu_pai = " . $menu['menu_id'] . " AND menu_status = 'Ativo'", "", "menu_ordem");
										if ($submenus['total'] >= 1) {

											if ($menu['menu_id'] == $this->menu) {

												$html	.=	'<li class="dropdown">';
													
													$html	.=	'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . utf8_encode($menu['menu_nome']) . ' <span class="caret"></span></a>';
													$html	.=	'<ul class="dropdown-menu">';
													foreach ($submenus['data'] as $submenu) {

														$html	.=	'<li><a href="' . $this->base . $menu['menu_id'] . '/' . $submenu['menu_id'] . '/">' . utf8_encode($submenu['menu_nome']) . '</a></li>';

													}

													$html	.=	'</ul>';

												$html	.=	'</li>';

											} else {
												
												$html	.=	'<li class="dropdown">';
													
													$html	.=	'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . utf8_encode($menu['menu_nome']) . ' <span class="caret"></span></a>';
													$html	.=	'<ul class="dropdown-menu">';
													foreach ($submenus['data'] as $submenu) {

														$html	.=	'<li><a href="' . $this->base . $menu['menu_id'] . '/' . $submenu['menu_id'] . '/">' . utf8_encode($submenu['menu_nome']) . '</a></li>';

													}

													$html	.=	'</ul>';

												$html	.=	'</li>';

											}

										} else {

											if ($menu['menu_id'] == $this->menu) {

												$html	.=	'<li class="active"><a href="' . $this->base . '' . $menu['menu_id'] . '/">' . utf8_encode($menu['menu_nome']) . '</a></li>';

											} else {
												
												$html	.=	'<li><a href="' . $this->base . '' . $menu['menu_id'] . '/">' . utf8_encode($menu['menu_nome']) . '</a></li>';

											}

										}

									}
									
									$html	.=	'<li><a href="' . $this->base . 'sair/">Sair</a></li>';

								$html	.=	'</ul>';

							$html	.=	'</div>';

						$html	.=	'</div>';

					$html	.=	'</div>';

				$html	.=	'</nav>';

			} else {

				$html	=	'';

			}

			return $html;

		}


		public function GerarCabecalho() {

			$cabecalho	=	'<html>';

				$cabecalho	.=	'<head>';

					$cabecalho	.=	'<meta charset="utf-8" />';
					$cabecalho	.=	'<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
					$cabecalho	.=	'<meta name="viewport" content="width=device-width, initial-scale=1" />';
					$cabecalho	.=	'<meta name="robots" content="noindex, nofollow" />';
					$cabecalho	.=	'<title>' . $this->titulo . '</title>';
					$cabecalho	.=	'<link rel="stylesheet" href="' . $this->base . '_assets/css/bootstrap.min.css" />';
					$cabecalho	.=	'<link rel="stylesheet" href="' . $this->base . '_assets/css/bootstrap-theme.min.css" />';
					$cabecalho	.=	'<link rel="stylesheet" href="' . $this->base . '_assets/css/style.css" />';

				$cabecalho	.=	'</head>';
				$cabecalho	.=	'<body>';

					$cabecalho	.=	'<div id="tudo">';

			return $cabecalho;

		}


		public function GerarRodape() {

					$rodape	 =	'</div>';
					$rodape	.=	'<script type="text/javascript" src="' . $this->base . '_assets/js/jquery.min.js"></script>';
					$rodape	.=	'<script type="text/javascript" src="' . $this->base . '_assets/js/bootstrap.min.js"></script>';

				$rodape	.=	'</body>';

			$rodape	.=	'</html>';

			return $rodape;

		}

		public function GerarConteudo() {

			if ($this->atual == '' || !$this->atual || $this->atual == '0' || $this->atual == 0) {

				$this->atual	=	1;

			}


			$menu	=	$this->GetData("table_menu", "modulo_id", "menu_id = " . $this->atual, "", "menu_ordem");

			echo '<div class="container">';
				
				echo '<div style="position: relative; display: table; width: 100%;">';
					
					$modulo	=	$this->GetData("table_modulos", "modulo_nome", "modulo_id = " . $menu['data'][0]['modulo_id'], "", "");
					if ($modulo['total'] >= 1) {

						$moduloNome		=	utf8_encode($modulo['data'][0]['modulo_nome']);
						$moduloPasta	=	'_modules/' . $this->GerarSlug($moduloNome);
						if (file_exists($moduloPasta)) {

							$moduloSlug		=	$this->GerarSlug($moduloNome);
							$moduloArquivo	= 	$moduloPasta . '/' . $moduloSlug . '.mod.php';
							if (file_exists($moduloArquivo)) {

								require_once($moduloArquivo);
								$moduloClassName	=	$this->RemoverTraco(ucfirst($moduloSlug));

								if (class_exists($moduloClassName)) {

									new $moduloClassName($this, $this->acao);

								} else {

									echo 'O módulo está corrompido';

								}

							} else {

								echo 'O módulo está incompleto';

							}

						} else {

							echo 'O módulo não existe';

						}

					} else {

						echo 'Nenhum módulo vinculado a este menu';

					}

				echo '</div>';

			echo '</div>';

			return true;

		}


		public function PowerOn() {

			$acao		=	$_GET['acao'];
			$menu		=	$_GET['menu'];
			$submenu	=	$_GET['submenu'];
			$pagina		=	$_GET['pagina'];
			if (!$submenu || $submenu == 0 || $submenu == '' || $submenu == '0') {

				$this->atual	=	$menu;
				$this->link		=	$this->base . $menu . '/';

			} else {

				$this->atual	=	$submenu;
				$this->link		=	$this->base . $menu . '/' . $submenu . '/';

			}

			$this->acao		=	$acao;
			$this->pagina	=	$pagina;


			echo $this->GerarCabecalho();
			echo $this->GerarMenu();
			$this->GerarConteudo();
			echo $this->GerarRodape();

		}


		public function __construct($database, $sistema) {

			require_once('mascaras.inc.php');
			$this->mascaras	=	new Mascaras($this);
			
			$this->titulo	=	$sistema['titulo'];
			$this->base		=	$sistema['base'];

			$this->Conexao($database['servidor'], $database['usuario'], $database['senha'], $database['banco']);

		}


	}

?>