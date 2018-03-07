/* CDC request manager */
/*** SCHEMA DI COMUNICAZIONE
 CDC => REST {
    action : "ACTION"       //OBBLICATORIO
    page : "PAGE"           //OBBLIGATORIO PER ALCUNE ACTION
    data : {
      ...                   //DATI DA INVIARE AL SERVER REST
    }
  }

  REST => CDC {
    html : ""               //SE PRESENTE, il CONTENUTO VA A SOSTITUIRE L'HTML PRINCIPALE (#main)
    script : ""             //SE È PRESENTE, VIENE INIETTATO DEL JS
    error: num              //SI È VERIFICATO UN ERRORE
    data :{
      ...                   //SEÈ È STATO RICHIESTO, VENGONO RESTITUITI DEI DATI STRUTTURATI
    }
  }
***/
class cdc{
  // INIT METHODS
  constructor(){
    this.settings();
    this.framework();
    this.boot();
  }
  framework(){
    var cdcthis = this;
    $(".cdcToolbarOption").filter("[data-cdc-toolbar]").each(function(){
      var elemName = $(this).attr("data-cdc-toolbar");
      var idbase = $(this).closest(".cdcToolbar").attr("id");
      var completeid = idbase + elemName.replace(/\W/g, '');
      $(this).attr('id', completeid);
      $(this).html('<i class="fas fa-2x fa-'+ elemName +'"></i>')
      $(this).append('<div class="cdcToolbarOptionMenu"></div>');
      $(this).removeAttr('data-cdc-toolbar');
      cdcthis.toolbarButtons[elemName] = '#' + completeid;
    })
  }
  settings(){
    this.target = "rest"; //set target rest page
    this.method = "POST";
    this.mainId = "#main";
    this.splashScreen = "#splashScreen";
    this.splashScreenMain = "#splashScreenMain";
    this.modal = "#messageModal";
    this.toolbarButtons = {}
  }
  triggers(){
    var cdcthis = this;
    $(".cdc-action").unbind();
    $(".cdc-action").click(function(e){
      if( typeof $(this).attr("data-cdc-action") !== 'undefined' ){
        e.preventDefault();
        var sendOptions = { action : $(this).attr("data-cdc-action") }

        if( typeof $(this).attr("data-cdc-page") === 'string' ){ sendOptions['page'] = $(this).attr("data-cdc-page"); }
        if( typeof $(this).closest("form").serializeObject() === 'object' ){ sendOptions['data'] = $(this).closest("form").serializeObject(); }

        if( $(this).attr("data-cdc-enablefades") === 'true'
          || $(this).attr("data-cdc-enablefades") === 'false' ){
            sendOptions['enableFades'] = $(this).attr("data-cdc-enablefades") === 'true'?true:false;
        }
        if( $(this).attr("data-cdc-defaulterror") === 'true'
          || $(this).attr("data-cdc-defaulterror") === 'false' ){
            sendOptions['endableDefaultError'] = $(this).attr("data-cdc-defaulterror") === 'true'?true:false;
        }

        cdcthis.send(sendOptions);
      }
    });
  }
  //REST INTERFACE METHODS (LOW LEVEL)
  sendData(action, page, data, handler){
    data = {
      action: action,
      page: page,
      data: data
    };
    $.ajax({
      url: this.target,
      method: this.method,
      data: data,
      success: handler,
      error: handler
    });
  }
  boot(){
    var splashScreen = this.splashScreen;
    this.send({
      action: "RENDER",
      page: "BOOT",
      handler: function(){
        $(splashScreen).fadeOut(400, function(){});
      },
      enableFades: false,
      endableDefaultError: false
    });
  }
  reboot(){
    this.send({
      action: "RENDER",
      page: "BOOT"
    });
  }
  //GRAPHICAL RENDER AND HTML CODE
  addScript( script ){
    var scriptTag = document.createElement("script");
    scriptTag.innerHTML = script;
    document.head.appendChild(scriptTag);
  }
  render(html){ $(this.mainId).html(html); this.triggers(); }
  flushRender(){ this.render("") }
  fadeInLoading( handler ){ $(this.splashScreenMain).fadeIn(350, handler); }
  fadeOutLoading( handler ){ $(this.splashScreenMain).fadeOut(450, handler); }

  //toolbar commands
  addToolbarVoice(toolbaroptionid, voice, action, page){
    var voiceHtml = voice
    var voiceHtml = '<li class="cdc-action" data-cdc-action="' + action + '" data-cdc-page="' + page + '" data-cdc-voice="' + voice + '" >' + voiceHtml + '</li>';
    if($(this.toolbarButtons[toolbaroptionid]).find(".cdcToolbarOptionMenu ul").length === 0){
      $(this.toolbarButtons[toolbaroptionid]).find(".cdcToolbarOptionMenu").html("<ul></ul>");
    }
    $(this.toolbarButtons[toolbaroptionid]).find(".cdcToolbarOptionMenu ul").append(voiceHtml);
    this.triggers();
  }
  removeToolbarVoice(toolbaroptionid, voice){
    $(this.toolbarButtons[toolbaroptionid]).find("li[data-cdc-voice='" + voice + "']").remove();
    if($(this.toolbarButtons[toolbaroptionid]).find('ul').is(':empty')){
      $(this.toolbarButtons[toolbaroptionid]).find('ul').remove();
    }
    this.triggers();
  }
  resetToolbar(){
    var cdcthis = this;
    $.each(CDC.toolbarButtons,function(){
      $(this).find(".cdcToolbarOptionMenu ul").remove();
    });
    this.triggers();
  }
  /* parameter in options

    page (mandatory for some action): page to render
    action (mandatory): action to execute
    data: data to be send
    handler: handler executed before the render
    endableDefaultError (default: true): on error, execute cdc.defaultError(status)
    enableFades (default: true): execute cdc.fadeInLoading before loading and cdc.fadeOutLoading after Loading
    */
  send( options ){
    //FILTERING request
    if (typeof options.endableDefaultError !== 'boolean') { options.endableDefaultError = true; }
    if (typeof options.enableFades !== 'boolean') { options.enableFades = true; }
    if (typeof options.data !== 'object') { options.data = {} ; }
    if (typeof options.handler !== 'function') { options.handler = function(){}; }
    if (typeof options.page !== 'string') { options.page = ""; }
    if (typeof options.action !== 'string') { return false; }

    var cdcthis = this;

    var operation = function(){
      cdcthis.sendData(options.action, options.page, options.data,
        function(content, status){
          content = JSON.parse(content);
          lastCdcRequest = content;
          options.handler(content, status);
          if( status === "success" && typeof content.error === 'undefined' ){
            if(typeof content.html === 'string') { cdcthis.render( content.html ); }
            if(typeof content.script === 'string') { cdcthis.appendScript( content.script ); }
            if (options.enableFades){ cdcthis.fadeOutLoading(); }
          }
          else if(options.endableDefaultError){
            if ( typeof content.error !== 'undefined' ){
              cdcthis.defaultError("Errore " + content.error + "<br/>" + content.errorString );
            }else if ( status === "success" ) {
              cdcthis.defaultError("Errore nell'elaborazione della richiesta");
            }else{
              cdcthis.defaultError("Errore durante la connessione");
            }
            if (options.enableFades){ cdcthis.fadeOutLoading(); }
          }
        });
    };

    if(options.enableFades){ cdcthis.fadeInLoading(operation); }
    else { operation(); }
  }

  //DEFAULT ERROR (DEBUG)
  defaultError( code ){
    $(this.modal).find(".modal-title").html("Errore");
    $(this.modal).find(".modal-body").html( code );
    $(this.modal).modal();
  }
}

function cdcJQueryPlugin(){/* jQuery Plugin */
  $.fn.serializeObject  = function() {
  	var o = {};
  	var a = this.serializeArray();
  	$.each(a, function() {
  		if (o[this.name]) {
  			if (!o[this.name].push) {
  				o[this.name] = [o[this.name]];
  			}
  			o[this.name].push(this.value || '');
  		} else {
  			o[this.name] = this.value || '';
  		}
  	});
  	return o;
  };
}
window.onload = function(){cdcJQueryPlugin();CDC=new cdc();}
var lastCdcRequest;
