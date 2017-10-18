<?php
include_once(__DIR__.'/../../Lib/class-ws-v1.php');

$_pesoTotalFilesSite = 0;
$_ArquivosFTP_ =array();

	function FTP_FILES_SIZE($dir,$oq =""){
		global $_pesoTotalFilesSite;
		global $_ArquivosFTP_;
		if (is_dir($dir)) {
			$dh = opendir($dir);
			while($diretorio = readdir($dh)){
				if($diretorio != '..' && $diretorio != '.' && is_dir($dir.'/'.$diretorio)){
						FTP_FILES_SIZE($dir.'/'.$diretorio);
				}elseif($diretorio != '..' && $diretorio != '.' && !is_dir($dir.'/'.$diretorio)){
						$peso = @filesize($dir.'/'.$diretorio);
						$_ArquivosFTP_[]= array('<b>Arquivo:</b>'.str_replace(ROOT_WEBSITE,"",$dir.'/'.$diretorio), $peso);
						$_pesoTotalFilesSite +=  $peso;
					};
			};
		};
	};




function returnFerramentas(){
			global $_pesoTotalFilesSite;
			$FERRAMENTA_ARRAY=array();
			header('Content-Type: application/json');
			$FERRAMENTA_ARRAY=array();
			$colunas= array();
			$AllFilesInModule 	= array();
			$AllFilesOutModule 	= array();

			//  AGORA PEGA OS RESULTADOS DESSAS COLUNAS QUE NÃO SÃO VAZIOS E ADD NA ARRAY...
			$itens = new MySQL(); $itens->set_table(PREFIX_TABLES.'_model_img'); $itens->select();
			foreach ($itens->fetch_array as $imagens){$AllFilesInModule[]=$imagens['imagem']; };

			$itens = new MySQL(); $itens->set_table(PREFIX_TABLES.'_model_img_gal'); $itens->select();
			foreach ($itens->fetch_array as $imagens){$AllFilesInModule[]=$imagens['file']; };

			$itens = new MySQL(); $itens->set_table(PREFIX_TABLES.'_model_files'); $itens->select();
			foreach ($itens->fetch_array as $imagens){$AllFilesInModule[]=$imagens['file']; };
			
			$itens = new MySQL(); $itens->set_table(PREFIX_TABLES.'ws_biblioteca'); $itens->select();
			foreach ($itens->fetch_array as $arquivos){if(!in_array($arquivos['file'], $AllFilesInModule)){$AllFilesOutModule[]= $arquivos['file']; } };


			$pesoUtilizado = 0;
			foreach ($AllFilesInModule as $arquivo){
				$peso = @filesize(ROOT_WEBSITE.'/assets/upload-files/'.$arquivo);
				if($peso!=false){
					$pesoUtilizado +=$peso;
				}
			};
			$FERRAMENTA_ARRAY[]= array('Em uso no site', $pesoUtilizado);

			$pesoNaoUtilizado = 0;
			foreach ($AllFilesOutModule as $arquivo){
				$peso = @filesize(ROOT_WEBSITE.'/assets/upload-files/'.$arquivo);
				if($peso!=false){
					$pesoNaoUtilizado +=$peso;						
				}
			};
			$FERRAMENTA_ARRAY[]= array('Nao utilizado', $pesoNaoUtilizado);

			FTP_FILES_SIZE(ROOT_WEBSITE);
			$todo_peso = $pesoNaoUtilizado+$pesoNaoUtilizado+$_pesoTotalFilesSite;

			$FERRAMENTA_ARRAY[]= array('Arquivos no FTP', $_pesoTotalFilesSite);


			$itens = new MySQL(); $itens->set_table(PREFIX_TABLES.'setupdata'); $itens->select();
			$EspacoLivre = (($itens->fetch_array[0]['hd']*1024)*1024) - $todo_peso; 


			$FERRAMENTA_ARRAY[]= array('name'=>'Espaço livre', 'y'=>$EspacoLivre,'sliced'=>'true','selected'=>'true');
			echo json_encode($FERRAMENTA_ARRAY);
}


function returnAllFilesSizes(){
		global $_ArquivosFTP_;
		FTP_FILES_SIZE(ROOT_WEBSITE);
		header('Content-Type: application/json');
		echo json_encode($_ArquivosFTP_);
/*		$itens = new MySQL(); 
		$itens->set_table(PREFIX_TABLES.'ws_biblioteca'); 
		$itens->select();
		foreach ($itens->fetch_array as $arquivos){
			// $_ArquivosFTP_[]= array(
			// 	ROOT_WEBSITE.'/assets/upload-files'.$arquivos['filename'], 
			// 	@filesize(ROOT_WEBSITE.'/assets/upload-files'.$arquivos['file'])
			// );
		};
*/
}

######################################################################

_session();
_exec($_REQUEST['function']);
