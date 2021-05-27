function get_eAddns_ElementSettings($element) {
    var elementSettings = [];
    var modelCID = $element.data('model-cid');
    if (elementorFrontend.isEditMode() && modelCID) {
        var settings = elementorFrontend.config.elements.data[modelCID];
        var type = settings.attributes.widgetType || settings.attributes.elType;
        var settingsKeys = elementorFrontend.config.elements.keys[ type ];
        if (!settingsKeys) {
            settingsKeys = elementorFrontend.config.elements.keys[type] = [];
            jQuery.each(settings.controls, function (name, control) {
                if (control.frontend_available) {
                    settingsKeys.push(name);
                }
            });
        }
        jQuery.each(settings.getActiveControls(), function (controlKey) {
            if (-1 !== settingsKeys.indexOf(controlKey)) {
                elementSettings[ controlKey ] = settings.attributes[ controlKey ];
            }
        });
    } else {
        elementSettings = $element.data('settings') || {};
    }
    return elementSettings;
}
/*
 // Questo è un APPUNTO per mappare una classe specifica......
 var elemToObserve = document.getElementById('your_elem_id');
 var prevClassState = elemToObserve.classList.contains('your_class');
 var observer = new MutationObserver(function(mutations) {
 mutations.forEach(function(mutation) {
 if(mutation.attributeName == "class"){
 var currentClassState = mutation.target.classList.contains('your_class');
 if(prevClassState !== currentClassState)    {
 prevClassState = currentClassState;
 if(currentClassState)
 console.log("class added!");
 else
 console.log("class removed!");
 }
 }
 });
 });
 observer.observe(elemToObserve, {attributes: true});
 */
function observe_eAddns_element($target, $function_callback) {
    if (elementorFrontend.isEditMode()) {
        // Seleziona il nodo di cui monitare la mutazione
        var elemToObserve = $target;
        //var prevClassState = elemToObserve.classList.contains('e-add-col-');

        /*
         // NOTA: le proprietà di observe
         mutationObserver.observe(document.documentElement, {
         attributes: true,
         characterData: true,
         childList: true,
         subtree: true,
         attributeOldValue: true,
         characterDataOldValue: true
         });*/

        // Opzioni per il monitoraggio (quali mutazioni monitorare)
        var config = {
            attributes: true,
            childList: false,
            characterData: true
        };

        var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
        // Creazione di un'istanza di monitoraggio collegata alla funzione di callback
        var observer = new MutationObserver($function_callback);

        // Inizio del monitoraggio del nodo target riguardo le mutazioni configurate
        observer.observe(elemToObserve, config);

        // Successivamente si può interrompere il monitoraggio
        //observer.disconnect();
    }

}