// Plugin pour ajouter des vid�o Youtube en background

// Ajouter data-youtube="https://youtu.be/id_de_votre_video" dans un div et le tour est jou�

// Appel de l'api youtube
var tag = document.createElement("script");
tag.src = "https://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName("script")[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


// Fonction de l'api youtube pour instancier les vid�os
function onYouTubePlayerAPIReady(){
	
	// Boucle sur tous les blocs qui r�clame une vid�o en Bg
	$("[data-youtube]").each(function(i)
	{
		var contener = this;
		var addsize = 0;//100

		// R�cup�re l'id du bloc qui doit contenir une vid�o ou en cr�e un
		if(!$(contener).attr("id")) uid_bg_video = $(contener).attr("id", "youtube-" + i);
		else uid_bg_video = $(contener).attr("id");
	
		// Ajout une class pour identifier les contenus au dessus de la vid�o
		$(contener).children().addClass(uid_bg_video + "-children");

		// Place les �l�ments du contenant au-dessus de la div qui contient la vid�o
		 $(contener).css("position","relative");
		 $(contener).wrapInner("<div class='over-video' style='position: absolute; z-index: 2; width: 100%; height: 100%; overflow: hidden;'></div>");

		// R�cup�ration de l'id de la vid�o
		var match = $(contener).attr("data-youtube").match(/^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
		id_video = (match && match[2].length == 11) ? match[2] : false;
			
		// Cr�ation d'une div pour la metre en bg
		 $(contener).append("<div id='video-"+uid_bg_video+"' style='position: absolute; top: 0; left: 0; z-index: 1; opacity: 0; transition: opacity .5s;'></div>");


		// Injection de la vid�o hd1080 hd720
		player = new YT.Player("video-"+uid_bg_video, {
			events: {
				"onReady": function(){
					player.loadVideoById({"videoId": id_video, "suggestedQuality": "hd720"});
					player.mute();
				},
				"onStateChange": function(event){
					//console.log(event.data);

					// Vid�o charg�e
					if(event.data === 1)
					{						
						// On �tire un peut la vid�o pour cacher le logo Youtube
							// Dimension du conteneur sup�rieur
							var h = $(contener).height() + addsize;
							var w = $(contener).width() + addsize;
							
							// Taille du player
							//player.setSize(h*16/9, h);
							player.setSize(w, w/(16/9));
							
							// Position du player
							$("#video-"+uid_bg_video).css({
								//"top": - addsize / 2,
								"top": - (( (w/(16/9)) / 2) - h),
								"left": ($(contener).width() - $("#video-"+uid_bg_video).width()) / 2
							});
						
						// On l'affiche progressivement la vid�o
						$("#video-"+uid_bg_video).css("opacity","1");						
					}

					// Vid�o termin�e : on la relance (loop maison)
					if(event.data === YT.PlayerState.ENDED) player.playVideo(id_video);
				}
			},
			playerVars: {
				autoplay: 0,
				autohide: 1,
				modestbranding: 1,
				rel: 0,
				showinfo: 0,
				controls: 0,
				disablekb: 1,
				enablejsapi: 0,
				iv_load_policy: 3}
		});


		// Si on redimensionne la fen�tre (responsive)
		$(window).on("resize", function(){
			// Position du player
			$("#video-"+uid_bg_video).css({
				"left": ($(contener).width() - $("#video-"+uid_bg_video).width()) / 2
			});
		});


		// D�sactive les vid�os lors du mode �dition
		edit.push(function() {
			$("#video-"+uid_bg_video).remove();
			$("."+uid_bg_video+"-children").unwrap();
		});
	});	
}