'use strict';
var Bit = function() {
    let Render = () => {
        let Button = (p) => {
            $('#'+p.target).append($("<button />", {
                id : p.id,
                class : p.class,
                type : p.type,
                text : p.label
            }));
        };
        return {
            CallButton: (p) => Button(p)
        }
    };
    return {
        rButton: (p) => {
            Render().CallButton(p);
        }
    }
}();



