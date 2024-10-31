(function() {  
    tinymce.create('tinymce.plugins.rsmember_editor_button', {  
        init : function(ed, url) {  
            ed.addButton('rsmember_editor_button', {  
                title : 'Restrict Post & Page content',  
                image: url + "/C_restric.png", 
                onclick : function() {  
                     ed.selection.setContent('[rsmembers-contentrestriction] [/rsmembers-contentrestriction]');
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('rsmember_editor_button', tinymce.plugins.rsmember_editor_button);  
})();