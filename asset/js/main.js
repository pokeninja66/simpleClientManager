
// jquery stuff
$(document).ready(function () {
    // for the tabs
    $('.tabs').tabs();
    // for the select
    $('select').formSelect();// kak go mrazq tova tupo inicializirane navsqkude

    //------------------------ client actions
    //setOptionsByAJAX("choce-a-prod", "loadClientAddProdOption");
    $("#choce-a-prod").load("asset/php/processing.php?action=selectAddClientProduct");
    // add a client
    $('.addClient').click(function (e) {

        e.preventDefault();
        sendAJAXform('addclientForm', "addClient");
    })
    // update client
    $("#loadClients").load("asset/php/processing.php?action=loadClients");
    $("#updateClientProd").load("asset/php/processing.php?action=selectAddClientProduct");
    $('.updateClient').click(function () {
        updateAJAXform('updateClientForm', 'updateClient', 'client');
    });

    //------------------------ product actions
    // add a product
    $('.addProd').click(function (e) {

        //let data = $(this).find("input").closest(".js-addProduct").serializeArray();
        //console.log(data);
        e.preventDefault();
        sendAJAXform('addProductForm', "addProuct");
    })
    // update a product
    $("#loadProducts").load("asset/php/processing.php?action=loadProducts");
    $('#currentProds').change(function () {
        console.log($(this).val());
    });
    $('.updateProd').click(function () {
        updateAJAXform('updateProductForm', 'updateProd', 'product');
    });

    //------------------------ Data actions
    $("#ClientsTable").load("asset/php/processing.php?action=ClientsTable");
    $('#ProductsTable').load('asset/php/processing.php?action=ProductsTable');

    $("#priceSearcher").blur(function () {
        $('.search-table').load('asset/php/processing.php?action=searchByPrice&price=' + $(this).val());
    });

});

// some functions

function sendAJAXform(elem, action) {

    // serialize the inputs
    let FormData = $("#" + elem).closest('div').find("input[type=text],input[type=number],input[type=tel],input[type=email],select, textarea").serializeArray();

    //let FormData = $("#" + elem + " :input,:hidden").serializeArray();
    //console.log(FormData);

    $.ajax({
        url: './asset/php/processing.php?action=' + action,
        data: FormData,
        method: 'Post',
        error: function (e) {
            console.log(e.responseText);
            alert(e.responseText);
        }
        ,
        success: function (data) {

            console.log(data);
            if (data.msg !== "") {
                M.toast({ html: data.msg });

            }
            if (data.status) {
                $("#" + elem).closest('div').find("input[type=text],input[type=number],input[type=tel],input[type=email],select, textarea").val("");

            }

        }

    });
}

function updateAJAXform(elem, action, type) {

    // serialize the inputs
    let FormData = $("#" + elem).closest('div').find("input[type=text],input[type=number],input[type=tel],input[type=email],input[type=hidden], select").serializeArray();

    $.ajax({
        url: './asset/php/processing.php?action=' + action,
        data: FormData,
        method: 'Post',
        error: function (e) {
            console.log(e.responseText);
            alert(e.responseText);
        }
        ,
        success: function (data) {

            console.log(data);
            if (data.msg !== "") {
                M.toast({ html: data.msg });
            }
            if (data.status) {
                $("#" + elem).closest('div').find("input[type=text],input[type=number],input[type=tel],input[type=email],select, textarea").val("");
                if (type == 'product') {
                    $("#loadProducts").load("asset/php/processing.php?action=loadProducts");
                } else {
                    $("#loadClients").load("asset/php/processing.php?action=loadClients");
                }
                $('select').formSelect();
            }

        }

    });
}

function updateBy(id, type) {
    let action;
    if (type == 'product') {
        action = 'getProdData';
    } else {
        action = 'getClientData';
    }

    $.ajax({
        url: './asset/php/processing.php',
        data: {
            action: action,
            update_id: id
        },
        method: 'Post',
        error: function (e) {
            console.log(e.responseText);
            alert(e.responseText);
        }
        ,
        success: function (data) {
            if (data.msg !== "") {
                M.toast({ html: data.msg });
            }
            if (type == 'product') {
                //console.log(data);
                let current = data.prodData;
                //console.log(current.id);
                $('#updateProdID').val(current.id);
                $('#updateProdName').val(current.name);
                $('#updateProdPprice').val(current.price);
                $('#updateProdQuantity').val(current.quantity);

            } else {

                //console.log(data);
                let current = data.clientData;
                //console.log(current);
                $('#updateClientID').val(current.id);
                $('#updateClientName').val(current.name);
                $('#updateClientPhone').val(current.phone);
                $('#updateClientEmail').val(current.email);

                $("#updateClientType option").each(function () {
                    if ($(this).val() == current.type) {
                        $(this).attr("selected", "selected");
                    }
                });
                $("#updateClientProd select option").each(function () {
                    if ($(this).val() == current.product_id) {
                        $(this).attr("selected", "selected");
                    }
                });
                $('select').formSelect();
            }

        }

    });
}

function deleteAJAX(id, action, table) {
    $.ajax({
        url: './asset/php/processing.php?action=' + action,
        data: {
            action: action,
            delete_id: id,
            table: table
        },
        method: 'Post',
        error: function (e) {
            console.log(e.responseText);
            alert(e.responseText);
        }
        ,
        success: function (data) {

            console.log(data);
            if (data.msg !== "") {
                M.toast({ html: data.msg });

            }

        }

    });
}

/* fuck this
function setOptionsByAJAX(elem, action) {
    $.ajax({
        url: './asset/php/processing.php?action=' + action,
        data: FormData,
        method: 'Post',
        error: function () {
            alert("An error has occurred");
        }
        ,
        success: function (data) {

            console.log(data);

            if (data.msg !== "") {
                M.toast({ html: data.msg });
            }

            if (data.status) {
                /*
               $.each(data.options, function () {
                   $(elem).append("<option value='" + this.id + "'>" + this.name + "</option>");
                   $.each(this, function (key, value) {
                       $(elem).append("<option value='" + value + "'>" + value + "</option>");
                       alert(key + ' ' + value);
                   });
               });

               var OptArray = data.options;
               for (i in OptArray) {
                   $(elem).append("<option value='" + OptArray[i].id + "'>" + OptArray[i].name + "</option>");
                   console.log(OptArray);
               }
               for (i = 1; i < OptArray.length; ++i) {
                   $(elem).append("<option value='" + data.options[i].id + "'>" + data.options[i].name + "</option>");
                   console.log($(elem).append("<option value='" + data.options[i].id + "'>" + data.options[i].name + "</option>"));
               }

                //var OptArray = data.options;
                for (i = 0; i < data.options.length; ++i) {
                    console.log(elem, data.options[i]);
                    $("#" + elem).append("<option value='" + data.options[i].id + "'>" + data.options[i].name + "</option>");
                }
                //$("#" + elem).formSelect();
                var instance = M.FormSelect.getInstance(elem);
                console.log(instance.getSelectedValues());
            }

        }

    });
}
*/