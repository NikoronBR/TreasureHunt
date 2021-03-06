<?php
// Codifica os caracteres
header("Content-type: text/html; charset=utf-8");
require_once 'conexao.php';

ob_start();
@session_start();

if (!isset($_SESSION['usuario']) == true) {
	unset($_SESSION['usuario']);
	header('location:index.php');
}

$problema = filter_input(INPUT_POST, 'problema');
$flagPura = filter_input(INPUT_POST, 'flag');
// não precisamos mais do hash da flag para verificar se a resposta coincide com o hash do bd
// $flag = hash("sha256", $flagPura); 
$usuario = $_SESSION['usuario'];


// Mesmo insert abaixo porém de versões anteriores
// $sql = "INSERT INTO TreasureHunt.Submissao VALUES ($usuario, $problema, '$flagPura', date('Y-m-d H:i:s'))";
// $sql = "INSERT INTO TreasureHunt.Submissao VALUES ($usuario, $problema, '$flagPura', CURRENT_TIMESTAMP)";

// Insere idUsuario, idResposta e flagPura na tabela submissão
$ip = $_SERVER['REMOTE_ADDR'];
$sql = "INSERT INTO TreasureHunt.Submissao VALUES ($usuario, $problema, '$flagPura', '$ip', CURRENT_TIMESTAMP)";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->bindParam(':problema', $problema, PDO::PARAM_STR);
// $stmt->bindParam(':flag', $flag, PDO::PARAM_STR); // Versão anterior enviava o hash ao invés da "flag pura" para o bd
$stmt->bindParam(':flag', $flagPura, PDO::PARAM_STR);
$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
$stmt->execute();

// Verifica se o usuário já acertou a questão
$sql = "SELECT * FROM TreasureHunt.Resposta WHERE idUsuario='$usuario' AND idProblema='$problema' AND acertou=true";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->bindParam(':problema', $problema, PDO::PARAM_STR);
$stmt->execute();

// Se retornar resultado, usuário já havia respondido corretamente
if ($stmt->rowCount() > 0) { // "erro" (aviso): questão já acertada
	header('Location:home.php?message=duplicada&id='.$problema);
	exit();
} else {

	// Se acerta a resposta e depois segue informando,
	// informa se está certa ou errada, mas não incrementa
	// as tentativas.

	// Poderia informar quando a resposta correta já foi submetida
	// e o usuário segue submetendo para o mesmo problema

	// versão antiga do select abaixo: atualizado para usar bcrypt
	// $sql = "SELECT * FROM TreasureHunt.Resposta WHERE idUsuario='$usuario' AND idProblema='$problema' AND resposta='$flag'";
	$sql = "SELECT resposta FROM TreasureHunt.Resposta WHERE idUsuario='$usuario' AND idProblema='$problema'";
	$stmt = $conexao->prepare($sql);
	$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
	$stmt->bindParam(':problema', $problema, PDO::PARAM_STR);
	// $stmt->bindParam(':flag', $flag, PDO::PARAM_STR); // não usamos mais o hash da flag digitada no select
	$stmt->execute();
	$resposta_hash = $stmt->fetch();


	// versão antiga desse if: fazia uso do select comentado (versão 256)
	// if ($stmt->rowCount() > 0) {

	// verifica a flag digitada com o hash da resposta armazenado no bd; se retornar true é a resposta correta
	if (password_verify($flagPura, $resposta_hash[0])) {
		$acertou = true;
		atualiza($acertou, $usuario, $problema);

		$stmt = $conexao->prepare("SELECT COUNT(*) AS Total FROM TreasureHunt.Resposta WHERE idUsuario=$usuario");
		$stmt->execute();
		$linhaTotal = $stmt->fetch(PDO::FETCH_OBJ);

		$stmt = $conexao->prepare("SELECT COUNT(*) AS Acertos FROM TreasureHunt.Resposta WHERE idUsuario=$usuario and acertou=1");
		$stmt->execute();
		$linhaAcertos = $stmt->fetch(PDO::FETCH_OBJ);
		// enviar mensagem de que usuário acertou questão (mostra na caixa verde)
		header('Location:home.php?message=acertou&acertos='.$linhaAcertos->Acertos.'&total='.$linhaTotal->Total);
		exit();
	} else { // se resposta não coincidir com hash do bd, resposta digitada é incorreta
		$stmt = $conexao->prepare("SELECT MAX(idProblema) AS Max FROM TreasureHunt.Resposta");
		$stmt->execute();
		$linhaTotal = $stmt->fetch(PDO::FETCH_OBJ);
		if ($problema < 1 or $problema > $linhaTotal->Max) { // erro: id inválido
			header('Location:home.php?message=id_invalido');
			exit();
		} else {
		$acertou = false;
		atualiza($acertou, $usuario, $problema);

		$tamanho = strlen($flagPura);
    	$verificaPadrao = (substr($flagPura, 0, 13) === 'TreasureHunt{') && (substr($flagPura, $tamanho - 1, $tamanho) === '}');
    	$mensagem = "Errou!";
    	if ($verificaPadrao != 1) { // erro: flag no formato incorreto
			$mensagem .= " Considere submeter a flag no seguinte formato: TreasureHunt{texto-aleatorio}";
			header('Location:home.php?message=formato');
			exit();
		}
		header('Location:home.php?message=erro');
		exit();
		} 
	}
}

/* Função que atualiza a tabela de respostas
   quando o usuário submeter uma flag */
function atualiza($resposta, $usuario, $problema) {
	include 'conexao.php';

	$param="";

	if ($resposta == true) {
		$hora = date('Y-m-d H:i:s');
		$param = "acertou=1, hora='$hora',";
		//$param = "acertou=1,";
	}

	$sql = "UPDATE TreasureHunt.Resposta SET $param tentativas=tentativas+1 WHERE idUsuario='$usuario' AND idProblema='$problema' AND acertou=0";
	$stmt = $conexao->prepare($sql);
	$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
	$stmt->bindParam(':problema', $problema, PDO::PARAM_STR);
	$stmt->execute();
}
?>
<script>
	window.setTimeout("location.href='home.php';");
</script>