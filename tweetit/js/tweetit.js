(function() {
    tinymce.create("tinymce.plugins.tweetit_button_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button     
            ed.addButton("green", {
                title : "Tweet It",
                cmd : "green_command",
                image : url + "/tweetit.png"
            });

            //button functionality.
            ed.addCommand("green_command", function() {
                var selected_text = ed.selection.getContent();
                var return_text = "[tweetit]" + selected_text + "[/tweetit]";
                ed.execCommand("mceInsertContent", 0, return_text);
            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Extra Buttons",
                author : "Narayan Prusty",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("tweetit_button_plugin", tinymce.plugins.tweetit_button_plugin);
})();