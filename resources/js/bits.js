'use strict';

const Bits = function () {
    let Render = () => {
        let Button = (p) => {
            console.log('its working');
            $('#' + p.target).append($("<button />", {
                id: p.id,
                class: p.class,
                type: p.type,
                text: p.text
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



