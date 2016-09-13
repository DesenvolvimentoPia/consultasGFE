/* Javascript Navegação */

$(document).ready(function() {
	$('.acao').click(function(e) {
		e.preventDefault();
		var exibir = this.href.split('#');
		//alert(exibir[1]);

		$('section').hide();
		$('#'+exibir[1]).fadeIn(700);
	});
});