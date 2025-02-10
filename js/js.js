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
                            `<label>
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
        let estado = "idea";  // Establecer el estado de la tarea como "idea" por defecto
    
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
                colaboradores: colaboradores,  // Enviar los colaboradores
                creador: creador,  // Enviar el ID del creador
                estado: estado  // Enviar el estado de la tarea
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
                let listaTareas = $("#idea"); // Aquí puedes modificar según la columna en la que quieras mostrar las tareas
    
                // Vaciar el contenedor antes de agregar las nuevas tareas
                listaTareas.empty();
    
                // Recorrer las tareas y agregar al contenedor
                tareas.forEach(tarea => {
                    listaTareas.append(`    
                        <div class="tarea" data-id="${tarea._id}" data-titulo="${tarea.titulo}" data-estado="${tarea.estado}" data-descripcion="${tarea.descripcion}" data-colaboradores="${tarea.colaboradores.join(',')}">
                            <h3 class="titulo-tarea">${tarea.titulo}</h3>
                            <p class="descripcion-tarea">${tarea.descripcion}</p>
                        </div>
                    `);
                });
    
                // Evento para mostrar los detalles de la tarea en un modal
                $(".tarea").on("click", function () {
                    let tareaId = $(this).data("id");
                    let titulo = $(this).data("titulo");
                    let descripcion = $(this).data("descripcion");
                    let colaboradores = $(this).data("colaboradores");
                    let estado = $(this).data("estado");  // Obtener el estado
    
                    // Llenar el modal con los datos de la tarea
                    $("#detalleTareaId").val(tareaId);
                    $("#detalleTitulo").text(titulo);
                    $("#detalleDescripcion").text(descripcion);
                    $("#detalleColaboradores").text(colaboradores);
                    $("#detalleEstado").text(estado);  // Mostrar el estado en el modal
    
                    // Mostrar el modal
                    $("#detalleTareaModal").modal("show");
                });
                $("#eliminarTarea").on("click", function () {
                    let tareaId = $("#detalleTareaId").val();  // Obtener el ID de la tarea desde el campo oculto en el modal
                    
                    
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


            } else {
                alert("No se pudieron obtener las tareas.");
            }
        },
        error: function () {
            alert("Error al obtener las tareas.");
        }
    });

    

});