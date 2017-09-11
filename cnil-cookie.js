/* Plugin pour ajouter une bulle d'information sur l'utilisation des cookies */

// Traduction
add_translation({
	"We use cookies to track site usage and improve it. <u>Hide</u>" : {"fr" : "Nous utilisons les cookies pour suivre l'utilisation du site et l'am\u00e9liorer. <u>Masquer</u>"}
});

$(document).ready(function()
{
	// Si bandeau pas masqu�
	if(get_cookie('cnilcookie') != "hide") 
	{
		// Ajout du bandeau en bas 
		$("body").append("<a href='' id='cnilcookie' class='bt fixed' style='transform: translateX(-50%);'><i class='fa fa-fw fa-bell'></i> "+ __("We use cookies to track site usage and improve it. <u>Hide</u>") +"</a>");	
		
		// Au click sur le bandeau
		$("#cnilcookie").click(function(){
			$(this).fadeOut();
			set_cookie("cnilcookie", "hide", "365");
			return false;
		});

		// Affichage du message apr�s un d�lai
		$("#cnilcookie").delay(2000).fadeIn("slow");
	}
});