'use strict';

function save(p){
    var resForm=$('form').serializeArray();

        $.ajax({
            type: 'POST',
            url: p,
            data: resForm
        })
            .done((data)=>{
                console.log(data);
            });
    }
const Bits = function () {
    let childForm;
    let app = 'http://autocrud.test/';
    let tClasses = (p) => {
        $(p).toggleClass('show')
    };
    let Route = (p) => {
        let rEl = (elem, option) => Render().CallElement(elem, option);
        let $ui = $('#ui-view');
        $ui.fadeOut().promise()
            .done(function () {
                $ui.children().remove();
                if (p!=='#bitmenu') {
                    $ui.append(rEl('div', {class: "card"})
                        .append(rEl('div', {class: "card-header bg-primary text-white"})
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
                            ).append(rEl('div', {class: "card-footer"})
                                .append(rEl('button',{
                                    id:'addfield',
                                    text:'Add Field',
                                    class: 'btn btn-info pull-left',
                                    onclick: "cloneTag(0)"
                                }))
                                .append(rEl('button',{
                                    id:'save',
                                    text:'Save',
                                    class: 'btn btn-primary pull-right',
                                    onclick: `save('${Bits.app}bit/save')`
                                }))
                            )

                        ));
                }
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header bg-primary text-white"})
                        .append(rEl('i', {class: "fa fa-table"}), 'Data Table')
                        .append(rEl('div', {class: "card-header-actions"})))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('table', {id: `${p.substring(1)}-table`,class: 'table table-striped table-bordered datatable'}))))
            }).then(function () {
            $ui.fadeIn();
            /*init datatable*/
            $.get(`${app}bit/get/${window.location.hash.substring(1)}`)
                .done(function (data) {
                    let param = {
                        id: p,
                        c:data.column,
                        url:`${app}bit/datatable/${window.location.hash.substring(1)}`,
                    };
                let f = data.form;
                    Bits.childForm = f.child;
                    if (p==='#bitform'){
                        $.each(f,function (k,v) {
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
                    }else {
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
                    }

                    if (p!=='#bitmenu') {
                        Render().CallDataTable(param)
                    }
                    if (p==='#bitmenu'){
                        $.get('/bit/bitMenuGet')
                    .done((data)=>{
                        $('.card-body').append(rEl('form', {class: "row"})

                        ).append(rEl('button', {class: "btn btn-primary",text:"Submit", onclick:`save('${Bits.app}bit/menusave')`}));
                            if (data!='') {
                                $.each(data,(k,v)=>{
                                    $('form')
                                        .append(rEl('div', {
                                            class: "form-group col-3"
                                        }).append(rEl('label', {
                                            text:'Menu Parent',
                                        }))
                                            .append(rEl('select', {
                                            id: `s${v.bitmenu_id}`,
                                            class: "form-control zz",
                                            name: `field[${v.bitmenu_id}][bitmenu_parent_id]`,
                                        }).append(rEl('option', {
                                                value: '',
                                                text: 'Choose Parent'
                                        }
                                            ))))
                                        .append(rEl('div', {
                                            class: "form-group col-3"
                                        }).append(rEl('label', {
                                            text:'Menu Name',
                                        }))
                                            .append(rEl('input', {
                                            class: "form-control",
                                            value:v.bittable_name,
                                            disabled: true
                                        })))
                                        .append(rEl('div', {
                                            class: "form-group col-3"
                                        }).append(rEl('label', {
                                            text:'Index',
                                        }))
                                            .append(rEl('input', {
                                        class: "form-control",
                                        placeholder:v.bitmenu_index,
                                        type:'number',
                                        name: `field[${v.bitmenu_id}][bitmenu_index]`,
                                    })))
                                        .append(rEl('div', {
                                            class: "form-group col-3"
                                        }).append(rEl('label', {
                                            text:'Icon',
                                        }))
                                        .append(rEl('input', {
                                        class: "form-control",
                                        name: `field[${v.bitmenu_id}][bitmenu_icon]`,
                                        value: v.bitmenu_icon
                                    })))
                                })
                                $.each(data,(k,v)=>{
                                    $('.zz').each(function(){
                                        if ($(this).attr('id')!= `s${v.bitmenu_id}`){
                                            $(this).append(rEl('option', {
                                                value: v.bitmenu_id,
                                                text: v.bittable_name,
                                            }))
                                        }
                                    })
                                })
                            }
                        });
                    }

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
                        return `<button id="${Object.values(data)[0]}" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button>
                        <button id="delete" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>`;
                    },
                }
            ];
            $(p.id+'-table').DataTable({
                dom: "Bfrtip",
                responsive: true,
                language: {
                    "emptyTable": "Data is Empty"
                },
                fixedHeader: true,
                keys: true,
                ajax: {
                    url: p.url,
                    dataSrc: 'data'
                },
                dataType: "json",
                stateSave: true,
                pagingType: "full_numbers",
                pageLength: 8,
                lengthMenu: [[5, 8, 15, 20], [5, 8, 15, 20]],
                autoWidth: true,
                orderable: true,
                columnDefs: cd,
                columns: p.c
            });
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
