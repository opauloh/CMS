<?
	ob_start();
	##################################################################
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA 
	##################################################################
	include_once(__DIR__.'/../Lib/class-ws-v1.php');
	##################################################################
	# SETAMOS OS CABEÇALHOS DA APLICAÇÃO
	# Security-Policy, Allow-Origin e Content-Type  
	##################################################################
	header("Content-Type:application/json");


	##################################################################
	# FUNÇÃO ADDSLASHES NAS VARIÁVEIS
	##################################################################
	function AddSlashesArray(&$string, $x) { 
		if(is_numeric($string) || is_int($string)){
			$string = $string; 
		}else{
			$string = '"'.addSlashes($string).'"'; 
		}
	}

	##################################################################
	# VERIFICAMOS O TIPO DE REQUISIÇÃO FEITO PELO CLIENT 
	#################################################################
	switch ($_SERVER['REQUEST_METHOD']) {
		##################################################################  
		# CASO A REQUISIÇÃO SEJA FEITA VIA GET
		##################################################################
		case "GET":
			##################################################################  
			# VERIFICAMOS O TOKEN DE ACESSO
			##################################################################
			ws::getTokenRest(@$_SERVER['HTTP_TOKEN']);

			##################################################################
			# MONTAMOS A VARIÁVEL QUE RECEBERÁ A STRING DA CLASSE PHP
			##################################################################
			$RestTool   = array();
			$RestTool[] = '$RestTool';

			##################################################################
			# VARREMOS A URL E ADICIONAMOS AS VARIÁVEIS
			##################################################################
			foreach ($_GET as $key => $value) {
				if(isset($value) && $value!=""){
					if(is_array($value)){
						foreach ($value as $v) {
							if(is_array($v)){
								array_walk($v,"AddSlashesArray");
								$RestTool[] = $key . '('.implode($v,',').')';
							}else{
								if(is_numeric($v)){
									$RestTool[] = $key . '(' . ($v) . ')';
								}else{
									$RestTool[] = $key . '("' . addslashes($v) . '")';						
								}
							}
						}
					}else{
						if(is_numeric($value)){
							$RestTool[] = $key . '(' . ($value) . ')';
						}else{
							$RestTool[] = $key . '("' . addslashes($value) . '")';						
						}
					}
				}
			}

			##################################################################
			# AGORA MONTAMOS A STRING DA CLASSE PHP IMPLODINDO A ARRAY COM "->"
			##################################################################
			eval('$RestTool=new WS();$RestTool='.implode($RestTool, '->').'->go()->obj;');

			##################################################################
			# RETORNAMOS O RESULTADO DE PESQUISA NO FORMADO DE JSON
			##################################################################
			echo json_encode($RestTool, JSON_PRETTY_PRINT);

			break;
		case "POST":
			##################################################################  
			# VERIFICAMOS O TOKEN DE ACESSO
			##################################################################
			ws::getTokenRest(@$_SERVER['HTTP_TOKEN']);

			##################################################################
			# UPDATE
			##################################################################
			$RestTool   = array();
			$RestTool[] = '$RestTool';

			if(empty($_POST['item']) 	|| $_POST['']=="")		{die("Valor inexistente: item=>''");}
			if(empty($_POST['type']) 	|| $_POST['']=="")		{die("Valor inexistente: type=>''");}
			if(empty($_POST['slug']) 	|| $_POST['']=="")		{die("Valor inexistente: slug=>''");}
			if(empty($_POST['draft']) 	|| $_POST['']=="")		{die("Valor inexistente: draft=>''");}

			foreach ($_POST as $key => $value) {
				if(isset($value) && $value!="" && $key!="item" && $key!="type" && $key!="slug" && $key!="draft"){
					if(is_array($value)){
						foreach ($value as $v) {
							if(is_array($v)){
								array_walk($v,"AddSlashesArray");
								$RestTool[] = 'updateVal("'.$key.'",'.implode($v,',').')';
							}else{
								if(is_numeric($v)){
									$RestTool[] =  'updateVal("'.$key.'",' . ($v) . ')';
								}else{
									$RestTool[] =  'updateVal("'.$key.'", "' . addslashes($v) . '")';						
								}
							}
						}
					}else{
						if(is_numeric($value)){
							$RestTool[] = 'updateVal("'.$key.'",' . ($value) . ')';
						}else{
							$RestTool[] = 'updateVal("'.$key.'","' . addslashes($value) . '")';						
						}
					}
				}
			}

			$RestTool[] = 'type("'.$_POST['type'].'")';
			$RestTool[] = 'slug("'.$_POST['slug'].'")';
			$RestTool[] = 'draft("'.$_POST['draft'].'")';
			$RestTool[] = 'item("'.$_POST['item'].'")';

			##################################################################
			# AGORA MONTAMOS A STRING DA CLASSE PHP IMPLODINDO A ARRAY COM "->"
			##################################################################
			eval('$RestTool=new WS();$RestTool='.implode($RestTool, '->').'->save();');
			if($RestTool){
				die ('[{status:"ok"}]');
			}else{
				die ('[{status:"fail"}]');
			};
			break;
		case "DELETE":
			##################################################################
			# DELETE, EM BREVE...
			##################################################################
			break;
		case "PUT":
			##################################################################  
			# VERIFICAMOS O TOKEN DE ACESSO
			##################################################################
			ws::getTokenRest(@$_SERVER['HTTP_TOKEN']);

			##################################################################  
			# DEFINIMOS A VARIÁVEL $_PUT COM OS VALORES ENVIADOS
			##################################################################
			parse_str(file_get_contents("php://input"),$_PUT);

			##################################################################
			# MONTAMOS A VARIÁVEL QUE RECEBERÁ A STRING DA CLASSE PHP
			##################################################################
			$RestTool   = array();
			$RestTool[] = '$RestTool';

			##################################################################
			# VARREMOS A URL E ADICIONAMOS AS VARIÁVEIS
			##################################################################
			foreach ($_PUT as $key => $value) {
				if(isset($value) && $value!="" && $key!="insertVal" && $key!="type" && $key!="slug" && $key!="draft"){
					if(is_array($value)){
						foreach ($value as $v) {
							if(is_array($v)){
								array_walk($v,"AddSlashesArray");
								$RestTool[] = 'insertVal("'.$key.'",'.implode($v,',').')';
							}else{
								if(is_numeric($v)){
									$RestTool[] = 'insertVal("'.$key.'",'. ($v) . ')';
								}else{
									$RestTool[] = 'insertVal("'.$key.'","' . addslashes($v) . '")';						
								}
							}
						}
					}else{
						if(is_numeric($value)){
							$RestTool[] = 'insertVal("'.$key.'",'. ($value) . ')';
						}else{
							$RestTool[] = 'insertVal("'.$key.'","' . addslashes($value) . '")';						
						}
					}
				}
			}

			if(empty($_PUT['type'])){die("Defina um tipo: 'type:' ");					}
			if(empty($_PUT['slug'])){die("Defina um slug: 'slug:' ");					}
			if(empty($_PUT['draft'])){die("Defina se terá rascunho ou não: 'draft:' ");	}

			$RestTool[] = 'type("'.$_PUT['type'].'")';
			$RestTool[] = 'slug("'.$_PUT['slug'].'")';
			$RestTool[] = 'draft("'.$_PUT['draft'].'")';

			##################################################################
			# AGORA MONTAMOS A STRING DA CLASSE PHP IMPLODINDO A ARRAY COM "->"
			##################################################################
			
			eval('$RestTool=new WS();$RestTool='.implode($RestTool, '->').'->insert();');

			##################################################################
			# RETORNAMOS O RESULTADO DE PESQUISA NO FORMADO DE JSON
			##################################################################
			echo json_encode($RestTool, JSON_PRETTY_PRINT);


			break;
		default:
			break;
	}
	/*
		############################################################################################################
		# GET
		############################################################################################################
			$GETDATA = http_build_query(
				array(
					'type' 			=> 'item',
					'slug' 			=> 'teste',
					'limit' 		=> '',
					'innerCategory' => (int)"id",
					'innerItem' 	=> (int)"id",
					'item' 			=> (int)"id",
					'setTemplate' 	=> '<div></div>',
					'where' 		=> 'query',
					'alias' 		=> '_alias_',
					'paginate' 		=> 'pag, $limit',
					'relLinker' 	=> (int)"id",
					'relLinked' 	=> (int)"id"
					'relType' 		=> 'item or cat'
					'galery' 		=> (int)"id"
					'like' 			=> array(array("coluna","palavra"),array("coluna","palavra"),array("coluna","palavra"))
					'distinct' 		=> ''
					'utf8' 			=> ''
					'url' 			=> ''
					'order' 		=> ''
					'filter' 		=> ''
				)
			);
			$HEADER  = array(
				'http' => array(
					'method' => 'GET',
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . 
								"token:" . ws::setTokenRest()."\r\n",
					'content' => $GETDATA
				)
			);
			$CONTENT = stream_context_create($HEADER);
			echo file_get_contents('http://' . DOMINIO . '/ws-rest/?' . $GETDATA, false, $CONTENT);
		############################################################################################################
		# PUT
		############################################################################################################

			$GETDATA = http_build_query(
					array(
						'type' 			=> 'item',
						'slug' 			=> 'teste',
						'draft' 		=> true,
						'fn_save_tool'  => 'TITULO DA PARADA!',
						'uploadtxt'     => 'DESCRIÇÃO GERAL',
					)
				);
			$HEADER  = array(
				'http' => array(
					'method' => 'PUT',
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . 
								"token:" . ws::setTokenRest('5 seconds')."\r\n",
					'content' => $GETDATA
				)
			);
			$CONTENT = stream_context_create($HEADER);
			echo file_get_contents('http://' . DOMINIO . '/ws-rest/', false, $CONTENT);
			
		############################################################################################################
		# POST
		############################################################################################################
			    $GETDATA = http_build_query(
			    	array(
			    		'type' 			=> 'item',
			    		'slug' 			=> 'teste',
			    		'item'     	    => 58,
			    		'draft' 		=> true,
			    		'fn_save_tool'  => 'ISRAEL FODA!',
			    		'uploadtxt'     => 'É REALMENTE FODA',
			    	)
			    );
			    $HEADER  = array(
			        'http' => array(
			        	'method' => 'POST',
			        	'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . 
			        				"token:" . ws::setTokenRest('5 seconds')."\r\n",
			        	'content' => $GETDATA
			        )
			    );
			    $CONTENT = stream_context_create($HEADER);
			    echo file_get_contents('http://' . DOMINIO . '/ws-rest/', false, $CONTENT);
	*/

