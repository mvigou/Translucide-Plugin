<?
/* Plugin pour ajouter la possibilit� de cr�er des blocs d'information dynamiquement (portfolio...) */

if(!$work) $work = "work";

// Extrait les donn�es work du tableau des contenu
$keys = array_keys($GLOBALS['content']);
foreach($keys as $key)
{
	if(preg_match("/".$work."-/", $key) == 1)
	{
		$type_num_work = str_replace($work."-", "", $key);

		$exp_key = explode("-", $type_num_work);
		
		$num_work = $exp_key[(count($exp_key)-1)];
		$type_work = rtrim($type_num_work, "-".$num_work);

		$array_work[$work][$num_work][$type_work] = $GLOBALS['content'][$key];
	}
}

// Bloc vide pour l'ajout de nouveau �l�ment
$array_work[$work][0]['titre'] = "";

// Re-init le tableau
reset($array_work[$work]);

?>
<div class="">
	<ul id="<?=$work?>" class="work unstyled">
	<?
	// Affichage des �l�ments existant
	while(list($key, $val) = each($array_work[$work]))
	{ 
		echo"
		<li class='w150p mtl tc small animation slide-up'>

			<a href=\"".$array_work[$work][$key]['link']."\" title=\"".$array_work[$work][$key]['tooltip']."\" class='block' target='_blank'>

				<h2 class='h4-like w100 mod mtn'><span class='editable' id='".$work."-titre-".(int)$key."'>".$array_work[$work][$key]['titre']."</span></h2>

				<div class='w150p animation' data-lazy='bg' data-id='".$work."-bg-".(int)$key."' data-bg='".$array_work[$work][$key]['bg']."'></div>

				<div class='w150p none'><span class='editable-media'><img src=\"".$array_work[$work][$key]['img']."\" width='150' id='".$work."-img-".(int)$key."'></span></div>
				
				<div class='absolute'>
					<input type='hidden' id='".$work."-tooltip-".(int)$key."' value=\"".$array_work[$work][$key]['tooltip']."\" class='editable-hidden tooltip w50'><input type='hidden' id='".$work."-link-".(int)$key."' value=\"".$array_work[$work][$key]['link']."\" class='editable-hidden link w50'>
				</div>

			</a>

		</li>";
	}
	?>
	</ul>
</div>


<?if(!$jswork) {?>
<script>
	add_translation({
		"Add a block" : {"fr" : "Ajouter un bloc"},
		"Move" : {"fr" : "D\u00e9placer"},
		"Remove" : {"fr" : "Supprimer"}
	});

	// Fonction d'ajout de bloc
	add_work = function(event)
	{
		work = $(event).parent().prev("ul").attr("id");

		// Cr�e un id unique => bug prend juste le dernier qui n'est pas forcement le + grand
		//key = parseInt($("#" + work + " li:first-child .editable").attr("id").split("-").pop()) + 1;

		// Prend l'id le plus elever
		var max = $.map($(".work .editable"), function(elem) {
			return parseInt(elem.id.match(/\d+/));
		}).sort(function(a, b) {
			return(b-a); /* reverse sort */
		});
		key = max[0] + 1;

		// Unbind les events d'edition
		$(".editable").off();
		$(".editable-media").off(".editable-media");
		//$(".editable-bg").off();

		// Cr�e un block
		$("#" + work + " li:last-child").clone().prependTo("#" + work).show("400", function()
		{
			// Modifie les cles txt et img
			$("[class*='editable']", this).each(function() {			
				
				if($(this).hasClass("editable-media")) // Image
				{
					old_key = $("[id*='" + work + "-']", this).attr("id");

					$("#" + old_key).attr({
						id: old_key.replace("-0", "-"+ key),
						src: ""
					});
				}
				else if($(this).hasClass("editable-bg"))// Bg
				{
					$(this).attr("data-id", $(this).attr("data-id").replace("-0", "-"+ key));
				}
				else// Autre (txt, hidden...)
				{
					new_key = $(this).attr("id").replace("-0", "-"+ key);

					$(this).attr({
						id: new_key,
						placeholder: new_key,
						title: new_key
					});
				}
			});

			// Relance les events d'edition
			editable_event();
			editable_file_event();
			editable_bg_event();
		});
	}


	// Rends d�pla�ables les blocs
	move_work = function() {

		// Change le style du bouton et l'action
		$(".work-bt .fa-arrows").css("transform","scale(.5)");

		// D�sactive l'edition
		$(".editable-media").off(".editable-media");
		$(".editable").off();

		// Change l'action sur le lien 'move'
		$(".work-bt [href='javascript:move_work();']").attr("href","javascript:unmove_work();");

		// Les rend d�pla�able
		$(".work").sortable();
	}

	// D�sactive le d�placement des blocs
	unmove_work = function() {

		// Change le style du bouton et l'action
		$(".work-bt .fa-arrows").css("transform","scale(1)");

		// Change l'action sur le lien 'move'
		$(".work-bt [href='javascript:unmove_work();']").attr("href","javascript:move_work();");

		// Active l'edition
		editable_event();
		editable_file_event();
		editable_bg_event();

		// D�sactive le d�placement
		$(".work").sortable("destroy");
	}


	$(document).ready(function()
	{		
		// Masque le bloc duplicable vide de d�faut
		$(".work li:last-child").hide();
		
		// Action si on lance le mode d'edition
		edit.push(function() {

			// D�sactive le lien sur le bloc
			$(".work li a").attr("href", "javascript:void(0)").css("cursor","default");

			// D�sactive les animations pour rendre plus fluide les d�placements et l'edition
			$(".work .fire").css({
				"opacity": "1",
				"transform": "translate3d(0, 0, 0)"
			});
			$(".work .animation").removeClass("animation fire");

			// Ajoute le bouton pour dupliquer le bloc vide de d�faut
			$(".work").after("<div class='work-bt'><a href='javascript:move_work();'><i class='fa fa-fw fa-arrows'></i> "+__("Move")+"</a> <a href='javascript:void(0)' onclick='add_work(this)'><i class='fa fa-fw fa-plus-square-o'></i> "+__("Add a block")+"</a></div>");
			
			// Force le parent en relatif pour bien positionner les boutons d'ajout
			$(".work-bt").parent().addClass("relative");

			// Ajout de la suppresion au survole d'un bloc
			$(".work li").append("<a href='javascript:void(0)' onclick='remove_work(this)'><i class='fa fa-close absolute none' style='top: -5px; right: -5px;' title='"+ __("Remove") +"'></i></a>");

			// Affiche les boutons de suppression
			$(".work li .fa-close").fadeIn();

			// Fonction pour supprimer un bloc
			remove_work = function(that) {
				$(that).closest("li").fadeOut("slow", function() {
					this.remove();
				});
			};
		});
	});
</script>
<?
}

// Pour ne pas instanti� 2 fois le js
$jswork = true;
?>