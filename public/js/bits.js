'use strict';

const Bits = function () {
    let kontol = this;
    let Data = (p) => {
        let result;
        switch (p) {
            case '#database':
                result = {
                    table: [
                        {
                            id: "bittable_id",
                            type: "hidden"
                        }, {
                            id: "bittable_name",
                            type: "text"
                        }, {
                            id: "bittable_type",
                            type: "text",
                            value: "table"
                        }
                    ],
                    field: [
                        {
                            id: "bittable_parent_id",
                            type: "hidden",
                        }, {
                            id: "bittable_name",
                            type: "hidden",
                        }, {
                            id: "bittable_type",
                            type: "select",
                        },
                    ]
                };
                break;
            case 'menu':
                // code block
                break;
            default:
                alert('error with your code')
        }
        return result;
    };
    let Route = (p) => {
        let rEl = (elem, option) => Render().CallElement(elem, option);
        let $ui = $('#ui-view');
        $ui.fadeOut().promise()
            .done(function () {
                $ui.children().remove();
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header"})
                        .append(rEl('i', {class: "fa fa-edit"}), "Test"))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('div', {class: "table"}))))
            }).then(function () {
            $ui.fadeIn();
            $('.table').DataTable();
        });
    };
    let Render = () => {
        let Element = (elem, option) => $("<" + elem + " />", option);
        let Button = (p) => {
            console.log('its working');
            $('#' + p.target).append($("<button />", {
                id: p.id,
                class: p.class,
                type: p.type,
                text: p.text
            }));
        };
        let DataTable = (p) => {
          console.log('trigger datatable')
        };
        return {
            CallButton: (p) => Button(p),
            CallElement: (elem, option) => Element(elem, option),
            CallDataTable: (p) => DataTable(p)
        }
    };
    return {
        rButton: (p) => {
            Render().CallButton(p);
        },
        rElement: (elem, option) => {
            Render().CallElement(elem, option);
        },
        Route: (p) => {
            Route(p);
        },
    }
}();



