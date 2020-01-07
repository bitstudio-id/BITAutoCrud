'use strict';

const Bits = function () {
    let app = 'http://autocrud.test/';
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
                        .append(rEl('i', {class: "fa fa-edit"}), "Test")
                        .append(rEl('div', {class: "card-header-actions"})
                            .append(rEl('button', {class: "btn btn-primary btn-sm",text: "Add Data",action: Render().CallForm(window.location.hash)})
                        )
                    ))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('table', {id: `${p.substring(1)}-table`}))))
            }).then(function () {
            $ui.fadeIn();
            /*init datatable*/
            $.get(`${app}crud/${window.location.hash.substring(1)}`)
                .done(function (data) {
                const c = data.column;
                const cd = [
                    {
                        targets: 0,
                        title: 'No.',
                        orderable: true,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: true,
                        render: function (data, type, full, meta) {
                            return `<button id="edit" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button>
                        <button id="delete" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>`;
                        },
                    }
                ];
                    $(p+'-table').DataTable({
                        dom: "Bfrtip",
                        responsive: true,
                        language: {
                            "emptyTable": "My Custom Message On Empty Table"
                        },
                        fixedHeader: true,
                        keys: true,
                        dataType: "json",
                        data: data.data,
                        stateSave: true,
                        pagingType: "full_numbers",
                        pageLength: 8,
                        lengthMenu: [[5, 8, 15, 20], [5, 8, 15, 20]],
                        autoWidth: true,
                        orderable: true,
                        columns: c,
                        columnDefs: cd
                    });
            });
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
        let Form = (p) => {
            console.log(p);
        };
        return {
            CallButton: (p) => Button(p),
            CallElement: (elem, option) => Element(elem, option),
            CallDataTable: (p) => DataTable(p),
            CallForm: (p) => Form(p)
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



