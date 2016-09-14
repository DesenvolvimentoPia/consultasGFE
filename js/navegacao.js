/* Javascript Navegação */

$(document).ready(function() {
	$('.acao').click(function(e) {
		var elementos = document.getElementsByClassName('acao');
		//alert(elementos);
		for (var x = 0; x < elementos.length; x++) {
			elementos[x].className = "acao";
		}

		this.className = "acao selecionado";
		e.preventDefault();
		var exibir = this.href.split('#');
		//alert(exibir[1]);

		$('section').hide();
		$('#'+exibir[1]).fadeIn(700);
	});
});