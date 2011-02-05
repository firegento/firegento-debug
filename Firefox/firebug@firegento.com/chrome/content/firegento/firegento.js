FBL.ns(function() { with (FBL) {

var panelName = "FireGento";

/**
 * Localization helpers
 */
function $HW_STR(name)
{
	return;
    //return document.getElementById("strings_helloWorld").getString(name);
}

function $HW_STRF(name, args)
{
	return;
    //return document.getElementById("strings_helloWorld").getFormattedString(name, args);
}

/**
 * Model implementation.
 */
Firebug.FireGentoModel = extend(Firebug.Module,
{
    reattachContext: function(browser, context) 
    {
        var panel = context.getPanel(panelName);
        this.addStyleSheet(panel.document);
    },
	
    showPanel: function(browser, panel) 
    {
        var isHwPanel = panel && panel.name == panelName;
        var hwButtons = browser.chrome.$("fbFireGentoButtons");
        collapse(hwButtons, !isHwPanel);
    },
    
    onMyButton: function(context) 
    {
        var panel = context.getPanel(panelName);        
        var args = {
            date: (new Date()).toGMTString(),
        };
        FireGentoRep.myTag.append(args, panel.panelNode, FireGentoRep);
    },
    
    addStyleSheet: function(doc)
    {
        // Make sure the stylesheet isn't appended twice. 
        if ($("hwStyles", doc))
            return;
        
        var styleSheet = createStyleSheet(doc, 
            "chrome://firegento/skin/firegento.css");
        styleSheet.setAttribute("id", "hwStyles");
	      addStyleSheet(doc, styleSheet);
    }    
});


/**
 * Panel implementation 
 */	
function FireGentoPanel() {}
FireGentoPanel.prototype = extend(Firebug.Panel,
{
    name: "FireGento",
    title: "FireGento",

    initialize: function() 
    {
      Firebug.Panel.initialize.apply(this, arguments);
      Firebug.FireGentoModel.addStyleSheet(this.document);
      
      /*
      searchResultRep.loadingTag.replace({}, this.panelNode, null);

      var searchUrl = "http://search.yahooapis.com/SiteExplorerService/V1/inlinkData?" + 
          "appid=YahooDemo&output=json&query=" + this.context.window.location.host;

      var panel = this;
      var request = new XMLHttpRequest();
      request.onreadystatechange = function() {
          if (request.readyState == 4 && request.status == 200) {
              var data = eval("(" + request.responseText + ")");
              searchResultRep.resultTag.replace(data, panel.panelNode, searchResultRep);
          }
      }

      request.open("GET", searchUrl, true);
      request.send(null);*/      
    },
});

var FireGentoRep = domplate(
		{
		    myTag:
		        DIV({class: "MyDiv"},
		            "Hello World!"
		        )
		});

/**
 * YAHOO! Search link API Domplate
 */
var searchResultRep = domplate(
{
    resultTag:
        TABLE({class: "searchResultSet", cellpadding: 0, cellspacing: 0},
            TBODY(
                FOR("result", "$ResultSet.Result|resultIterator",
                    TR({class: "searchResult"},
                        TD({class: "searchResultTitle"}, 
                            "$result.Title"
                        ),
                        TD({class: "searchResultUrl", onclick: "$onClickResult"}, 
                            "$result.Url"
                        )
                    )
                )
            )
        ),

    loadingTag:
        DIV({class: "searchResultLoading"},
            IMG({src: "chrome://firegento/skin/ajax-loader.gif"})
        ),

    onClickResult: function(event)
    {
        openNewTab(event.target.innerHTML);
    },

    resultIterator: function(result)
    {
        if (!result)
            return [];

        if (result instanceof Array)
            return result;

        return [result];
    }
});


/**
 * Registration
 */
Firebug.registerModule(Firebug.FireGentoModel);
Firebug.registerPanel(FireGentoPanel);

}});