/* Javascript Navegação */

$(document).ready(function() {
	$('.acao').click(function(e) {
		var elementos = document.getElementsByClassName('acao');

		for (var x = 0; x < elementos.length; x++) {
			elementos[x].className = "acao";
		}

		this.className = "acao selecionado";
		e.preventDefault(); // Previne o Comportamento padrão do Link
		var exibir = this.href.split('#'); // Recebe a Página para Navegação

		$('section').hide(); // Esconde Páginas
		$('#'+exibir[1]).fadeIn(700); // Exibe Página Selecionada
	});
});