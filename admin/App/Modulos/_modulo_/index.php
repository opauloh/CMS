<?
	############################################################################
	# IMPORTA A CLASSE PADRÃO DO WEBSHEEP
	############################################################################
	include_once(__DIR__.'/../../Lib/class-ws-v1.php');

	############################################################################
	# INICIAMOS A SESSÃO
	############################################################################
	// _session();
	$session = new session();

	#####################################################  
	# RESTAURA AS TABELAS 
	#####################################################  
	ws::updateTool($_GET['ws_id_ferramenta']);

	############################################################################
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	############################################################################
	verifyUserLogin();

	############################################################################
	# GRAVAMOS NA SESSÃO UM GRUPO DE UPLOAD PARA FUTURAS PESQUISAS
	############################################################################
	$session->set('token_group',_token(PREFIX_TABLES."ws_biblioteca","token_group"));
	$session->set('ws_id_ferramenta',$_GET['ws_id_ferramenta']);

	############################################################################
	# DEFINIMOS O PATH PADRÃO DO MÓDULO 
	############################################################################
	define("_PATH_",'App/Modulos/_modulo_');

	############################################################################
	# LIMPAMOS O CACHE INTERNO DO PHP
	############################################################################
	clearstatcache();

	############################################################################
	# CASO NÃO TENHA CATEGORIAS OU NÍVEIS REQUERIDOS
	############################################################################
	if(empty($_GET['id_cat'])	){			$_GET['id_cat']			='0';			}
	if(empty($_GET['ws_nivel'])	){			$_GET['ws_nivel']		='0';			}

	############################################################################
	# CAPTA DADOS DA FERRAMENTA
	############################################################################
	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ = @$_FERRAMENTA_->fetch_array[0];

	############################################################################
	# VERIFICA SE VEM DE ALGUMA CATEGORIA
	############################################################################
	$_CAT_PAI_ 					= new MySQL();
	$_CAT_PAI_->set_table(PREFIX_TABLES.'_model_cat');
	$_CAT_PAI_->set_where('id_cat<>0');
	$_CAT_PAI_->set_where('AND id="'.$_GET['id_cat'].'"');
	$_CAT_PAI_->debug(0);
	$_CAT_PAI_->select();
	$_CATEGORIA_PAI = @$_CAT_PAI_->fetch_array[0];

	############################################################################
	# DEIXA COMO PADRÃO 0
	############################################################################
	if(!$_CAT_PAI_->_num_rows){
		$_CATEGORIA_PAI 			=	array();
		$_CATEGORIA_PAI['id']		=	0;
		$_CATEGORIA_PAI['ws_nivel']	=	0;
		$_CATEGORIA_PAI['id_cat']	=	0;
		$_CATEGORIA_PAI['titulo']	=	"";
	}

	############################################################################
	# CAPTA TÍTULO DA CATEGORIA PAI
	############################################################################
	$_TIT_PAI_ 					= new MySQL();
	$_TIT_PAI_->set_table(PREFIX_TABLES.'_model_cat');
	$_TIT_PAI_->set_where('id="'.$_GET['id_cat'].'"');
	$_TIT_PAI_->debug(0);
	$_TIT_PAI_->select();

	############################################################################
	# LISTA CATEGORIAS COM O ID_CAT SETADO NO GET
	############################################################################
	$_CATEGORIAS_ 					= new MySQL();
	$_CATEGORIAS_->set_table(PREFIX_TABLES.'_model_cat');
	$_CATEGORIAS_->set_where('id_cat="'.$_GET['id_cat'].'"');
	$_CATEGORIAS_->set_order('posicao',"ASC");
	$_CATEGORIAS_->debug(0);
	$_CATEGORIAS_->select();

	############################################################################
	# DEIXA COMO PADRÃO 0
	############################################################################
	if(!$_CATEGORIAS_->_num_rows){
		$_CATEGORIAS_ 				=	array();
		$_CATEGORIAS_['id']			=	0;
		$_CATEGORIAS_['ws_nivel']	=	0;
		$_CATEGORIAS_['id_cat']		=	0;
		$_CATEGORIAS_['titulo']		=	"";
	}

	############################################################################
	# LISTAMOS OS CAMPOS DAS FERRAMENTAS
	############################################################################
	$_CAMPOS_ 					= new MySQL();
	$_CAMPOS_->set_table(PREFIX_TABLES.'_model_campos');
	$_CAMPOS_->set_where('ws_id_ferramenta="'.$_GET['ws_id_ferramenta'].'"');
	$_CAMPOS_->select();


	############################################################################
	# VERIFICA E RETORNA O ARQUIVO CERTO
	############################################################################
	if($_FERRAMENTA_['_niveis_']== -1 && $_CAMPOS_->_num_rows==1 && $_CAMPOS_->fetch_array[0]['type']=='bt_fotos'){
		echo '<script type="text/javascript">
				include_css ("./App/Templates/css/websheep/modulos/_modulo_/imagens.min.css?v='.md5(uniqid(rand(), true)).'","css_mod","All");
				$("#conteudo").load("./'._PATH_.'/imagens.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&back=false&id_item=1&ws_nivel=-1&id_cat=1");
			</script>';exit;
		exit;

	}elseif($_FERRAMENTA_['_niveis_']== -1 && $_CAMPOS_->_num_rows==1 && $_CAMPOS_->fetch_array[0]['type']=='bt_galerias'){
		echo '<script type="text/javascript">
				include_css ("./App/Templates/css/websheep/modulos/_modulo_/galerias.css?v='.md5(uniqid(rand(), true)).'","css_mod","All");
				$("#conteudo").load("./'._PATH_.'/galerias.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&back=false&id_item=1&ws_nivel=-1");
			</script>';exit;
		exit;

	}elseif($_FERRAMENTA_['_niveis_']== -1 && $_CAMPOS_->_num_rows==1 && $_CAMPOS_->fetch_array[0]['type']=='bt_files'){
		echo '<script type="text/javascript">
				include_css ("./App/Templates/css/websheep/modulos/_modulo_/style_files.min.css?v='.md5(uniqid(rand(), true)).'","css_mod","All");
				$("#conteudo").load("./'._PATH_.'/files.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&back=false&id_item=1&ws_nivel=-1");
			</script>';exit;
		exit;

	}elseif($_FERRAMENTA_['_niveis_']	== -1 ){
		echo '<script type="text/javascript">
				$("#conteudo").load("./'._PATH_.'/detalhes.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&back=false");
			</script>';exit;
		exit;

	}elseif(isset($_GET['edita']) && $_GET['edita']=='cat'){
		echo '<script type="text/javascript">
				include_css ("./App/Templates/css/websheep/modulos/_modulo_/style.css?v='.md5(uniqid(rand(), true)).'","css_mod","All");
				$("#conteudo").load("./'._PATH_.'/categorias.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&id_cat=0&ws_nivel=0");
			</script>';exit;
		exit;

	}else{
		echo '<script type="text/javascript">
				$("#conteudo").load("./'._PATH_.'/itens.php?token_group='.$session->get('token_group').'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'].'&id_cat='.$_CATEGORIA_PAI['id'].'&ws_nivel='.$_CATEGORIA_PAI['ws_nivel'].'");
				</script>';
		exit;

	}