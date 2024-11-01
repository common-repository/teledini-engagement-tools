try{ if(__c2a_no_compat__ || !Call2Action){throw ""};
 (function($){
	 teledini_aboutus = {};
	 teledini_social	= {};

	 if(teledini_options.aboutus != null) {

		 teledini_aboutus = $.parseJSON(JSON.stringify(eval( "({" + teledini_options.aboutus + "})" )));
	 }

	 if(teledini_options.social != null) {

		 teledini_social = $.parseJSON(JSON.stringify(eval( "({" + teledini_options.social + "})" )));
	 }

  Call2Action
  .js("https://www.teledini.com/et/teledini.js",function(){
	 return $("<div id=\"teledini-vertical\"></div>").prependTo("body").teledini({
	 routingTeam: teledini_options.routingTeamId,
	 teledini_id: teledini_options.orgId,
	 domain: "https://www.teledini.com",
	 version: "2",
	 services: teledini_options.services,
	 buttonPosition: teledini_options.orientation,
	 fontBackgroundColor: teledini_options.fontBackgroundColor,
	 fontColor: teledini_options.fontColor,
	 iconBackgroundColor: teledini_options.iconBackgroundColor,
	 iconShade: teledini_options.iconColor,
	 textClosedLine1: teledini_options.textClosedMain,
	 textClosedLine2: teledini_options.textClosedSub,
	 textOpenLine1: teledini_options.textOpenMain,
	 textOpenLine2: teledini_options.textOpenSub,
	 imageLogo: teledini_options.logo,
	 imagePage: teledini_options.imagePage,
	 customVars: [teledini_options.custom_vars],
	 about:teledini_aboutus,
	 social:teledini_social
	}).init();
   });
 }(tQuery))
} catch(e){}
