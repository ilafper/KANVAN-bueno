$(document).ready(function () {


    /*OBTENER LOS COLABOLADORES */
    $(".nueva-tarea").click(function () {

        // Obtener usuarios disponibles para colaborar, excluyendo al creador
        $.ajax({
            url: "../php/obtenerUsuarios.php", // URL del archivo PHP para obtener los usuarios
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let lista = $("#colaboradores");
                    lista.empty(); // Limpiar la lista de colaboradores

                    // Recorrer los usuarios y agregar solo los que no son el creador
                    response.usuarios.forEach(usuario => {
                        lista.append(
                            `<label class="colabo">
                                <input type="checkbox" value="${usuario.id}" class="colaborador-nuevo">
                                ${usuario.nombre}
                            </label><br>`
                        );
                    });
                } else {
                    alert("Error al obtener los usuarios.");
                }
            },
            error: function () {
                alert("Error al obtener la lista de usuarios.");
            }
        });
        // Mostrar el modal para crear nueva tarea
        $("#nuevaTareaModal").modal("show");
    });


    // Crear una nueva tarea
    $('#guardarTarea').click(function () {
        let titulo = $('#titulo').val();
        let descripcion = $('#descripcion').val();
        let colaboradores = [];
        let estado = "idea";  //IDEA POR DEFECTO

        // Obtener los colaboradores seleccionados
        $('#colaboradores input:checked').each(function () {
            colaboradores.push($(this).val());
        });

        // Obtener el ID del creador (si está en el front-end)
        let creador = $('#usuario_id').val();  // O lo que sea necesario para obtener el ID del creador

        // Enviar los datos al servidor
        $.ajax({
            url: '../php/nuevaTarea.php',
            method: 'POST',
            data: {
                titulo: titulo,
                descripcion: descripcion,
                colaboradores: colaboradores,
                creador: creador,
                estado: estado
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    alert(res.message);
                    $('#tareaModal').modal('hide');  // Cerrar el modal
                    $('#formTarea')[0].reset();  // Limpiar el formulario
                } else {
                    alert("Error: " + res.error);
                }
            },
            error: function () {
                alert("Error al guardar la tarea.");
            }
        });
    });


    /*MOSTRAR LAS TAREAS*/
    $.ajax({
        url: "../php/obtenerTareas.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                const tareas = response.tareas;

                // Asegurarnos de limpiar todas las columnas antes de agregar nuevas tareas
                $("#idea, #todo, #doing, #done").empty();

                tareas.forEach(tarea => {
                    // Determinar la columna correcta según el estado de la tarea
                    let listaTareas = $(`#${tarea.estado.toLowerCase()}`);

                    listaTareas.append(`
                    <div class="tarea" data-id="${tarea.id}" data-creador="${tarea.creador}" data-titulo="${tarea.titulo}" data-estado="${tarea.estado}"  data-descripcion="${tarea.descripcion}" data-colaboradores="${tarea.colaboradores.join(',')}">
                        <section class="infor">
                            <h3 class="titulo-tarea">${tarea.titulo}</h3>
                        </section>
                        
                        <section class="botones">
                            <i class='bx bx-chevron-left'></i>
                            <i class='bx bx-chevron-right'></i>
                        </section>
                    </div>
                `);
                });



                // Evento para mostrar los detalles de la tarea en un modal
                $(".tarea").on("click", function () {
                    let tareaId = $(this).data("id");
                    let creator = $(this).data("creador");
                    let titulo = $(this).data("titulo");
                    let descripcion = $(this).data("descripcion");
                    let colaboradores = $(this).data("colaboradores");
                    let estado = $(this).data("estado");

                    // Llenar el modal con los datos de la tarea
                    $("#idTarea").val(tareaId);
                    $("#detalleCreador").text(creator);
                    $("#detalleTitulo").text(titulo);
                    $("#detalleDescripcion").text(descripcion);
                    $("#detalleColaboradores").text(colaboradores);
                    $("#detalleEstado").text(estado);
                    // Mostrar el modal
                    $("#detalleTareaModal").modal("show");

                });




                $("#eliminarTarea").on("click", function () {
                    let tareaId = $("#idTarea").val();  // Obtener el ID de la tarea desde el campo oculto en el modal

                    //console.log(tareaId);

                    if (tareaId) {
                        if (confirm("¿Estás seguro de que deseas eliminar esta tarea?")) {
                            $.ajax({
                                url: "../php/eliminarTarea.php",
                                type: "POST",
                                data: {
                                    tareaId: tareaId  // ID de la tarea a eliminar
                                },
                                success: function (response) {
                                    let res = JSON.parse(response);
                                    if (res.success) {
                                        alert(res.message);
                                        // Eliminar la tarea de la interfaz
                                        $(`.tarea[data-id=${tareaId}]`).remove();
                                        // Cerrar el modal
                                        $("#detalleTareaModal").modal("hide");
                                    } else {
                                        alert("Error: " + res.error);
                                    }
                                },
                                error: function () {
                                    alert("Error al eliminar la tarea.");
                                }
                            });
                        }
                    } else {
                        alert("No se ha encontrado el ID de la tarea.");
                    }
                });


                $("#modificarTarea").on('click', function () {
                    // Obtener los datos de la tarea desde el modal de detalles
                    const tareaId = $('#idTarea').val();
                    const estado = $('#detalleEstado').text();
                    const creador = $('#detalleCreador').text();
                    const titulo = $('#detalleTitulo').text();
                    const descripcion = $('#detalleDescripcion').text();
                    const colaboradores = $('#detalleColaboradores').text().split(',').map(c => c.trim());
                    //console.log(tareaId, estado, creador, titulo, descripcion, colaboradores);

                    // Establecer valores en los campos del formulario
                    $('#creadorTarea').val(creador);
                    $('#idTareaModificar').val(tareaId);
                    $('#tituloModificar').val(titulo);
                    $('#descripcionModificar').val(descripcion);
                    $('#estadoTarea').val(estado);

                    $('#colaboradoresModificar').empty();
                    // Obtener los usuarios disponibles para colaborar
                    $.ajax({
                        url: "../php/obtenerUsuarios.php", // URL para obtener los usuarios disponibles
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                // Recorrer los usuarios y agregar solo los que no son el creador
                                response.usuarios.forEach(usuario => {
                                    // Comprobar si el usuario está en los colaboradores de la tarea (basado en los IDs)
                                    const checked = colaboradores.includes(usuario.nombre) ? 'checked' : '';

                                    // Agregar el checkbox correspondiente con el valor de 'checked' si está asignado
                                    $('#colaboradoresModificar').append(
                                        `<label>
                                            <input type="checkbox" value="${usuario.id}" data-nombre="${usuario.nombre}" class="colaborador-nuevo" ${checked}>
                                            ${usuario.nombre}
                                        </label><br>`
                                    );
                                });
                            } else {
                                alert("Error al obtener los usuarios.");
                            }
                        },
                        error: function () {
                            alert("Error al obtener la lista de usuarios.");
                        }
                    });

                    // Mostrar el modal de modificación
                    $('#tareaModalModi').modal('show');
                });


                $("#guardarTareaModificar").on('click', function () {
                    const tareaIdM = $('#idTareaModificar').val();
                    const estadoM = $('#estadoTarea').val();
                    const tituloM = $('#tituloModificar').val();
                    const descripcionM = $('#descripcionModificar').val();
                    const creadorM = $('#creadorTarea').val();
                    const colaboradoresSeleccionadosM = [];
                    $("#colaboradoresModificar input:checked").each(function () {
                        colaboradoresSeleccionadosM.push($(this).data("nombre"));
                    });
                    //console.log(tareaIdM, estadoM, creadorM, tituloM, descripcionM, colaboradoresSeleccionadosM);
                    // Enviar los datos modificados al servidor
                    $.ajax({
                        url: "../php/modificar.php", // Archivo PHP para guardar los cambios
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify({
                            id: tareaIdM,
                            titulo: tituloM,
                            descripcion: descripcionM,
                            estado: estadoM,
                            creador: creadorM,
                            colaboradores: colaboradoresSeleccionadosM
                        }),
                        success: function (response) {
                            let res = JSON.parse(response);
                            if (res.success) {
                                
                                // Recargar o actualizar la lista de tareas
                                location.reload();
                            } else {
                                alert("Error al modificar la tarea.");
                            }
                        },
                        error: function () {
                            alert("Error al guardar los cambios.");
                        }
                    });
                });


            } else {
                alert("No se pudieron obtener las tareas.");
            }
        },
        error: function () {
            alert("Error al obtener las tareas.");
        }
    });


    /* MOVER LA TARJETA DE COLUMNA CON LAS FLECHAS */

    /* FLECHA DERECHA */
    $(document).on("click", ".bx-chevron-right", function (e) {
        e.stopPropagation(); // Evita que el evento se propague al contenedor de la tarea
        e.preventDefault();  // Evita que se realice cualquier acción predeterminada

        let tarea = $(this).closest(".tarea");
        let estadoActu = tarea.data("estado");
        let tareaId = tarea.data("id");

        let nuevoEstado = "";
        /* idea → todo → doing → done */
        if (estadoActu === "idea") {
            nuevoEstado = "todo";
        } else if (estadoActu === "todo") {
            nuevoEstado = "doing";
        } else if (estadoActu === "doing") {
            nuevoEstado = "done";
        } else {
            return;
        }


        $.ajax({
            url: "../php/actualizarEstado.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                id: tareaId,
                estado: nuevoEstado,
            }),
            success: function (response) {
                console.log(response);
                let res = JSON.parse(response);
                if (res.success) {
                    location.reload();
                } else {
                    
                }
            },
            error: function () {
               
            }
        });

        return false;
    });

    /* FLECHA IZQUIERDA */
    $(document).on("click", ".bx-chevron-left", function (e) {
        e.stopPropagation(); // Evita que el evento se propague al contenedor de la tarea
        e.preventDefault();  // Evita que se realice cualquier acción predeterminada

        let tarea = $(this).closest(".tarea");
        let estadoActu = tarea.data("estado");
        let tareaId = tarea.data("id");

        let nuevoEstado = "";

        /* idea → todo → doing → done */
        if (estadoActu === "done") {
            nuevoEstado = "doing";
        } else if (estadoActu === "doing") {
            nuevoEstado = "todo";
        } else {
            return;
        }

        // Cambiar estado en la interfaz (enviar actualización al servidor)
        $.ajax({
            url: "../php/actualizarEstado.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                id: tareaId,
                estado: nuevoEstado,
            }),
            success: function (response) {
                console.log(response);
                let res = JSON.parse(response);
                if (res.success) {
                    location.reload(); // Recargar o actualizar la lista de tareas
                } else {
                   
                }
            },
            error: function () {
            
            }
        });

        return false;
    });



});