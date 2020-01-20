'use strict';

function save(){
    var resForm=$('form').serializeArray();
        $.ajax({
            type: 'POST',
            url: `${Bits.app}bit/save`,
            data: resForm
        })
            .done((data)=>{
                console.log(data);
            });
    }
const Bits = function () {
    let childForm;
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
    let tClasses = (p) => {
        console.log(p);
        $(p).toggleClass('show')
    };
    let Route = (p) => {
        let rEl = (elem, option) => Render().CallElement(elem, option);
        let $ui = $('#ui-view');
        $ui.fadeOut().promise()
            .done(function () {
                $ui.children().remove();
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header"})
                        .append(rEl('i', {class: "fa fa-edit"}), 'Form')
                        .append(rEl('div', {class: "card-header-actions"})
                            .append(rEl('a', {
                                class: "card-header-action btn-minimize collapsed",
                                "onclick":`Bits.tClasses('#cForm')`,
                                html: `<i class="icon-arrow-up"></i>`
                            })
                            )
                        ))
                    .append(rEl('div', {id: "cForm", class: "card-body collapse"})
                        .append(rEl('form', {
                        })
                            .append(rEl('div',{id:'parent',class:'row'}))
                            .append(rEl('div',{id:'child',class:'row'}))
                        ).append(rEl('a',{
                            id:'addfield',
                            text:'Add Field',
                            class: 'btn btn-primary',
                            onclick: "cloneTag(0)"
                        }))
                        .append(rEl('a',{
                            id:'save',
                            text:'Save',
                            class: 'btn btn-primary',
                            onclick: "save()"
                        }))
                    )
                );
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header"})
                        .append(rEl('i', {class: "fa fa-table"}), 'Data Table')
                        .append(rEl('div', {class: "card-header-actions"})))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('table', {id: `${p.substring(1)}-table`,class: 'table table-striped table-bordered datatable'}))))
            }).then(function () {
            $ui.fadeIn();
            /*init datatable*/
            $.get(`${app}bit/get/${window.location.hash.substring(1)}`)
                .done(function (data) {
                let f = data.form;
                    Bits.childForm = f.child;
                    $.each(f.parent,function (k,v) {
                        $('#parent').append(rEl('div',{
                            class: `form-group ${k !== 0 ? 'col-sm-4' : ''}`
                        }).append(rEl(v.input,{
                            id: v.id,
                            name: v.id,
                            type: v.type,
                            placeholder: v.label,
                            class: 'form-control',
                            url: v.url
                        })))
                    });
                    $.each(f.child,function (k,v) {
                        $('#child')
                            .append(rEl('div',{
                                class: `form-group ${k !== 0 ? 'col-sm-6' : ''}`
                            })
                                .append(rEl(v.input,{
                            id: v.id,
                            name: v.id,
                            type: v.type,
                            placeholder: v.label,
                            class: 'form-control',
                            url: v.url
                        })))
                    });
                let c = data.column;
                    c[0] = {"title": "No", data: null, name: null};
                    c[data.column.length] = {"title": "Action", data: null, name: null};
                const cd = [
                    {
                        targets: 0,
                        title: 'No.',
                        orderable: false,
                        visible: true,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        visible: true,
                        render: function (data, type, full, meta) {
                            return `<button id="${data.id}" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button>
                        <button id="delete" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>`;
                        },
                    }
                ];
                    $(p+'-table').DataTable({
                        dom: "Bfrtip",
                        responsive: true,
                        language: {
                            "emptyTable": "Data is Empty"
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
                        columnDefs: cd,
                        columns: c
                    });
            })
                .then(function () {
                    Render().CallSelect()
                });
        });
    };
    let Render = () => {
        let Element = (elem, option) => $("<" + elem + " />", option);
        let Select = () => {
            $('select').each(function(){
                let $t = $(this);
                $.get($t.attr('url')).done((data) => {
                    console.log(data);
                    data[0] = {
                        'id': '',"text":$t.attr('placeholder'),"title":$t.attr('placeholder')
                    };

                }).then((data)=>$t.select2({
                    data: data
                }));
            });

        };
        let Button = (p) => {
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
            CallForm: (p) => Form(p),
            CallSelect: () => Select()
        }
    };
    return {
        rButton: (p) => {
            Render().CallButton(p);
        },
        rElement: (elem, option) => {
            Render().CallElement(elem, option);
        },
        rSelect2: () =>
            Render().CallSelect()
        ,
        Route: (p) => {
            Route(p);
        },
        tClasses: (p) => {
            tClasses(p);
        },
        app,
        childForm
    }
}();

function cloneTag(index){
    index = index >= 0 ? index+1 :null;
    $('#addfield').attr('onclick',`cloneTag(${index})`);
    $('form').append($('<div />', {
        class: `form-${index} row`
    }));
    $.each(Bits.childForm, function (k,v) {
        Bits.childForm[k].id = (v.id).replace(/\d/g, index);
        $(`.form-${index}`)
            .append($('<div />', {
                class: `form-group ${k !== 0 ? 'col-sm-6' : ''}`
            }).append($(`<${v.input} />`, {
                    id: v.id,
                    name: v.id,
                    type: v.type,
                    placeholder: v.label,
                    class: 'form-control',
                    url: v.url
                }))
            )
    });
    Bits.rSelect2();
    $(`.form-${index}`)
        .append($('<div />', {class:"form-group col-sm-6"})
            .append($('<a />', {
                onclick: `removeParent(${index})`,
                class: 'btn btn-danger btn-block',
                text: 'remove',
            })));
}

function removeParent(p){
    $(`.form-${p}`).remove();
}
