'use strict';

const capitalize = (s) => {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
};

$(window).bind('hashchange', function () {
    const arr= ['#bitform','#bitmenu','#bittable','#bitquery'];
    $.inArray(window.location.hash,arr) !== -1 ? Bits.Route(window.location.hash) : Bits.Crud(window.location.hash);
});
function execute() {
    let rEl = (elem, option) => $("<" + elem + " />", option);
    var $form = $('form').serializeArray();
    $.ajax({
        type: 'get',
        url: '/bit/query',
        data: $form
    })
        .done((data)=>{
            $('table').append(rEl('thead').append(rEl('tr')))
                .append(rEl('tbody'));
            $.each(data.th,function (k,v) {
                $('table>thead>tr').append(rEl('td',{text:v}))
            });
            $.each(data.data,function (k,v) {
                $('table>tbody').append(rEl('tr',{class: `tr-${k}`}));
                $.each(v,function (kk,vv) {
                    $(`.tr-${k}`).append(rEl('td',{text:vv}))
                })
            })
        });

};
function loadMenu(){
    const $this = $('#bit-menu');
    $this.children().remove();
    $.get('/bit/bitMenuGet')
        .done((data)=>
            $.each(data,(k,v) =>
                $this.append(`<li class="nav-item">
                            <a class="nav-link" href="#${v.bittable_name}">
                                <i class="nav-icon ${v.bitmenu_icon}"></i> ${capitalize(v.bittable_name)}
                            </a>
                        </li>`)));
}
function dd(p,table=null){
    if (table === null)
    {
        $.get('/bit/delete/'+p).done(Bits.Route(window.location.hash))
    }else{
        $.get(`/crud/delete/${table}/${p}`).done(Bits.Crud(window.location.hash))
    }
}
function save(p){
    var resForm=$('form').serializeArray();
        $.ajax({
            type: 'POST',
            url: p,
            data: resForm
        })
            .done((data)=>{
                Bits.Route(window.location.hash);
            });
    }
function clearForm(){
    $('form').find("input, textarea").val("");
    $('select').val("").trigger('change');
}
const Bits = function () {
    let childForm;
    let joinForm;
    let genForm;
    let app = '/';
    let tClasses = (p) => {
        $(p).toggleClass('show')
    };
    let Crud = (p) => {
        let rEl = (elem, option) => Render().CallElement(elem, option);
        let $ui = $('#ui-view');
        $ui.fadeOut().promise()
            .done(function () {
                $ui.empty();
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header bg-primary text-white"})
                        .append(rEl('i', {class: "fa fa-table"}), 'Data Table')
                        .append(rEl('div', {class: "card-header-actions"})))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('table', {id: `${p.substring(1)}-table`,class: 'table table-striped table-bordered'}))))
            }).then(function () {
            $ui.fadeIn();
            $ui.prepend(rEl('div',{class:"card"})
                .append(rEl('div',{class: "card-header bg-primary text-white"})
                    .append(rEl('i', {class: "fa fa-edit"}), 'Form')
                    .append(rEl('div', {class: "card-header-actions"})))
                .append(rEl('div', {class: "card-body"})
                    .append(rEl('form', {class: 'form row'}))
                    .append(rEl('button', {class: 'btn btn-primary',text:'Clear',onclick:`clearForm()`}))
                    .append(rEl('button', {class: 'btn btn-primary',text:'Save',onclick:`crudSave("${p.substring(1)}")`}))))
            $.get(`${app}crud/get/${window.location.hash.substring(1)}`)
                .done((data) => {
                    let param = {
                        id: p,
                        c:data.column,
                        url:`${app}crud/datatable/${window.location.hash.substring(1)}`,
                    };
                    $.each(data.form,(k,v)=>{
                        if (v.form.bitform_type==='hidden'){
                            $('form')
                                    .append(rEl(v.form.bitform_input,
                                        {
                                            id:`${v.form.bitform_input}-${v.bittable_name}`,
                                            name: `${v.bittable_name}`,
                                            type: v.form.bitform_type,
                                            class: 'form-control'
                                        }))
                        }else{
                            $('form')
                                .append(rEl('div',{class:'form-group col-3'})
                                    .append(rEl('label',{text:v.form.bitform_label}))
                                    .append(rEl(v.form.bitform_input,
                                        {
                                            id:`${v.form.bitform_input}-${v.bittable_name}`,
                                            name: `${v.bittable_name}`,
                                            type: v.form.bitform_type,
                                            class: 'form-control',
                                            url:`/crud/select${v.form.bitform_url}`,
                                            placeholder: `${v.form.bitform_label}`
                                        })))
                        }

                    },Render().CallDataTable(param)); Bits.rSelect2()
                })
        });
    };
    let Route = (p) => {
        let $ui = $('#ui-view');
        let rEl = (elem, option) => Render().CallElement(elem, option);
        let formRender = (param,f) => {
            switch (param) {
                case '#bitmenu' :
                    $.get('/bit/bitMenuGet')
                        .done((data)=>{
                            $('.card-body').append(rEl('form', {class: "row"})
                            ).append(rEl('button', {class: "btn btn-primary",text:"Submit", onclick:`save('${Bits.app}bit/menusave')`}));
                            if (data!='') {
                                $.each(data,(k,v)=>{
                                    $('form')
                                        .append(rEl('div', {
                                            class: "form-group col-4"
                                        }).append(rEl('label', {
                                            text:'Menu Name',
                                        }))
                                            .append(rEl('input', {
                                                class: "form-control",
                                                value:v.bittable_name,
                                                disabled: true
                                            })))
                                        .append(rEl('div', {
                                            class: "form-group col-4"
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
                                            class: "form-group col-4"
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
                    break;
                case '#bitform' : Bits.genForm = f; break;
                case '#bittable' :
                    Bits.childForm = f.child;
                    Bits.joinForm = f.join;
                    $.each(f.parent,function (k,v) {
                        $('#parent').append(rEl('fieldset',{
                            class: `form-group ${k !== 0 ? 'col-sm-4' : ''}`
                        }).append(rEl('label',{text: v.label}))
                            .append(rEl(v.input,{
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
                            .append(rEl('fieldset',{
                                class: `form-group ${k !== 0 ? 'col-sm-3' : ''}`
                            }).append(rEl('label',{text: v.label}))
                                .append(rEl(v.input,{
                                    id: v.id,
                                    name: v.name ? v.name : v.id,
                                    type: v.type,
                                    placeholder: v.label,
                                    class: 'form-control',
                                    url: v.url
                                })));
                        if (k=== f.child.length-1){
                            $('#child').append(rEl('div',{
                                id:'child-join',
                                class:'form-group col-sm-12'
                            }).append(rEl('div',{class:'row'})));
                            $(`#${v.id}`).on('select2:select',  (e) => {
                                e.params.data.id === 'foreign' ? appendJoin('#child-join>div') : removejoin('#child-join>div')
                            });
                        }
                    });
                    break;
                default :
                    $ui.prepend(rEl('div', {class: "card"})
                        .append(rEl('div', {class: "card-header bg-primary text-white"})
                            .append(rEl('i', {class: "fa fa-edit"}), 'Form Query')
                            .append(rEl('div', {class: "card-header-actions"})))
                        .append(rEl('div', {class: "card-body"})
                            .append(rEl('form', {class: 'row'})
                                .append(rEl('fieldset',{class:'col-sm-3'})
                                    .append(rEl('select',{
                                        class: 'form-control',
                                        placeholder: 'Choose Mode',
                                        url: `${app}bit/select/query_mode`,
                                        name: 'mode'
                                })))
                                .append(rEl('fieldset',{class:'col-sm-3'}).append(rEl('select',{
                                    class: 'form-control',
                                    placeholder: 'Choose Table',
                                    url: `${app}bit/select/query_table`,
                                    name: 'table'
                                })))
                                .append(rEl('fieldset',{class:'col-sm-3'}).append(rEl('select',{
                                    class: 'form-control',
                                    placeholder: 'Choose Field',
                                    url: `${app}bit/select/query_field`,
                                    name: 'field[]',
                                    multiple:"multiple"
                                })))
                                .append(rEl('fieldset',{class:'col-sm-3'}).append(rEl('select',{
                                    class: 'form-control',
                                    placeholder: 'Group by',
                                    url: `${app}bit/select/query_field`,
                                    name: 'groupby'
                                })))
                                .append(rEl('fieldset',{class:'col-sm-3'}).append(rEl('select',{
                                    class: 'form-control',
                                    placeholder: 'Order By',
                                    url: `${app}bit/select/query_field`,
                                    name: 'orderby'
                                })))
                                .append(rEl('fieldset',{class:'col-sm-3'}).append(rEl('join',{
                                    class: 'form-control',
                                    placeholder: 'Join Table',
                                    url: `${app}bit/select/query_table`
                                }))))
                            .append(rEl('button', {onclick:"execute()", class: 'btn btn-primary',text:'execute'}))

                        ));

                    break;
            }
        };
        $ui.fadeOut().promise()
            .done(function () {
                $ui.children().remove();
                if (p==='#bittable') {
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
                                    .append(rEl('div',{id:'parent',class:'row border-bottom'}))
                                    .append(rEl('div',{id:'child',class:'row border-bottom mt-4'}))
                            ).append(rEl('div', {class: "mt-2 row"})
                                .append(rEl('div',{
                                    class: "col-sm-6 btn-group"
                                }).append(rEl('button',{
                                    id:'add-child',
                                    text:'Add Field',
                                    class: 'btn btn-success',
                                    onclick: "cloneTag('child',0)"
                                })))
                                .append(rEl('div',{
                                    class: "col-sm-6"
                                }).append(rEl('button',{
                                    id:'save',
                                    text:'Save',
                                    class: 'btn btn-primary pull-right px-4',
                                    onclick: `save('${Bits.app}bit/save')`
                                })))
                            )

                        ));
                }
                $ui.append(rEl('div', {class: "card"})
                    .append(rEl('div', {class: "card-header bg-primary text-white"})
                        .append(rEl('i', {class: "fa fa-table"}), 'Data Table')
                        .append(rEl('div', {class: "card-header-actions"})))
                    .append(rEl('div', {class: "card-body"})
                        .append(rEl('table', {id: `${p.substring(1)}-table`,class: 'table table-striped table-bordered'}))))
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
                    formRender(p,f);
                    if (p!=='#bitmenu' && p!=='#bitquery') {
                        Render().CallDataTable(param);
                    }
            })
                .then(() =>
                    Render().CallSelect(),
                    loadMenu());
        });
    };
    let Render = () => {
        let Element = (elem, option) => $("<" + elem + " />", option);
        let Select = () => {
            $('select').each(function(){
                let $t = $(this);
                if (!$t.hasClass('select2-hidden-accessible')){

                    $.get($t.attr('url')).done((data) => {
                        data[0] = {
                            'id': '',"text":$t.attr('placeholder'),"title":$t.attr('placeholder')
                        };

                    }).then((data)=>{
                        if ($t.attr('value') !== undefined){
                            $t.select2({
                                theme:'bootstrap',
                                data: data
                            }).val($t.attr('value')).trigger('change')
                        }else{
                            $t.select2({
                                theme:'bootstrap',
                                data: data
                            })
                        }
                    })
                }

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
                        let check= window.location.hash;
                        let result;
                        switch (check) {
                            case '#bitform' :
                                result = `<button onclick="editForm(${Object.values(data)[0]})" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button>`; break;
                            case '#bittable':
                                result = `<button onclick="dd(${Object.values(data)[0]})" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>`; break;
                            default :
                                result =`<button onclick="editForm(${Object.values(data)[0]},'${check.substring(1)}')" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button>
                                        <button onclick="dd(${Object.values(data)[0]},'${check.substring(1)}')" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button>`;
                                break
                        }
                        return result;
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
        Crud: (p) => {
            Crud(p);
        },
        tClasses: (p) => {
            tClasses(p);
        },
        app,
        childForm,
        joinForm,
        genForm
    }
}();

function cloneTag(set,index){
    let data;
        index = index+1;
    data = set === 'child' ? Bits.childForm : Bits.joinForm;
    let cc = 'col-sm-4';
    $(`#add-${set}`).attr('onclick',`cloneTag('${set}',${index})`);
    $('form').append($('<div />', {
        class: `form-${set}-${index} row mt-4 border-bottom`
    })
        .append($('<div />', {
            class: `col-sm-11 row`
        }))
    );
    $.each(data, function (k,v) {
        if (set === 'child'){
            cc = `${k !== 0 ? 'col-sm-3' : ''}`
        }
        data[k].id = (v.id).replace(/\d/g, index);
        data[k].name = v.name ? (v.name).replace(/\d/g, index) : v.id;
        $(`.form-${set}-${index}>div`)
            .append($('<div />', {
                class: `form-group ${cc}`
            }).append($(`<label />`, {
                    "for": v.id,
                    "text" : v.label,
                }))
                .append($(`<${v.input} />`, {
                    id: v.id,
                    name:  v.name,
                    type: v.type,
                    placeholder: v.label,
                    class: 'form-control',
                    url: v.url
                }))
            )
        $(`#${v.id}`).on('select2:select',  (e) => {
            e.params.data.id === 'foreign' ? appendJoin(`#join-${set}-${index}>div.row`) : removejoin(`#join-${set}-${index}>div.row`)
        });
    });
    Bits.rSelect2();
    $(`.form-${set}-${index}`)
        .append($('<div />', {class:"form-group col-sm-1 text-align-right"})
            .append($('<button />', {
                onclick: `removeParent('${set}',${index})`,
                class: 'btn btn-danger pull-right',
                html: '<i class="fa fa-close"></i>',
            }
            )
            )
        )
        .append($('<div />', {id:`join-${set}-${index}`,class:"form-group col-sm-12"})
            .append($('<div />', {class:"row"}))
        );
}

function appendJoin(p){
    let index = p.replace( /\D+/g, '');
    $.each(Bits.joinForm, function (k,v) {
        if (index!==''){
            Bits.joinForm[k].id = (v.id).replace(/\d/g, index);
        }
        $(p).append($('<div />', {
                    class: `form-group col-sm-4`
                }).append($(`<label />`, {
                    "for": v.id,
                    "text" : v.label,
                })).append($(`<${v.input} />`, {
                        id: v.id,
                        name: v.name ? v.name : v.id,
                        type: v.type,
                        placeholder: v.label,
                        class: 'form-control',
                        url: v.url
                    })))
    });
    Bits.rSelect2();
}

function removejoin(p) {
    $(p).children().remove();
}

function removeParent(set,p){
    $(`.form-${set}-${p}`).remove();
}
function editForm(p,table = null){
    let rEl = (elem, option) => $("<" + elem + " />", option);
    if (window.location.hash!=='#bitform'){
        $.get(`/crud/edit/${table}/${p}`).done((data)=>{
            $.each(data,(k,v)=>{
                $(`#input-${k}`).val(v)
            })
        })
    }else{
        $("#ui-view>div").children().length !== 2 ? $('#ui-view>div').first().remove() : null;
        $('#ui-view').prepend(rEl('div', {class: "card"})
            .append(rEl('div', {id: "cForm", class: "card-body"})
                .append(rEl('form', {})
                ).append(rEl('div', {class:""})
                    .append(rEl('button',{
                        id:'save',
                        text:'Save',
                        class: 'btn btn-primary pull-right',
                        onclick: `save('${Bits.app}bit/save?id=${p}')`
                    })))));
        let $this = $('#cForm>form');
        $.get(`/bit/bitGetDataDetail/${p}`).done((data)=>{
            $this.text(`Table ${data[0].parent.bittable_name} Form`);
            $.each(data,(k,v)=>{
                $this.append(rEl('div',{id:`block-${k}` ,class:'row shadow p-3 mb-5 bg-white rounded'}));
                $.each(Bits.genForm,(kk,vv)=>{
                    $(`#block-${k}`).append(rEl('fieldset',{
                        class: `form-group ${kk !== 0 ? 'col-sm-3' : ''}`
                    }).append(rEl('label',{text:
                        vv.label}))
                        .append(rEl(vv.input,{
                            id: `input-${v.bittable_id}-${vv.id}`,
                            name: `field[${v.bittable_id}][${vv.id}]`,
                            type: vv.type,
                            class: 'form-control',
                            url: vv.url,
                            value: data[k].form[vv.id]
                        })))
                });

            });
            Bits.rSelect2();
        }).then();
    }

}
function crudSave(p){
    $.ajax({
        type: 'POST',
        url: `/crud/post/${p}`,
        data: $('form').serializeArray()
    })
        .done((data)=>{
            Bits.Crud(window.location.hash);
        });
}
